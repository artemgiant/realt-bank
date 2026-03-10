<?php

namespace App\Http\Controllers\Property\Developer\Queries;

use App\Models\Location\City;
use App\Models\Location\State;
use App\Models\Reference\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Построение запроса для списка девелоперов (DataTables).
 *
 * Отвечает за фильтрацию, сортировку и пагинацию.
 * НЕ форматирует данные — только строит SQL-запрос.
 * Каждый фильтр — отдельный приватный метод.
 */
class DeveloperIndexQuery
{
    /** @var \Illuminate\Database\Eloquent\Builder Построитель запроса */
    private $query;

    /** @var int Общее количество девелоперов (без фильтров) */
    private int $total;

    /**
     * Инициализация базового запроса с eager loading всех связей.
     */
    public function __construct()
    {
        $this->query = Developer::with(['contacts.phones', 'complexes', 'locations']);
        $this->total = Developer::count();
    }

    /**
     * Применить все фильтры из запроса.
     * Вызывает приватные методы для каждой группы фильтров.
     */
    public function applyFilters(Request $request): self
    {
        $this->filterBySearchId($request);
        $this->filterByContact($request);
        $this->filterByCityIds($request);
        $this->filterByLocation($request);

        return $this;
    }

    /**
     * Сортировка результатов.
     * Допустимые поля: created_at, name, year_founded.
     */
    public function applySorting(string $sortField, string $sortDir): self
    {
        $allowedSortFields = ['created_at', 'name', 'year_founded'];
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
     * Фильтр по ID девелопера (точный поиск).
     */
    private function filterBySearchId(Request $request): void
    {
        if ($request->filled('search_id')) {
            $this->query->where('id', $request->input('search_id'));
        }
    }

    /**
     * Фильтр по контакту (имя, фамилия, отчество, телефон).
     */
    private function filterByContact(Request $request): void
    {
        if (!$request->filled('contact_search')) {
            return;
        }

        $search = $request->input('contact_search');
        $this->query->whereHas('contacts', function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('middle_name', 'like', "%{$search}%")
                ->orWhereHas('phones', function ($pq) use ($search) {
                    $pq->where('phone', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Фильтр по конкретным городам (JSON-массив city_ids).
     * Если выбраны города — фильтруем только по ним.
     */
    private function filterByCityIds(Request $request): void
    {
        if (!$request->filled('city_ids')) {
            return;
        }

        $decoded = json_decode($request->input('city_ids'), true);
        if (!is_array($decoded) || count($decoded) === 0) {
            return;
        }

        $this->query->whereHas('locations', function ($q) use ($decoded) {
            $q->where('location_type', 'city')
                ->whereIn('location_id', $decoded);
        });
    }

    /**
     * Фильтр по локации (страна/регион) с вложенными подзапросами.
     * Для страны: ищем девелоперов с этой страной, областями или городами в ней.
     * Для региона: ищем девелоперов с этим регионом или городами в нём.
     * Для города: точное совпадение.
     * Применяется только если не выбраны конкретные города (city_ids).
     */
    private function filterByLocation(Request $request): void
    {
        // Не применяем если выбраны конкретные города
        if ($request->filled('city_ids')) {
            $decoded = json_decode($request->input('city_ids'), true);
            if (is_array($decoded) && count($decoded) > 0) {
                return;
            }
        }

        if (!$request->filled('location_type') || !$request->filled('location_id')) {
            return;
        }

        $locationType = $request->input('location_type');
        // Маппинг 'region' -> 'state' (JS отправляет 'region', в БД хранится 'state')
        if ($locationType === 'region') {
            $locationType = 'state';
        }
        $locationId = (int) $request->input('location_id');

        $this->query->whereHas('locations', function ($q) use ($locationType, $locationId) {
            if ($locationType === 'country') {
                // Для страны: ищем девелоперов с этой страной, или с областями/городами в этой стране
                $stateIds = State::where('country_id', $locationId)->pluck('id')->toArray();
                $cityIds = City::whereIn('state_id', $stateIds)->pluck('id')->toArray();

                $q->where(function ($subQ) use ($locationId, $stateIds, $cityIds) {
                    $subQ->where(function ($w) use ($locationId) {
                        $w->where('location_type', 'country')->where('location_id', $locationId);
                    });
                    if (!empty($stateIds)) {
                        $subQ->orWhere(function ($w) use ($stateIds) {
                            $w->where('location_type', 'state')->whereIn('location_id', $stateIds);
                        });
                    }
                    if (!empty($cityIds)) {
                        $subQ->orWhere(function ($w) use ($cityIds) {
                            $w->where('location_type', 'city')->whereIn('location_id', $cityIds);
                        });
                    }
                });
            } elseif ($locationType === 'state') {
                // Для региона: ищем девелоперов с этим регионом или с городами в нём
                $cityIds = City::where('state_id', $locationId)->pluck('id')->toArray();

                $q->where(function ($subQ) use ($locationId, $cityIds) {
                    $subQ->where(function ($w) use ($locationId) {
                        $w->where('location_type', 'state')->where('location_id', $locationId);
                    });
                    if (!empty($cityIds)) {
                        $subQ->orWhere(function ($w) use ($cityIds) {
                            $w->where('location_type', 'city')->whereIn('location_id', $cityIds);
                        });
                    }
                });
            } else {
                // Для города: точное совпадение
                $q->where('location_type', $locationType)
                    ->where('location_id', $locationId);
            }
        });
    }
}
