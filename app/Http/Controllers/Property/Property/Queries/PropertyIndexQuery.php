<?php

namespace App\Http\Controllers\Property\Property\Queries;

use App\Models\Employee\Employee;
use App\Models\Property\Property;
use App\Models\Reference\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Построение запроса для списка объектов недвижимости (DataTables).
 *
 * Отвечает за фильтрацию, поиск, сортировку и пагинацию.
 * НЕ форматирует данные — только строит SQL-запрос.
 * Каждый фильтр — отдельный приватный метод.
 */
class PropertyIndexQuery
{
    /** @var \Illuminate\Database\Eloquent\Builder Построитель запроса */
    private $query;

    /** @var int Общее количество объектов (без фильтров) */
    private int $total;

    /**
     * Инициализация базового запроса с eager loading всех связей.
     */
    public function __construct()
    {
        $this->query = Property::with([
            'dealType',
            'currency',
            'translations',
            'user',
            'roomCount',
            'propertyType',
            'condition',
            'buildingType',
            'photos',
            'contacts.phones',
            'employee.company',
            'contactType',
            'complex',
            'street',
            'zone',
            'district',
            'city',
            'state',
            'country',
            'features',
        ]);

        // Ограничение видимости объектов по правам пользователя
        $this->applyAccessScope();

        $this->total = (clone $this->query)->count();
    }

    /**
     * Применить все фильтры из запроса.
     * Вызывает приватные методы для каждой группы фильтров.
     */
    public function applyFilters(Request $request, ?Currency $targetCurrency = null): self
    {
        $this->filterByDealType($request);
        $this->joinCurrency();
        $this->filterByPrice($request, $targetCurrency);
        $this->filterByArea($request);
        $this->filterByFloor($request);
        $this->filterByPricePerM2($request);
        $this->filterByDictionaries($request);
        $this->filterByFeatures($request);
        $this->filterByDeveloper($request);
        $this->filterByStatus($request);
        $this->filterBySearchId($request);
        $this->filterByContact($request);
        $this->filterByDate($request);
        $this->filterByLocation($request);

        return $this;
    }

    /**
     * Глобальный поиск DataTables — по ID, заголовку, улице, городу.
     */
    public function applySearch(?string $searchValue): self
    {
        if (empty($searchValue)) {
            return $this;
        }

        $this->query->where(function ($q) use ($searchValue) {
            $q->where('properties.id', 'like', "%{$searchValue}%")
                ->orWhereHas('translations', function ($tq) use ($searchValue) {
                    $tq->where('title', 'like', "%{$searchValue}%");
                })
                ->orWhereHas('street', function ($sq) use ($searchValue) {
                    $sq->where('name', 'like', "%{$searchValue}%");
                })
                ->orWhereHas('city', function ($cq) use ($searchValue) {
                    $cq->where('name', 'like', "%{$searchValue}%");
                });
        });

        return $this;
    }

