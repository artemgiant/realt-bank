<?php

namespace App\Http\Controllers\Company\Employee\Queries;

use App\Models\Employee\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Построение запроса для списка сотрудников (DataTables).
 *
 * Отвечает за фильтрацию, поиск, сортировку и пагинацию.
 * НЕ форматирует данные — только строит SQL-запрос.
 * Каждый фильтр — отдельный приватный метод.
 */
class EmployeeIndexQuery
{
    /** @var \Illuminate\Database\Eloquent\Builder Построитель запроса */
    private $query;

    /** @var int Общее количество сотрудников (без фильтров) */
    private int $total;

    /**
     * Инициализация базового запроса с eager loading связей.
     */
    public function __construct()
    {
        $this->query = Employee::with(['company', 'office', 'position', 'status']);
        $this->total = Employee::count();
    }

    /**
     * Применить все фильтры из запроса.
     * Вызывает приватные методы для каждой группы фильтров.
     */
    public function applyFilters(Request $request): self
    {
        $this->filterBySearch($request);
        $this->filterByPosition($request);
        $this->filterByStatus($request);
        $this->filterByCompany($request);
        $this->filterByOffice($request);
        $this->filterByTags($request);

        return $this;
    }

    /**
     * Сортировка результатов.
     * Маппинг колонок: created_at, objects_count, deals_count, last_activity, active_until.
     */
    public function applySorting(string $sortColumn, string $sortDirection): self
    {
        $sortableColumns = [
            'created_at'    => 'created_at',
            'objects_count'  => 'objects_count',      // TODO: когда будет связь с объектами
            'deals_count'    => 'deals_count',        // TODO: когда будет связь со сделками
            'last_activity'  => 'last_activity_at',   // TODO: когда будет поле последней активности
            'active_until'   => 'active_until',
        ];

        $orderBy = $sortableColumns[$sortColumn] ?? 'created_at';
        $orderDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        $this->query->orderBy($orderBy, $orderDirection);

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
     * Поиск по тексту (DataTables отправляет search как массив или строку).
     */
    private function filterBySearch(Request $request): void
    {
        $search = $request->input('search');

        if (is_array($search) && !empty($search['value'])) {
            $this->query->search($search['value']);
        } elseif (is_string($search) && !empty($search)) {
            $this->query->search($search);
        }
    }

    /**
     * Фильтр по должности сотрудника.
     */
    private function filterByPosition(Request $request): void
    {
        if ($positionId = $request->input('position_id')) {
            $this->query->byPosition($positionId);
        }
    }

    /**
     * Фильтр по статусу сотрудника.
     */
    private function filterByStatus(Request $request): void
    {
        if ($statusId = $request->input('status')) {
            $this->query->byStatus($statusId);
        }
    }

    /**
     * Фильтр по компании сотрудника.
     */
    private function filterByCompany(Request $request): void
    {
        if ($companyId = $request->input('company_id')) {
            $this->query->byCompany($companyId);
        }
    }

    /**
     * Фильтр по офису сотрудника.
     */
    private function filterByOffice(Request $request): void
    {
        if ($officeId = $request->input('office_id')) {
            $this->query->byOffice($officeId);
        }
    }

    /**
     * Фильтр по тегам (JSON contains для каждого тега).
     */
    private function filterByTags(Request $request): void
    {
        if ($tags = $request->input('tags')) {
            $this->query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tag_ids', $tag);
                }
            });
        }
    }
}
