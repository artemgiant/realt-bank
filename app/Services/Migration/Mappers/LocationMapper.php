<?php

namespace App\Services\Migration\Mappers;

use Illuminate\Support\Facades\DB;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Zone;
use App\Models\Location\Street;

/**
 * Маппинг локаций: старые ID из factor_dump → новые ID в realt_bank.
 *
 * Сопоставление по имени (case-insensitive):
 *   lib_towns   → cities     (города)
 *   lib_regions → districts  (районы)
 *   lib_zones   → zones      (жилмассивы)
 *   lib_streets → streets    (улицы)
 *
 * Если локация не найдена в новой базе — создаётся автоматически.
 */
class LocationMapper
{
    // Маппинги: old_id → new_id
    protected array $cityMap = [];      // lib_towns.id → cities.id
    protected array $districtMap = [];  // lib_regions.id → districts.id
    protected array $zoneMap = [];      // lib_zones.id → zones.id
    protected array $streetMap = [];    // lib_streets.id → streets.id

    /**
     * Загрузить все локации из factor_dump и найти соответствия в realt_bank.
     */
    public function build(): void
    {
        $this->buildCityMap();
        $this->buildDistrictMap();
        $this->buildZoneMap();
        $this->buildStreetMap();
    }

    public function getCityId(?int $oldTownId): ?int
    {
        return $this->cityMap[$oldTownId] ?? null;
    }

    public function getDistrictId(?int $oldRegionId): ?int
    {
        return $this->districtMap[$oldRegionId] ?? null;
    }

    public function getZoneId(?int $oldZoneId): ?int
    {
        return $this->zoneMap[$oldZoneId] ?? null;
    }

    public function getStreetId(?int $oldStreetId): ?int
    {
        return $this->streetMap[$oldStreetId] ?? null;
    }

    protected function buildCityMap(): void
    {
        $oldTowns = DB::connection('factor_dump')
            ->table('lib_towns')
            ->where('deleted', 0)
            ->get();

        $newCities = City::all()->keyBy(fn($c) => mb_strtolower(trim($c->name)));

        foreach ($oldTowns as $town) {
            $key = mb_strtolower(trim($town->name));
            if (isset($newCities[$key])) {
                $this->cityMap[$town->id] = $newCities[$key]->id;
            } else {
                // Создаём отсутствующий город с дефолтным регионом (Одесский)
                $city = City::create([
                    'name' => $town->name,
                    'state_id' => $this->getDefaultStateId(),
                    'region_id' => $this->getDefaultRegionId(),
                ]);
                $this->cityMap[$town->id] = $city->id;
            }
        }
    }

    protected function buildDistrictMap(): void
    {
        $oldRegions = DB::connection('factor_dump')
            ->table('lib_regions')
            ->where('deleted', 0)
            ->get();

        $newDistricts = District::all();

        foreach ($oldRegions as $region) {
            $cityId = $this->getCityId($region->town_id);
            $match = $newDistricts->first(function ($d) use ($region, $cityId) {
                return $d->city_id == $cityId
                    && mb_strtolower(trim($d->name)) === mb_strtolower(trim($region->name));
            });

            if ($match) {
                $this->districtMap[$region->id] = $match->id;
            } else {
                $district = District::create([
                    'name' => $region->name,
                    'city_id' => $cityId,
                ]);
                $this->districtMap[$region->id] = $district->id;
            }
        }
    }

    protected function buildZoneMap(): void
    {
        $oldZones = DB::connection('factor_dump')
            ->table('lib_zones')
            ->where('deleted', 0)
            ->get();

        $newZones = Zone::all();

        foreach ($oldZones as $zone) {
            $cityId = $this->getCityId($zone->town_id);
            $districtId = $this->getDistrictId($zone->region_id);

            $match = $newZones->first(function ($z) use ($zone, $cityId) {
                return $z->city_id == $cityId
                    && mb_strtolower(trim($z->name)) === mb_strtolower(trim($zone->name));
            });

            if ($match) {
                $this->zoneMap[$zone->id] = $match->id;
            } else {
                $newZone = Zone::create([
                    'name' => $zone->name,
                    'city_id' => $cityId,
                    'district_id' => $districtId,
                ]);
                $this->zoneMap[$zone->id] = $newZone->id;
            }
        }
    }

    protected function buildStreetMap(): void
    {
        $oldStreets = DB::connection('factor_dump')
            ->table('lib_streets')
            ->where('deleted', 0)
            ->get();

        $newStreets = Street::all();
        $newStreetsIndex = [];
        foreach ($newStreets as $s) {
            $key = ($s->city_id ?? 0) . '|' . mb_strtolower(trim($s->name));
            $newStreetsIndex[$key] = $s->id;
        }

        foreach ($oldStreets as $street) {
            $cityId = $this->getCityId($street->town_id);
            $key = ($cityId ?? 0) . '|' . mb_strtolower(trim($street->name));

            if (isset($newStreetsIndex[$key])) {
                $this->streetMap[$street->id] = $newStreetsIndex[$key];
            } else {
                $newStreet = Street::create([
                    'name' => $street->name,
                    'city_id' => $cityId,
                    'district_id' => $this->getDistrictId($street->region_id),
                    'zone_id' => $this->getZoneId($street->zone_id),
                ]);
                $this->streetMap[$street->id] = $newStreet->id;
                $newStreetsIndex[$key] = $newStreet->id;
            }
        }
    }

    protected function getDefaultStateId(): int
    {
        // Одесская область — state_id from seeder
        return \App\Models\Location\State::where('name', 'like', '%Одесс%')->value('id') ?? 1;
    }

    protected function getDefaultRegionId(): int
    {
        // Одесский район — region_id для новых городов
        return \App\Models\Location\Region::where('name', 'like', '%Одесс%')->value('id') ?? 1;
    }

    public function getStats(): array
    {
        return [
            'cities' => count($this->cityMap),
            'districts' => count($this->districtMap),
            'zones' => count($this->zoneMap),
            'streets' => count($this->streetMap),
        ];
    }
}
