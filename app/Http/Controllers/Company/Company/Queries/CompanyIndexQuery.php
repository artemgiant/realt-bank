<?php

namespace App\Http\Controllers\Company\Company\Queries;

use App\Models\Location\City;
use App\Models\Location\State;
use App\Models\Reference\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Построение запроса для списка компаний (DataTables).
 *
 * Отвечает за фильтрацию, сортировку и пагинацию.
 * НЕ форматирует данные — только строит SQL-запрос.
 * Каждый фильтр — отдельный приватный метод.
 */
class CompanyIndexQuery
{
    /** @var \Illuminate\Database\Eloquent\Builder Построитель запроса */
    private $query;

    /** @var int Общее количество компаний (без фильтров) */
    private int $total;

    /**
     * Инициализация базового запроса с eager loading всех связей.
     */
    public function __construct()
    {
        $this->query = Company::with(['contacts.phones', 'offices', 'city', 'state']);
        $this->total = Company::count();
    }

    /**
     * Применить все фильтры из запроса.
     * Вызывает приватные методы для каждой группы фильтров.
     */
    public function applyFilters(Request $request): self
    {
        $this->filterById($request);
        $this->filterByName($request);
        $this->filterByCompanyType($request);
        $this->filterByStatus($request);
        $this->filterByLocation($request);

        return $this;
    }

    /**
     * Сортировка результатов.
     * Допустимые поля: created_at, name.
     */
    public function applySorting(string $sortField, string $sortDir): self
    {
        $allowedSortFields = ['created_at', 'name'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';

        $this->query->orderBy($sortField, $sortDir);

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

    // ========== Приватные методы фильтрации ==========

    /**
     * Фильтр по ID компании (точный поиск).
     */
    private function filterById(Request $request): void
    {
        if ($request->filled('search_id')) {
            $this->query->where('id', $request->input('search_id'));
        }
    }

    /**
     * Фильтр по названию компании (частичное совпадение).
     */
    private function filterByName(Request $request): void
    {
        if ($request->filled('search_name')) {
            $search = $request->input('search_name');
            $this->query->where('name', 'like', "%{$search}%");
        }
    }

    /**
     * Фильтр по типу компании (агентство, застройщик и т.д.).
     */
    private function filterByCompanyType(Request $request): void
    {
        if ($request->filled('company_type')) {
            $this->query->where('company_type', $request->input('company_type'));
        }
    }

    /**
     * Фильтр по статусу активности (active/inactive).
     */
    private function filterByStatus(Request $request): void
    {
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $this->query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $this->query->where('is_active', false);
            }
        }
    }

    /**
     * Фильтр по локации:
     * - Мульти-выбор городов (city_ids — JSON-массив)
     * - Страна/регион/город (location_type + location_id)
     * - Совместимость со старым фильтром по городу (city_id)
     */
    private function filterByLocation(Request $request): void
    {
        // Сначала проверяем, выбраны ли конкретные города (мульти-выбор)
        $selectedCityIds = [];
        if ($request->filled('city_ids')) {
            $decoded = json_decode($request->input('city_ids'), true);
            if (is_array($decoded) && count($decoded) > 0) {
                $selectedCityIds = $decoded;
            }
        }

        // Если выбраны конкретные города — фильтруем только по ним
        if (!empty($selectedCityIds)) {
            $this->query->whereIn('city_id', $selectedCityIds);
        }
        // Иначе фильтруем по локации (страна/регион)
        elseif ($request->filled('location_type') && $request->filled('location_id')) {
            $locationType = $request->input('location_type');
            $locationId = (int) $request->input('location_id');

            // Маппинг 'region' -> 'state' (JS отправляет 'region', в БД хранится 'state_id')
            if ($locationType === 'region') {
                $locationType = 'state';
            }

            switch ($locationType) {
                case 'country':
                    // Для страны: ищем компании с этой страной напрямую
                    // или компании в городах этой страны
                    $this->query->where(function ($q) use ($locationId) {
                        $q->where('country_id', $locationId);
                        // Также ищем по городам этой страны (через state)
                        $stateIds = State::where('country_id', $locationId)->pluck('id')->toArray();
                        if (!empty($stateIds)) {
                            $cityIds = City::whereIn('state_id', $stateIds)->pluck('id')->toArray();
                            if (!empty($cityIds)) {
                                $q->orWhereIn('city_id', $cityIds);
                            }
                            $q->orWhereIn('state_id', $stateIds);
                        }
                    });
                    break;

                case 'state':
                    // Для области: ищем компании с этой областью или с городами в этой области
                    $this->query->where(function ($q) use ($locationId) {
                        $q->where('state_id', $locationId);
                        $cityIds = City::where('state_id', $locationId)->pluck('id')->toArray();
                        if (!empty($cityIds)) {
                            $q->orWhereIn('city_id', $cityIds);
                        }
                    });
                    break;

                case 'city':
                    // Для города: точное совпадение
                    $this->query->where('city_id', $locationId);
                    break;
            }
        }
        // Совместимость со старым фильтром по городу
        elseif ($request->filled('city_id')) {
            $this->query->where('city_id', $request->input('city_id'));
        }
    }
}
