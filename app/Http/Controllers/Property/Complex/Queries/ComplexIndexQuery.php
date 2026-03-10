<?php

namespace App\Http\Controllers\Property\Complex\Queries;

use App\Models\Reference\Complex;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Построение запроса для списка комплексов с фильтрацией.
 *
 * Инкапсулирует всю логику фильтрации (14 фильтров),
 * сортировку и пагинацию для DataTables Server-Side.
 */
class ComplexIndexQuery
{
    private Builder $query;
    private int $total;

    public function __construct()
    {
        $this->query = Complex::with([
            'developer',
            'city.state.country',
            'district',
            'zone',
            'blocks.heatingType',
            'blocks.wallType',
            'blocks.street',
            'contacts.phones',
        ]);

        $this->total = Complex::count();
    }

    /** Применить все фильтры из запроса */
    public function applyFilters(Request $request): self
    {
        $this->filterBySearchId($request);
        $this->filterByCategory($request);
        $this->filterByDeveloper($request);
        $this->filterByHousingClass($request);
        $this->filterByObjectType($request);
        $this->filterByYearBuilt($request);
        $this->filterByCondition($request);
        $this->filterByWallType($request);
        $this->filterByHeatingType($request);
        $this->filterByFeatures($request);
        $this->filterByArea($request);
        $this->filterByFloors($request);
        $this->filterByPrice($request);
        $this->filterByPricePerM2($request);
        $this->filterByLocation($request);
        $this->filterByDetails($request);

        return $this;
    }

    /** Применить сортировку */
    public function applySorting(string $field, string $direction): self
    {
        $allowedFields = ['created_at', 'name', 'price_per_m2', 'area_from', 'price_total'];

        if (in_array($field, $allowedFields)) {
            $this->query->orderBy($field, $direction === 'asc' ? 'asc' : 'desc');
        }

        return $this;
    }

    /** Общее количество записей (до фильтрации) */
    public function getTotal(): int
    {
        return $this->total;
    }

    /** Количество записей после фильтрации */
    public function getFiltered(): int
    {
        return $this->query->count();
    }

    /** Получить страницу результатов */
    public function paginate(int $start, int $length): Collection
    {
        return $this->query->skip($start)->take($length)->get();
    }

    // ========== Приватные методы фильтрации ==========

    /** Фильтр по ID комплекса */
    private function filterBySearchId(Request $request): void
    {
        if ($searchId = $request->get('search_id')) {
            $this->query->where('id', $searchId);
        }
    }

    /** Фильтр по категории (JSON массив) */
    private function filterByCategory(Request $request): void
    {
        if ($categoryIds = $request->get('category_id')) {
            $categoryIds = is_array($categoryIds) ? $categoryIds : [$categoryIds];
            $this->query->where(function ($q) use ($categoryIds) {
                foreach ($categoryIds as $categoryId) {
                    $q->orWhereJsonContains('categories', (int) $categoryId);
                }
            });
        }
    }

    /** Фильтр по девелоперу */
    private function filterByDeveloper(Request $request): void
    {
        if ($developerId = $request->get('developer_id')) {
            $this->query->where('developer_id', $developerId);
        }
    }

    /** Фильтр по классу жилья (JSON массив) */
    private function filterByHousingClass(Request $request): void
    {
        if ($housingClassIds = $request->get('housing_class_id')) {
            $housingClassIds = is_array($housingClassIds) ? $housingClassIds : [$housingClassIds];
            $this->query->where(function ($q) use ($housingClassIds) {
                foreach ($housingClassIds as $housingClassId) {
                    $q->orWhereJsonContains('housing_classes', (int) $housingClassId);
                }
            });
        }
    }

    /** Фильтр по типу объекта (JSON массив) */
    private function filterByObjectType(Request $request): void
    {
        if ($objectTypeIds = $request->get('object_type_id')) {
            if (is_array($objectTypeIds)) {
                $this->query->where(function ($q) use ($objectTypeIds) {
                    foreach ($objectTypeIds as $objectTypeId) {
                        $q->orWhereJsonContains('object_types', (int) $objectTypeId);
                    }
                });
            }
        }
    }

    /** Фильтр по году сдачи (через блоки) */
    private function filterByYearBuilt(Request $request): void
    {
        if ($yearsBuilt = $request->get('year_built')) {
            if (is_array($yearsBuilt)) {
                $this->query->whereHas('blocks', function ($q) use ($yearsBuilt) {
                    $q->whereIn('year_built', $yearsBuilt);
                });
            }
        }
    }

    /** Фильтр по состоянию (JSON массив) */
    private function filterByCondition(Request $request): void
    {
        if ($conditionIds = $request->get('condition_id')) {
            if (is_array($conditionIds)) {
                $this->query->where(function ($q) use ($conditionIds) {
                    foreach ($conditionIds as $conditionId) {
                        $q->orWhereJsonContains('conditions', (int) $conditionId);
                    }
                });
            }
        }
    }

