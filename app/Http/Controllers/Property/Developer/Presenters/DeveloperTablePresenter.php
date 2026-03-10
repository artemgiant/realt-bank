<?php

namespace App\Http\Controllers\Property\Developer\Presenters;

use App\Models\Reference\Developer;
use Illuminate\Support\Collection;

/**
 * Форматирование данных девелопера для таблицы DataTables.
 *
 * Преобразует модель Developer в массив для JSON-ответа.
 * НЕ делает запросы к БД — работает только с загруженными данными.
 */
class DeveloperTablePresenter
{
    /**
     * Форматировать одного девелопера в строку таблицы DataTables.
     * Включает основные данные + данные для раскрывающейся строки (child row).
     */
    public function toRow(Developer $developer): array
    {
        // Локация из первой записи locations
        $location = $developer->locations->first();
        $locationText = $location ? $location->full_location_name : '-';

        // Контакт (берём основной через accessor или первый из списка)
        $contact = $developer->primary_contact;
        $contactData = [
            'has_contact' => (bool) $contact,
            'full_name' => $contact?->full_name,
            'contact_role_names' => $contact?->contact_role_names,
            'phone' => $contact?->primary_phone,
            'contacts_count' => $developer->contacts->count(),
        ];

        return [
            'id' => $developer->id,
            'checkbox' => $developer->id,
            'developer' => [
                'id' => $developer->id,
                'name' => $developer->name,
                'logo_url' => $developer->logo_url,
                'location' => $locationText,
            ],
            'year_founded' => $developer->year_founded ?? '-',
            'complexes_count' => $developer->complexes->count(),
            'contact' => $contactData,
            'actions' => $developer->id,
            // Дополнительные данные для child row
            'description' => $developer->description,
            'website' => $developer->website,
            'company_website' => $developer->company_website,
            'agent_notes' => $developer->agent_notes,
            'created_at_formatted' => $developer->created_at?->format('d.m.Y H:i'),
            'updated_at_formatted' => $developer->updated_at?->format('d.m.Y H:i'),
        ];
    }

    /**
     * Форматировать коллекцию девелоперов в массив строк таблицы.
     */
    public function toCollection(Collection $developers): array
    {
        return $developers->map(fn(Developer $developer) => $this->toRow($developer))->values()->all();
    }
}
