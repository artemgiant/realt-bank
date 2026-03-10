<?php

namespace App\Http\Controllers\Location\Queries;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\State;
use App\Models\Location\Street;
use App\Models\Location\Zone;
use App\Models\Property\Property;
use App\Models\Reference\Complex;
use App\Models\Reference\Developer;
use Illuminate\Http\Request;

/**
 * Построение данных для фильтра локаций.
 *
 * Возвращает списки стран, регионов, городов, районов, улиц,
 * ориентиров, комплексов и девелоперов на основе параметров запроса.
 * Каждая секция — отдельный приватный метод.
 */
class LocationFilterQuery
{
    /** @var string|null Тип локации (country, region, city) */
    private ?string $locationType;

    /** @var string|null ID выбранной локации */
    private ?string $locationId;

    /** @var string|null Тип детализации (district, street, landmark, complex, block, developer) */
    private ?string $detailType;

    /** @var string|null ID города */
    private ?string $cityId;

    /** @var string Поисковый запрос */
    private string $search;

    /**
     * Выполнить построение данных фильтра на основе параметров запроса.
     *
     * @param Request $request HTTP-запрос с параметрами фильтра
     * @return array Массив данных для фильтра
     */
    public function execute(Request $request): array
    {
        $this->locationType = $request->input('location_type');
        $this->locationId = $request->input('location_id');
        $this->detailType = $request->input('detail_type');
        $this->cityId = $request->input('city_id');
        $this->search = $request->input('search', '');

        $data = [];

        // Режим Location (Страна, Область, Город)
        if ($this->locationType === null || $this->locationType === 'country') {
            $data['countries'] = $this->countries();
        }

        if ($this->locationType === 'country' && $this->locationId) {
            $data['regions'] = $this->regions();
        }

        if ($this->locationType === 'region' && $this->locationId) {
            $data['cities'] = $this->cities();
        }

        // Режим Detail (Район, Улица, Зона, Комплекс, Блок, Девелопер)
        if ($this->cityId && ($this->detailType === 'district' || $this->detailType === null)) {
            $data['districts'] = $this->districts();
        }

        if ($this->cityId && ($this->detailType === 'street' || $this->detailType === null)) {
            $data['streets'] = $this->streets();
        }

        if ($this->cityId && ($this->detailType === 'landmark' || $this->detailType === null)) {
            $data['landmarks'] = $this->landmarks();
        }

        if (($this->cityId || ($this->locationType === 'region' && $this->locationId)) && ($this->detailType === 'complex' || $this->detailType === null)) {
            $data['complexes'] = $this->complexes();
        }

        if (($this->cityId || ($this->locationType === 'region' && $this->locationId)) && ($this->detailType === 'developer' || $this->detailType === null)) {
            $data['developers'] = $this->developers();
        }

        return $data;
    }

    /**
     * Получить список стран с количеством регионов, имеющих активные города.
     */
    private function countries(): \Illuminate\Support\Collection
    {
        return Country::active()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->withCount([
                'states as count' => function ($q) {
                    $q->whereHas('cities', function ($cq) {
                        $cq->active();
                    });
                }
            ])
            ->get()
            ->map(function ($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                    'count' => $country->count ?? 0,
                ];
            });
    }

    /**
     * Получить список регионов для выбранной страны с количеством активных городов.
     */
    private function regions(): \Illuminate\Support\Collection
    {
        return State::where('country_id', $this->locationId)
            ->active()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->withCount([
                'cities as count' => function ($q) {
                    $q->active();
                }
            ])
            ->get()
            ->map(function ($region) {
                return [
                    'id' => $region->id,
                    'name' => $region->name,
                    'countryId' => $this->locationId,
                    'count' => $region->count ?? 0,
                ];
            });
    }

    /**
     * Получить список городов для выбранного региона с количеством объектов.
     */
    private function cities(): \Illuminate\Support\Collection
    {
        return City::where('state_id', $this->locationId)
            ->active()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->with('state')
            ->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'regionId' => $this->locationId,
                    'region' => $city->state->name ?? '',
                    'count' => Property::where('city_id', $city->id)->count(),
                ];
            });
    }

    /**
     * Получить список районов для выбранного города с количеством объектов.
     */
    private function districts(): \Illuminate\Support\Collection
    {
        return District::where('city_id', $this->cityId)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->with('city')
            ->limit(100)
            ->get()
            ->map(function ($district) {
                return [
                    'id' => $district->id,
                    'name' => $district->name,
                    'cityId' => $this->cityId,
                    'city' => $district->city->name ?? '',
                    'count' => Property::where('district_id', $district->id)->count(),
                ];
            });
    }

    /**
     * Получить список улиц для выбранного города с количеством объектов.
     */
    private function streets(): \Illuminate\Support\Collection
    {
        return Street::where('city_id', $this->cityId)
            ->active()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->with('city')
            ->limit(100)
            ->get()
            ->map(function ($street) {
                return [
                    'id' => $street->id,
                    'name' => $street->name,
                    'cityId' => $this->cityId,
                    'city' => $street->city->name ?? '',
                    'count' => Property::where('street_id', $street->id)->count(),
                ];
            });
    }

    /**
     * Получить список ориентиров (зон) для выбранного города с количеством объектов.
     */
    private function landmarks(): \Illuminate\Support\Collection
    {
        return Zone::where('city_id', $this->cityId)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->with('city')
            ->limit(100)
            ->get()
            ->map(function ($zone) {
                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'cityId' => $this->cityId,
                    'city' => $zone->city->name ?? '',
                    'count' => Property::where('zone_id', $zone->id)->count(),
                ];
            });
    }

    /**
     * Получить список жилых комплексов для города или региона с количеством объектов.
     */
    private function complexes(): \Illuminate\Support\Collection
    {
        $complexesQuery = Complex::active()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->with('city');

        if ($this->cityId) {
            $complexesQuery->whereHas('city', function ($q) {
                $q->where('id', $this->cityId);
            });
        } else {
            $complexesQuery->whereHas('city', function ($q) {
                $q->where('state_id', $this->locationId);
            });
        }

        return $complexesQuery->limit(100)
            ->get()
            ->map(function ($complex) {
                return [
                    'id' => $complex->id,
                    'name' => $complex->name,
                    'cityId' => $this->cityId ?? $complex->city_id,
                    'city' => $complex->city->name ?? '',
                    'count' => Property::where('complex_id', $complex->id)->count(),
                ];
            });
    }

    /**
     * Получить список девелоперов для города или региона с количеством объектов.
     */
    private function developers(): \Illuminate\Support\Collection
    {
        $developersQuery = Developer::active()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });

        if ($this->cityId) {
            $developersQuery->whereHas('complexes.city', function ($q) {
                $q->where('id', $this->cityId);
            });
        } else {
            $developersQuery->whereHas('complexes.city', function ($q) {
                $q->where('state_id', $this->locationId);
            });
        }

        return $developersQuery->limit(100)
            ->get()
            ->map(function ($developer) {
                return [
                    'id' => $developer->id,
                    'name' => $developer->name,
                    'cityId' => $this->cityId,
                    'city' => '', // Девелопер может работать в разных городах
                    'count' => Property::whereHas('complex.developer', function ($q) use ($developer) {
                        $q->where('id', $developer->id);
                    })->count(),
                ];
            });
    }
}