    /** Фильтр по типу стен (через блоки) */
    private function filterByWallType(Request $request): void
    {
        if ($wallTypeIds = $request->get('wall_type_id')) {
            if (is_array($wallTypeIds)) {
                $this->query->whereHas('blocks', function ($q) use ($wallTypeIds) {
                    $q->whereIn('wall_type_id', $wallTypeIds);
                });
            }
        }
    }

    /** Фильтр по отоплению (через блоки) */
    private function filterByHeatingType(Request $request): void
    {
        if ($heatingTypeIds = $request->get('heating_type_id')) {
            if (is_array($heatingTypeIds)) {
                $this->query->whereHas('blocks', function ($q) use ($heatingTypeIds) {
                    $q->whereIn('heating_type_id', $heatingTypeIds);
                });
            }
        }
    }

    /** Фильтр по особенностям (все указанные должны присутствовать — AND) */
    private function filterByFeatures(Request $request): void
    {
        if ($featureIds = $request->get('features')) {
            if (is_array($featureIds)) {
                $this->query->where(function ($q) use ($featureIds) {
                    foreach ($featureIds as $featureId) {
                        $q->whereJsonContains('features', (int) $featureId);
                    }
                });
            }
        }
    }

    /** Фильтр по площади (от/до) */
    private function filterByArea(Request $request): void
    {
        if ($areaFrom = $request->get('area_from')) {
            $this->query->where('area_from', '>=', $areaFrom);
        }
        if ($areaTo = $request->get('area_to')) {
            $this->query->where('area_to', '<=', $areaTo);
        }
    }

    /** Фильтр по этажности (через блоки, от/до) */
    private function filterByFloors(Request $request): void
    {
        if ($floorsFrom = $request->get('floors_from')) {
            $this->query->whereHas('blocks', function ($q) use ($floorsFrom) {
                $q->where('floors_total', '>=', $floorsFrom);
            });
        }
        if ($floorsTo = $request->get('floors_to')) {
            $this->query->whereHas('blocks', function ($q) use ($floorsTo) {
                $q->where('floors_total', '<=', $floorsTo);
            });
        }
    }

    /** Фильтр по цене (от/до) */
    private function filterByPrice(Request $request): void
    {
        if ($priceFrom = $request->get('price_from')) {
            $this->query->where('price_total', '>=', $priceFrom);
        }
        if ($priceTo = $request->get('price_to')) {
            $this->query->where('price_total', '<=', $priceTo);
        }
    }

    /** Фильтр по цене за м² (от/до) */
    private function filterByPricePerM2(Request $request): void
    {
        if ($pricePerM2From = $request->get('price_per_m2_from')) {
            $this->query->where('price_per_m2', '>=', $pricePerM2From);
        }
        if ($pricePerM2To = $request->get('price_per_m2_to')) {
            $this->query->where('price_per_m2', '<=', $pricePerM2To);
        }
    }

    /** Фильтр по локации (страна/регион/город) */
    private function filterByLocation(Request $request): void
    {
        $locationType = $request->get('location_type');
        $locationId = $request->get('location_id');

        if (!$locationType || !$locationId) {
            return;
        }

        switch ($locationType) {
            case 'country':
                $this->query->whereHas('city.state', function ($q) use ($locationId) {
                    $q->where('country_id', $locationId);
                });
                break;
            case 'region':
                $this->query->whereHas('city', function ($q) use ($locationId) {
                    $q->where('state_id', $locationId);
                });
                break;
            case 'city':
                $this->query->where('city_id', $locationId);
                break;
        }
    }

    /** Детальные фильтры локации (районы, улицы, зоны, комплексы, девелоперы) */
    private function filterByDetails(Request $request): void
    {
        $detailIds = $request->get('detail_ids');
        if (!$detailIds) {
            return;
        }

        $details = json_decode($detailIds, true);
        if (!is_array($details) || count($details) === 0) {
            return;
        }

        $this->query->where(function ($q) use ($details) {
            foreach ($details as $detail) {
                switch ($detail['type']) {
                    case 'district':
                        $q->orWhere('district_id', $detail['id']);
                        break;
                    case 'street':
                        $q->orWhereHas('blocks', function ($bq) use ($detail) {
                            $bq->where('street_id', $detail['id']);
                        });
                        break;
                    case 'landmark':
                        $q->orWhere('zone_id', $detail['id']);
                        break;
                    case 'complex':
                        $q->orWhere('id', $detail['id']);
                        break;
                    case 'developer':
                        $q->orWhere('developer_id', $detail['id']);
                        break;
                }
            }
        });
    }
}
