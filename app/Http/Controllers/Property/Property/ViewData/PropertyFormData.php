<?php

namespace App\Http\Controllers\Property\Property\ViewData;

use App\Models\Contact\Contact;
use App\Models\Location\Country;
use App\Models\Reference\Complex;
use App\Models\Reference\Currency;
use App\Models\Reference\Dictionary;
use App\Models\Reference\Source;

/**
 * Справочники для форм создания и редактирования объекта недвижимости.
 *
 * Убирает дублирование одинаковых запросов в create() и edit().
 * Используется через spread-оператор: ...PropertyFormData::get()
 */
class PropertyFormData
{
    /**
     * Загрузить все справочники для формы объекта.
     * Возвращает массив для передачи во view.
     */
    public static function get(): array
    {
        return [
            // Валюты
            'currencies' => Currency::active()->get(),

            // Источники
            'sources' => Source::active()->orderBy('name')->get(),

            // Комплексы
            'complexes' => Complex::active()->orderBy('name')->get(),

            // Контакты (для модального окна)
            'contacts' => Contact::with('phones')->orderBy('last_name')->orderBy('first_name')->limit(100)->get(),

            // Страны
            'countries' => Country::active()->orderBy('name')->get(),

            // Справочники
            'dealTypes' => Dictionary::getDealTypes(),
            'dealKinds' => Dictionary::getDealKinds(),
            'buildingTypes' => Dictionary::getBuildingTypes(),
            'propertyTypes' => Dictionary::getPropertyTypes(),
            'conditions' => Dictionary::getConditions(),
            'wallTypes' => Dictionary::getWallTypes(),
            'heatingTypes' => Dictionary::getHeatingTypes(),
            'roomCounts' => Dictionary::getRoomCounts(),
            'bathroomCounts' => Dictionary::getBathroomCounts(),
            'ceilingHeights' => Dictionary::getCeilingHeights(),
            'features' => Dictionary::getFeatures(),
            'contactTypes' => Dictionary::getContactTypes(),
            'contactRoles' => Dictionary::getContactRoles(),
            'contactTags' => Dictionary::getContactTags(),

            // Годы постройки
            'yearsBuilt' => Dictionary::getYearsBuilt(),

            // Маппинг тип сделки → типы недвижимости (для JS-фильтрации)
            'dealTypePropertyTypeMap' => Dictionary::getDealTypePropertyTypeMapIds(),
        ];
    }
}