    /**
     * Сортировка результатов.
     * Для цены и цены за м² — сортировка с учётом курса валюты (приведение к UAH).
     */
    public function applySorting(string $sortField, string $sortDir): self
    {
        $allowedSortFields = ['created_at', 'price', 'area_total', 'price_per_m2'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';

        if (in_array($sortField, ['price', 'price_per_m2'])) {
            $this->query->orderByRaw("properties.{$sortField} * COALESCE(property_currency.rate, 1) {$sortDir}");
        } else {
            $this->query->orderBy("properties.{$sortField}", $sortDir);
        }

        return $this;
    }

    /**
     * Общее количество записей (без фильтров) — для DataTables recordsTotal.
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Количество записей после фильтрации — для DataTables recordsFiltered.
     */
    public function getFiltered(): int
    {
        return $this->query->count();
    }

    /**
     * Получить страницу результатов (skip/take).
     */
    public function paginate(int $start, int $length): Collection
    {
        return $this->query->skip($start)->take($length)->get();
    }

    // ========== Ограничение доступа ==========

    /**
     * Ограничение видимости объектов по правам текущего пользователя.
     *
     * Иерархия (от широкого к узкому):
     * - view_all     → все объекты
     * - view_company → объекты сотрудников своей компании
     * - view_office  → объекты сотрудников своего офиса
     * - (иначе)      → только свои объекты (user_id)
     */
    private function applyAccessScope(): void
    {
        $user = auth()->user();

        if (!$user) {
            $this->query->whereRaw('0 = 1');
            return;
        }

        // view_all — видит всё, ограничений нет
        if ($user->can('properties.view_all')) {
            return;
        }

        $employee = $user->employee;

        // view_company — объекты всех сотрудников компании + свои
        if ($user->can('properties.view_company') && $employee && $employee->company_id) {
            $companyUserIds = Employee::where('company_id', $employee->company_id)
                ->whereNotNull('user_id')
                ->pluck('user_id');

            $this->query->where(function ($q) use ($user, $companyUserIds) {
                $q->whereIn('properties.user_id', $companyUserIds)
                    ->orWhere('properties.user_id', $user->id);
            });
            return;
        }

        // view_office — объекты всех сотрудников офиса + свои
        if ($user->can('properties.view_office') && $employee && $employee->office_id) {
            $officeUserIds = Employee::where('office_id', $employee->office_id)
                ->whereNotNull('user_id')
                ->pluck('user_id');

            $this->query->where(function ($q) use ($user, $officeUserIds) {
                $q->whereIn('properties.user_id', $officeUserIds)
                    ->orWhere('properties.user_id', $user->id);
            });
            return;
        }

        // Только свои объекты
        $this->query->where('properties.user_id', $user->id);
    }

    // ========== Приватные методы фильтрации ==========

    /** Фильтр по типу сделки (продажа/аренда) */
    private function filterByDealType(Request $request): void
    {
        if ($request->filled('deal_type_id')) {
            $this->query->where('deal_type_id', $request->deal_type_id);
        }
    }

    /** Присоединяем таблицу валют для конвертации цен при фильтрации и сортировке */
    private function joinCurrency(): void
    {
        $this->query->leftJoin('currencies as property_currency', 'properties.currency_id', '=', 'property_currency.id')
            ->select('properties.*');
    }

    /** Фильтр по цене с конвертацией валюты (приведение к UAH через курсы) */
    private function filterByPrice(Request $request, ?Currency $targetCurrency = null): void
    {
        if ($targetCurrency) {
            $targetRate = $targetCurrency->rate;

            if ($request->filled('price_from')) {
                $priceFromUah = $request->price_from * $targetRate;
                $this->query->whereRaw('(properties.price * property_currency.rate) >= ?', [$priceFromUah]);
            }
            if ($request->filled('price_to')) {
                $priceToUah = $request->price_to * $targetRate;
                $this->query->whereRaw('(properties.price * property_currency.rate) <= ?', [$priceToUah]);
            }
        } else {
            if ($request->filled('price_from')) {
                $this->query->where('properties.price', '>=', $request->price_from);
            }
            if ($request->filled('price_to')) {
                $this->query->where('properties.price', '<=', $request->price_to);
            }
        }
    }

    /** Фильтр по площади (общая, жилая, кухня, участок) */
    private function filterByArea(Request $request): void
    {
        if ($request->filled('area_from')) {
            $this->query->where('area_total', '>=', $request->area_from);
        }
        if ($request->filled('area_to')) {
            $this->query->where('area_total', '<=', $request->area_to);
        }

        if ($request->filled('area_living_from')) {
            $this->query->where('area_living', '>=', $request->area_living_from);
        }
        if ($request->filled('area_living_to')) {
            $this->query->where('area_living', '<=', $request->area_living_to);
        }

        if ($request->filled('area_kitchen_from')) {
            $this->query->where('area_kitchen', '>=', $request->area_kitchen_from);
        }
        if ($request->filled('area_kitchen_to')) {
            $this->query->where('area_kitchen', '<=', $request->area_kitchen_to);
        }

        if ($request->filled('area_land_from')) {
            $this->query->where('area_land', '>=', $request->area_land_from);
        }
        if ($request->filled('area_land_to')) {
            $this->query->where('area_land', '<=', $request->area_land_to);
        }
    }

    /** Фильтр по этажу и этажности здания */
    private function filterByFloor(Request $request): void
    {
        if ($request->filled('floor_from')) {
            $this->query->where('floor', '>=', $request->floor_from);
        }
        if ($request->filled('floor_to')) {
            $this->query->where('floor', '<=', $request->floor_to);
        }

        if ($request->filled('floors_total_from')) {
            $this->query->where('floors_total', '>=', $request->floors_total_from);
        }
        if ($request->filled('floors_total_to')) {
            $this->query->where('floors_total', '<=', $request->floors_total_to);
        }
    }

    /** Фильтр по цене за м² (вычисляется как price / area_total) */
    private function filterByPricePerM2(Request $request): void
    {
        if ($request->filled('price_per_m2_from') || $request->filled('price_per_m2_to')) {
            $this->query->whereNotNull('price')
                ->whereNotNull('area_total')
                ->where('area_total', '>', 0);

            if ($request->filled('price_per_m2_from')) {
                $this->query->whereRaw('(price / area_total) >= ?', [$request->price_per_m2_from]);
            }
            if ($request->filled('price_per_m2_to')) {
                $this->query->whereRaw('(price / area_total) <= ?', [$request->price_per_m2_to]);
            }
        }
    }

    /** Фильтр по справочникам (комнаты, тип, состояние, стены, отопление и т.д.) */
    private function filterByDictionaries(Request $request): void
    {
        $filters = [
            'room_count_id',
            'property_type_id',
            'condition_id',
            'building_type_id',
            'wall_type_id',
            'heating_type_id',
            'bathroom_count_id',
            'ceiling_height_id',
        ];

        foreach ($filters as $field) {
            if ($request->filled($field)) {
                $ids = is_array($request->$field) ? $request->$field : [$request->$field];
                $this->query->whereIn($field, $ids);
            }
        }

        if ($request->filled('year_built')) {
            $years = is_array($request->year_built) ? $request->year_built : [$request->year_built];
            $this->query->whereIn('year_built', $years);
        }
    }

    /** Фильтр по особенностям объекта (балкон, парковка, лифт и т.д.) */
    private function filterByFeatures(Request $request): void
    {
        if ($request->filled('features')) {
            $featureIds = is_array($request->features) ? $request->features : [$request->features];
            $this->query->whereHas('features', function ($q) use ($featureIds) {
                $q->whereIn('dictionaries.id', $featureIds);
            });
        }
    }

    /** Фильтр по девелоперу (через связь complex.developer) */
    private function filterByDeveloper(Request $request): void
    {
        if ($request->filled('developer_id')) {
            $developerIds = is_array($request->developer_id) ? $request->developer_id : [$request->developer_id];
            $this->query->whereHas('complex.developer', function ($q) use ($developerIds) {
                $q->whereIn('developers.id', $developerIds);
            });
        }
    }

    /** Фильтр по статусу: мои, черновик, активные, на модерации, архив, избранные */
    private function filterByStatus(Request $request): void
    {
        if (!$request->filled('status')) {
            return;
        }

        switch ($request->status) {
            case 'open':
                $this->query->where('properties.is_visible_to_agents', true);
                break;
            case 'my':
                $this->query->where('user_id', auth()->id());
                break;
            case 'my_company':
                $employee = auth()->user()->employee;
                if ($employee && $employee->company_id) {
                    $companyUserIds = Employee::where('company_id', $employee->company_id)
                        ->whereNotNull('user_id')
                        ->pluck('user_id');
                    $this->query->whereIn('properties.user_id', $companyUserIds);
                } else {
                    $this->query->where('properties.user_id', auth()->id());
                }
                break;
            case 'draft':
                $this->query->where('status', 'draft');
                break;
            case 'active':
                $this->query->where('status', 'active');
                break;
            case 'on_review':
                $this->query->where('status', 'on_review');
                break;
            case 'favorite':
                // TODO: фильтр по избранным
                break;
            case 'archive':
                $this->query->where('status', 'archived');
                break;
        }
    }

    /** Фильтр по ID объекта (точный поиск) */
    private function filterBySearchId(Request $request): void
    {
        if ($request->filled('search_id')) {
            $this->query->where('properties.id', $request->search_id);
        }
    }

    /** Фильтр по контакту — ответственный сотрудник (employee) */
    private function filterByContact(Request $request): void
    {
        if ($request->filled('contact_search')) {
            $search = $request->contact_search;
            $this->query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
    }

    /** Фильтр по дате создания (от/до) */
    private function filterByDate(Request $request): void
    {
        if ($request->filled('created_from')) {
            $this->query->whereDate('properties.created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $this->query->whereDate('properties.created_at', '<=', $request->created_to);
        }
    }

    /**
     * Фильтр по локации:
     * - Мульти-выбор городов (city_ids)
     * - Страна/регион (location_type + location_id)
     * - Детали: районы, улицы, зоны, ЖК, секции, девелоперы (detail_ids)
     */
    private function filterByLocation(Request $request): void
    {
        // Мульти-выбор городов
        if ($request->filled('city_ids')) {
            $cityIds = json_decode($request->input('city_ids'), true);

            if (is_array($cityIds) && !empty($cityIds)) {
                $this->query->where(function ($q) use ($cityIds) {
                    $q->whereIn('city_id', $cityIds)
                        ->orWhereHas('street', function ($sq) use ($cityIds) {
                            $sq->whereIn('city_id', $cityIds);
                        })
                        ->orWhereHas('district', function ($dq) use ($cityIds) {
                            $dq->whereIn('city_id', $cityIds);
                        })
                        ->orWhereHas('zone', function ($zq) use ($cityIds) {
                            $zq->whereIn('city_id', $cityIds);
                        });
                });
            }
        }
        // Локация (страна/регион)
        elseif ($request->filled('location_type') && $request->filled('location_id')) {
            $locationType = $request->location_type;
            $locationId = $request->location_id;

            switch ($locationType) {
                case 'country':
                    $this->query->where(function ($q) use ($locationId) {
                        $q->where('country_id', $locationId)
                            ->orWhereHas('state', function ($sq) use ($locationId) {
                                $sq->where('country_id', $locationId);
                            })
                            ->orWhereHas('city', function ($cq) use ($locationId) {
                                $cq->where('country_id', $locationId);
                            });
                    });
                    break;
                case 'region':
                    $this->query->where(function ($q) use ($locationId) {
                        $q->where('state_id', $locationId)
                            ->orWhereHas('city', function ($cq) use ($locationId) {
                                $cq->where('state_id', $locationId);
                            });
                    });
                    break;
            }
        }

        // Детали локации (районы, улицы, зоны и т.д.)
        if ($request->filled('detail_ids')) {
            $detailIds = json_decode($request->detail_ids, true);

            if (is_array($detailIds) && !empty($detailIds)) {
                $this->query->where(function ($q) use ($detailIds) {
                    foreach ($detailIds as $detail) {
                        $type = $detail['type'] ?? null;
                        $id = $detail['id'] ?? null;

                        if (!$type || !$id) continue;

                        switch ($type) {
                            case 'district':
                                $q->orWhere('district_id', $id);
                                break;
                            case 'street':
                                $q->orWhere('street_id', $id);
                                break;
                            case 'landmark':
                                $q->orWhere('zone_id', $id);
                                break;
                            case 'complex':
                                $q->orWhere('complex_id', $id);
                                break;
                            case 'block':
                                $q->orWhere('block_id', $id);
                                break;
                            case 'developer':
                                $q->orWhereHas('complex.developer', function ($dq) use ($id) {
                                    $dq->where('developers.id', $id);
                                });
                                break;
                        }
                    }
                });
            }
        }
    }
}
