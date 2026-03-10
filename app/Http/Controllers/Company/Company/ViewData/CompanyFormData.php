<?php

namespace App\Http\Controllers\Company\Company\ViewData;

use App\Models\Contact\Contact;
use App\Models\Reference\Dictionary;

/**
 * Справочники для форм создания и редактирования компании.
 *
 * Убирает дублирование одинаковых запросов в create() и edit().
 * Используется через spread-оператор: ...CompanyFormData::get()
 */
class CompanyFormData
{
    /**
     * Загрузить все справочники для формы компании.
     * Возвращает массив для передачи во view.
     */
    public static function get(): array
    {
        return [
            // Контакты (с телефонами, сортировка по фамилии и имени, лимит 100)
            'contacts' => Contact::with('phones')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(100)
                ->get(),

            // Типы агентств
            'agencyTypes' => Dictionary::getAgencyTypes(),

            // Роли контактов
            'contactRoles' => Dictionary::getContactRoles(),
        ];
    }
}
