<?php

namespace App\Http\Controllers\Company\Employee\Presenters;

use App\Models\Employee\Employee;
use Illuminate\Support\Collection;

/**
 * Форматирование данных сотрудника для таблицы DataTables.
 *
 * Преобразует модель Employee в массив для JSON-ответа.
 * НЕ делает запросы к БД — работает только с загруженными данными.
 */
class EmployeeTablePresenter
{
    /** @var \Illuminate\Support\Collection Справочник должностей для Select2 в строках таблицы */
    private Collection $positions;

    /** @var \Illuminate\Support\Collection Справочник офисов для Select2 в строках таблицы */
    private Collection $offices;

    /**
     * Инициализация презентера со справочниками.
     *
     * @param Collection $positions Коллекция должностей для Select2
     * @param Collection $offices   Коллекция офисов для Select2
     */
    public function __construct(Collection $positions, Collection $offices)
    {
        $this->positions = $positions;
        $this->offices = $offices;
    }

    /**
     * Форматировать одного сотрудника в строку таблицы DataTables.
     * Включает основные данные + справочники для inline-редактирования.
     */
    public function toRow(Employee $employee): array
    {
        return [
            'id'               => $employee->id,
            'full_name'        => $employee->full_name,
            'photo_url'        => $employee->photo_url,
            'company_name'     => $employee->company?->name,
            'phone'            => $employee->phone,
            'email'            => $employee->email,
            'position_id'      => $employee->position_id,
            'position_name'    => $employee->position?->name,
            'office_id'        => $employee->office_id,
            'office_name'      => $employee->office?->name,
            'status_id'        => $employee->status_id,
            'status_name'      => $employee->status?->name,
            'objects_count'    => 0, // TODO: связь с объектами
            'clients_count'    => 0, // TODO: связь с клиентами
            'success_deals'    => 0, // TODO: связь со сделками
            'failed_deals'     => 0, // TODO: связь со сделками
            'active_until'     => $employee->active_until?->format('d.m.Y'),
            'active_until_time' => $employee->active_until?->format('H:i'),
            'is_active'        => $employee->is_active,
            'positions'        => $this->positions,
            'offices'          => $this->offices,
        ];
    }

    /**
     * Форматировать коллекцию сотрудников в массив строк таблицы.
     */
    public function toCollection(Collection $employees): array
    {
        return $employees->map(fn(Employee $employee) => $this->toRow($employee))->values()->all();
    }
}
