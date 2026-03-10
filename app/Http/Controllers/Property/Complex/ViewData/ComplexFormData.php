<?php

namespace App\Http\Controllers\Property\Complex\ViewData;

use App\Models\Contact\Contact;
use App\Models\Reference\Dictionary;

/**
 * Справочники для форм создания и редактирования комплекса.
 *
 * Убирает дублирование одинаковых запросов в create() и edit().
 * Используется через spread-оператор: ...ComplexFormData::get()
 */
class ComplexFormData
{
    /** Загрузить все справочники для формы комплекса */
    public static function get(): array
    {
        return [
            // Контакты (для модального окна)
            'contacts' => Contact::with('phones')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(100)
                ->get(),

            // Справочники
            'housingClasses' => Dictionary::getHousingClasses(),
            'heatingTypes' => Dictionary::getHeatingTypes(),
            'wallTypes' => Dictionary::getWallTypes(),
            'yearsBuilt' => Dictionary::getYearsBuilt(),
            'features' => Dictionary::getComplexFeatures(),
            'conditions' => Dictionary::getConditions(),
            'complexCategories' => Dictionary::getComplexCategories(),
            'objectTypes' => Dictionary::getPropertyTypes(),
            'contactRoles' => Dictionary::getContactRoles(),
        ];
    }
}
