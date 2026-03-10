<?php

namespace App\Http\Controllers\Property\Developer\ViewData;

use App\Models\Contact\Contact;
use App\Models\Reference\Dictionary;

/**
 * Справочники для форм создания и редактирования девелопера.
 *
 * Убирает дублирование одинаковых запросов в create() и edit().
 * Используется через spread-оператор: ...DeveloperFormData::get()
 */
class DeveloperFormData
{
    /**
     * Загрузить все справочники для формы девелопера.
     * Возвращает массив для передачи во view.
     */
    public static function get(): array
    {
        return [
            // Контакты (с телефонами, отсортированные по имени, лимит 100)
            'contacts' => Contact::with('phones')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(100)
                ->get(),

            // Роли контактов из справочника
            'contactRoles' => Dictionary::getContactRoles(),
        ];
    }
}
