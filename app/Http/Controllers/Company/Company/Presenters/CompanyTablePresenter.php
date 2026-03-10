<?php

namespace App\Http\Controllers\Company\Company\Presenters;

use App\Models\Reference\Company;
use Illuminate\Support\Collection;

/**
 * Форматирование данных компании для таблицы DataTables.
 *
 * Преобразует модель Company в массив для JSON-ответа.
 * НЕ делает запросы к БД — работает только с загруженными данными.
 */
class CompanyTablePresenter
{
    /**
     * Форматировать одну компанию в строку таблицы DataTables.
     * Включает основные данные + данные для раскрывающейся строки (child row).
     */
    public function toRow(Company $company): array
    {
        $contact = $company->primary_contact;

        return [
            'id' => $company->id,
            'checkbox' => $company->id,
            'logo_url' => $company->logo_url,
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'address' => $company->full_address,
            ],
            'director' => [
                'has_contact' => (bool) $contact,
                'full_name' => $contact ? $contact->full_name : null,
                'phone' => $contact ? $contact->primary_phone : null,
            ],
            'offices_count' => $company->offices->count(),
            'team_count' => 0,
            'properties_count' => 0,
            'deals_count' => 0,
            'commission' => '-',
            'is_active' => $company->is_active,
            'actions' => $company->id,
            'website' => $company->website,
            'description' => $company->description,
            'created_at_formatted' => $company->created_at ? $company->created_at->format('d.m.Y H:i') : null,
            'updated_at_formatted' => $company->updated_at ? $company->updated_at->format('d.m.Y H:i') : null,
        ];
    }

    /**
     * Форматировать коллекцию компаний в массив строк таблицы.
     */
    public function toCollection(Collection $companies): array
    {
        return $companies->map(fn(Company $company) => $this->toRow($company))->values()->all();
    }
}
