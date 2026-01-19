<?php

namespace App\Http\Controllers;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\State;
use App\Models\Location\Street;
use App\Models\Location\Zone;
use App\Models\Property\Property;
use App\Models\Reference\Complex;
use App\Models\Reference\Developer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Поиск улиц для autocomplete
     * GET /location/search?q=поисковый_запрос&state_id=ID_региона
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $stateId = $request->input('state_id');
        $limit = $request->input('limit', 15);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Минимум 2 символа для поиска',
                'results' => [],
            ]);
        }

        $streetsQuery = Street::with(['city', 'district', 'zone'])
            ->active()
            ->search($query);

        // Фильтрация по региону через город
        if ($stateId) {
            $streetsQuery->whereHas('city', function ($q) use ($stateId) {
                $q->where('state_id', $stateId);
            });
        }

        $streets = $streetsQuery->limit($limit)->get();

        $results = $streets->map(function ($street) {
            return [
                'id' => $street->id,
                'name' => $street->name,
                'city_id' => $street->city_id,
                'city_name' => $street->city?->name,
                'district_id' => $street->district_id,
                'district_name' => $street->district?->name,
                'zone_id' => $street->zone_id,
                'zone_name' => $street->zone?->name,
                'full_address' => $street->full_address,
                'short_address' => $street->short_address,
            ];
        });

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => $results->count(),
        ]);
    }

    /**
     * Получение данных улицы по ID
     * GET /location/street/{id}
     */
    public function show(int $id): JsonResponse
    {
        $street = Street::with(['city', 'district', 'zone'])->find($id);

        if (!$street) {
            return response()->json([
                'success' => false,
                'message' => 'Улица не найдена',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'street' => [
                'id' => $street->id,
                'name' => $street->name,
                'city_id' => $street->city_id,
                'city_name' => $street->city?->name,
                'district_id' => $street->district_id,
                'district_name' => $street->district?->name,
                'zone_id' => $street->zone_id,
                'zone_name' => $street->zone?->name,
                'full_address' => $street->full_address,
                'short_address' => $street->short_address,
            ],
        ]);
    }

    /**
     * Поиск регионов для autocomplete
     * GET /location/states/search?q=поисковый_запрос
     */
    public function searchStates(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 15);

        $statesQuery = State::with('country')->active();

        if (strlen($query) >= 1) {
            $statesQuery->where('name', 'like', "%{$query}%");
        }

        $states = $statesQuery->orderBy('name')->limit($limit)->get();

        $results = $states->map(function ($state) {
            return [
                'id' => $state->id,
                'name' => $state->name,
                'code' => $state->code,
                'country_id' => $state->country_id,
                'country_name' => $state->country?->name,
                'full_name' => $state->name . ', ' . ($state->country?->name ?? ''),
            ];
        });

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => $results->count(),
        ]);
    }

    /**
     * Получение региона по умолчанию (Одесский регион)
     * GET /location/states/default
     */
    public function getDefaultState(): JsonResponse
    {
        // Ищем Одесский регион
        $state = State::with('country')
            ->active()
            ->where('name', 'like', '%Одес%')
            ->first();

        if (!$state) {
            // Если не найден - берем первый активный
            $state = State::with('country')->active()->first();
        }

        if (!$state) {
            return response()->json([
                'success' => false,
                'message' => 'Регион не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'state' => [
                'id' => $state->id,
                'name' => $state->name,
                'code' => $state->code,
                'country_id' => $state->country_id,
                'country_name' => $state->country?->name,
                'full_name' => $state->name . ', ' . ($state->country?->name ?? ''),
            ],
        ]);
    }

    /**
     * Получение региона по ID
     * GET /location/states/{id}
     */
    public function showState(int $id): JsonResponse
    {
        $state = State::with('country')->find($id);

        if (!$state) {
            return response()->json([
                'success' => false,
                'message' => 'Регион не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'state' => [
                'id' => $state->id,
                'name' => $state->name,
                'code' => $state->code,
                'country_id' => $state->country_id,
                'country_name' => $state->country?->name,
                'full_name' => $state->name . ', ' . ($state->country?->name ?? ''),
            ],
        ]);
    }

    /**
     * Получение данных для фильтра локаций
     * GET /location/filter-data
     */
    public function getFilterData(Request $request): JsonResponse
    {
        $locationType = $request->input('location_type'); // country, region, city
        $locationId = $request->input('location_id');
        $detailType = $request->input('detail_type'); // district, street, landmark, complex, block, developer
        $cityId = $request->input('city_id');
        $search = $request->input('search', '');

        $data = [];

        // Режим Location (Страна, Область, Город)
        if ($locationType === null || $locationType === 'country') {
            // Получаем страны
            $countries = Country::active()
                ->when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
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

            $data['countries'] = $countries;
        }

        if ($locationType === 'country' && $locationId) {
            // Получаем области для страны
            $regions = State::where('country_id', $locationId)
                ->active()
                ->when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->withCount([
                    'cities as count' => function ($q) {
                        $q->active();
                    }
                ])
                ->get()
                ->map(function ($region) use ($locationId) {
                    return [
                        'id' => $region->id,
                        'name' => $region->name,
                        'countryId' => $locationId,
                        'count' => $region->count ?? 0,
                    ];
                });

            $data['regions'] = $regions;
        }

        if ($locationType === 'region' && $locationId) {
            // Получаем города для области
            $cities = City::where('state_id', $locationId)
                ->active()
                ->when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->with('state')
                ->get()
                ->map(function ($city) use ($locationId) {
                    return [
                        'id' => $city->id,
                        'name' => $city->name,
                        'regionId' => $locationId,
                        'region' => $city->state->name ?? '',
                        'count' => Property::where('city_id', $city->id)->count(),
                    ];
                });

            $data['cities'] = $cities;
        }

        // Режим Detail (Район, Улица, Зона, Комплекс, Блок, Девелопер)
        if ($cityId && ($detailType === 'district' || $detailType === null)) {
            $districts = District::where('city_id', $cityId)
                ->when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->with('city')
                ->limit(100)
                ->get()
                ->map(function ($district) use ($cityId) {
                    return [
                        'id' => $district->id,
                        'name' => $district->name,
                        'cityId' => $cityId,
                        'city' => $district->city->name ?? '',
                        'count' => Property::where('district_id', $district->id)->count(),
                    ];
                });

            $data['districts'] = $districts;
        }

        if ($cityId && ($detailType === 'street' || $detailType === null)) {
            $streets = Street::where('city_id', $cityId)
                ->active()
                ->when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->with('city')
                ->limit(100)
                ->get()
                ->map(function ($street) use ($cityId) {
                    return [
                        'id' => $street->id,
                        'name' => $street->name,
                        'cityId' => $cityId,
                        'city' => $street->city->name ?? '',
                        'count' => Property::where('street_id', $street->id)->count(),
                    ];
                });

            $data['streets'] = $streets;
        }

        if ($cityId && ($detailType === 'landmark' || $detailType === null)) {
            $landmarks = Zone::where('city_id', $cityId)
                ->when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->with('city')
                ->limit(100)
                ->get()
                ->map(function ($zone) use ($cityId) {
                    return [
                        'id' => $zone->id,
                        'name' => $zone->name,
                        'cityId' => $cityId,
                        'city' => $zone->city->name ?? '',
                        'count' => Property::where('zone_id', $zone->id)->count(),
                    ];
                });

            $data['landmarks'] = $landmarks;
        }

        if (($cityId || ($locationType === 'region' && $locationId)) && ($detailType === 'complex' || $detailType === null)) {
            $complexesQuery = Complex::active()
                ->when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->with('city');

            if ($cityId) {
                $complexesQuery->whereHas('city', function ($q) use ($cityId) {
                    $q->where('id', $cityId);
                });
            } else {
                $complexesQuery->whereHas('city', function ($q) use ($locationId) {
                    $q->where('state_id', $locationId);
                });
            }

            $complexes = $complexesQuery->limit(100)
                ->get()
                ->map(function ($complex) use ($cityId) {
                    return [
                        'id' => $complex->id,
                        'name' => $complex->name,
                        'cityId' => $cityId ?? $complex->city_id,
                        'city' => $complex->city->name ?? '',
                        'count' => Property::where('complex_id', $complex->id)->count(),
                    ];
                });

            $data['complexes'] = $complexes;
        }



        if (($cityId || ($locationType === 'region' && $locationId)) && ($detailType === 'developer' || $detailType === null)) {
            $developersQuery = Developer::active()
                ->when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });

            if ($cityId) {
                $developersQuery->whereHas('complexes.city', function ($q) use ($cityId) {
                    $q->where('id', $cityId);
                });
            } else {
                $developersQuery->whereHas('complexes.city', function ($q) use ($locationId) {
                    $q->where('state_id', $locationId);
                });
            }

            $developers = $developersQuery->limit(100)
                ->get()
                ->map(function ($developer) use ($cityId) {
                    return [
                        'id' => $developer->id,
                        'name' => $developer->name,
                        'cityId' => $cityId,
                        'city' => '', // Девелопер может работать в разных городах
                        'count' => Property::whereHas('complex.developer', function ($q) use ($developer) {
                            $q->where('id', $developer->id);
                        })->count(),
                    ];
                });

            $data['developers'] = $developers;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Универсальный поиск локации (страна/область/город)
     * GET /location/search-all?q=поисковый_запрос
     */
    public function searchAll(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 20);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Минимум 2 символа для поиска',
                'results' => [],
            ]);
        }

        $results = collect();

        // Поиск по странам
        $countries = Country::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($country) {
                return [
                    'id' => $country->id,
                    'type' => 'country',
                    'name' => $country->name,
                    'full_name' => $country->name,
                    'parent' => null,
                ];
            });
        $results = $results->merge($countries);

        // Поиск по областям
        $states = State::with('country')
            ->active()
            ->where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($state) {
                return [
                    'id' => $state->id,
                    'type' => 'state',
                    'name' => $state->name,
                    'full_name' => $state->name . ', ' . ($state->country?->name ?? ''),
                    'parent' => $state->country?->name,
                    'country_id' => $state->country_id,
                ];
            });
        $results = $results->merge($states);

        // Поиск по городам
        $cities = City::with(['state', 'state.country'])
            ->active()
            ->where('name', 'like', "%{$query}%")
            ->limit(15)
            ->get()
            ->map(function ($city) {
                $parent = $city->state?->name;
                if ($city->state?->country) {
                    $parent .= ', ' . $city->state->country->name;
                }
                return [
                    'id' => $city->id,
                    'type' => 'city',
                    'name' => $city->name,
                    'full_name' => $city->name . ', ' . $parent,
                    'parent' => $parent,
                    'state_id' => $city->state_id,
                    'country_id' => $city->state?->country_id,
                ];
            });
        $results = $results->merge($cities);

        // Сортируем и ограничиваем
        $results = $results->take($limit)->values();

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => $results->count(),
        ]);
    }
}
