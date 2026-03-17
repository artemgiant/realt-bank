<?php

namespace App\Http\Controllers\Property\Property\Requests;

use App\Models\Reference\Dictionary;

/**
 * Общие правила валидации для создания и обновления объекта недвижимости.
 *
 * Используется в StorePropertyRequest и UpdatePropertyRequest.
 * Содержит базовые правила и сообщения об ошибках, которые совпадают на 95%.
 */
trait PropertyValidationRules
{
    /**
     * Базовые правила валидации полей объекта недвижимости.
     * Общие для store и update — различия добавляются в конкретных Request-классах.
     */
    protected function baseRules(): array
    {
        return [
            // Обязательные поля
            'deal_type_id' => 'required|exists:dictionaries,id',   // Тип сделки (продажа/аренда)
            'currency_id' => 'required|exists:currencies,id',      // Валюта цены

            // Справочники (необязательные)
            'deal_kind_id' => 'nullable|exists:dictionaries,id',      // Вид сделки
            'building_type_id' => 'nullable|exists:dictionaries,id',  // Тип здания
            'property_type_id' => 'nullable|exists:dictionaries,id',  // Тип недвижимости (квартира/дом/участок)
            'room_count_id' => 'nullable|exists:dictionaries,id',     // Количество комнат
            'condition_id' => 'nullable|exists:dictionaries,id',      // Состояние (ремонт/без ремонта)
            'bathroom_count_id' => 'nullable|exists:dictionaries,id', // Количество ванных комнат
            'ceiling_height_id' => 'nullable|exists:dictionaries,id', // Высота потолков
            'wall_type_id' => 'nullable|exists:dictionaries,id',      // Тип стен (кирпич/панель)
            'heating_type_id' => 'nullable|exists:dictionaries,id',   // Тип отопления
            'source_id' => 'nullable|exists:sources,id',              // Источник объекта
            'contact_type_id' => 'nullable|exists:dictionaries,id',   // Тип контакта (собственник/посредник)

            // Локация (иерархия: страна → область → город → район → зона → улица)
            'country_id' => 'nullable|exists:countries,id',       // Страна
            'state_id' => 'nullable|exists:states,id',            // Область/регион
            'region_id' => 'required|exists:regions,id',          // Район региона (обязательно)
            'city_id' => 'nullable|exists:cities,id',             // Город
            'district_id' => 'nullable|exists:districts,id',      // Район
            'zone_id' => 'nullable|exists:zones,id',              // Зона/ориентир
            'street_id' => 'nullable|exists:streets,id',          // Улица
            'building_number' => 'nullable|string|max:50',        // Номер дома
            'apartment_number' => 'nullable|string|max:50',       // Номер квартиры

            // Жилой комплекс
            'complex_id' => 'nullable|exists:complexes,id',   // ЖК
            'block_id' => 'nullable|exists:blocks,id',         // Секция/корпус ЖК

            // Числовые характеристики
            'area_total' => 'nullable|integer|min:0',      // Общая площадь (м²)
            'area_living' => 'nullable|integer|min:0',     // Жилая площадь (м²)
            'area_kitchen' => 'nullable|integer|min:0',    // Площадь кухни (м²)
            'area_land' => 'nullable|integer|min:0',       // Площадь участка (сотки)
            'floor' => 'nullable|integer|min:0',           // Этаж
            'floors_total' => 'nullable|integer|min:1',    // Этажность здания
            'year_built' => 'nullable|exists:dictionaries,id', // Год постройки
            'price' => 'nullable|integer|min:0',           // Цена
            'commission' => 'nullable|integer|min:0',      // Комиссия агента (%)

            // Настройки видимости
            'is_advertised' => 'nullable|boolean',             // Рекламировать объект
            'is_visible_to_agents' => 'nullable|boolean',      // Видимость для других агентов

            // Текстовые поля
            'youtube_url' => 'nullable|url|max:255',           // Ссылка на видео YouTube
            'title_ru' => 'nullable|string|max:255',           // Заголовок объекта
            'personal_notes' => 'nullable|string|max:5000',    // Личные заметки агента
            'agent_notes' => 'nullable|string|max:5000',       // Заметки для других агентов
            'description_ua' => 'nullable|string|max:10000',   // Описание (украинский)
            'description_ru' => 'nullable|string|max:10000',   // Описание (русский)
            'description_en' => 'nullable|string|max:10000',   // Описание (английский)

            // Передача объекта другому агенту
            'assigned_agent_id' => 'nullable|exists:employees,id',

            // Привязка контактов (клиентов) к объекту
            'contact_ids' => 'required|array|min:1',
            'contact_ids.*' => 'exists:contacts,id',

            // Загрузка документов (макс. 10 файлов по 5MB)
            'documents' => 'nullable|array|max:10',
            'documents.*' => 'file|max:5120',

            // Загрузка фотографий (макс. 20 фото по 10MB)
            'photos' => 'nullable|array|max:20',
            'photos.*' => 'file|mimes:jpeg,jpg,png,webp,heic,heif|max:10240',
        ];
    }

    /**
     * Применяет условные правила валидации на основе типа сделки:
     * - квартиры/комнаты → building_number и apartment_number обязательны
     * - дома → building_number обязателен
     */
    protected function applyDealTypeRules(array $rules): array
    {
        $dealTypeId = $this->input('deal_type_id');

        if ($dealTypeId) {
            $dealType = Dictionary::find($dealTypeId);
            $dealTypeName = $dealType ? mb_strtolower($dealType->name) : '';

            $isApartment = str_contains($dealTypeName, 'квартир')
                || str_contains($dealTypeName, 'комнат');
            $isHouse = str_contains($dealTypeName, 'домов');

            if ($isApartment) {
                $rules['building_number'] = 'required|string|max:50';
                $rules['apartment_number'] = 'required|string|max:50';
            } elseif ($isHouse) {
                $rules['building_number'] = 'required|string|max:50';
            }
        }

        return $rules;
    }

    /**
     * Кастомные сообщения об ошибках валидации на русском языке.
     */
    protected function baseMessages(): array
    {
        return [
            'deal_type_id.required' => 'Выберите тип сделки',
            'deal_type_id.exists' => 'Выбранный тип сделки не существует',
            'currency_id.required' => 'Выберите валюту',
            'currency_id.exists' => 'Выбранная валюта не существует',
            'city_id.exists' => 'Выбранный город не существует',
            'price.integer' => 'Цена должна быть целым числом',
            'price.min' => 'Цена не может быть отрицательной',
            'commission.integer' => 'Комиссия должна быть целым числом',
            'area_total.integer' => 'Площадь должна быть целым числом',
            'year_built.exists' => 'Выбранный год постройки не существует',
            'floors_total.integer' => 'Этажность должна быть целым числом',
            'youtube_url.url' => 'Введите корректную ссылку на YouTube',
            'contact_ids.required' => 'Необходимо добавить хотя бы один контакт',
            'contact_ids.min' => 'Необходимо добавить хотя бы один контакт',
            'contact_ids.array' => 'Неверный формат контактов',
            'contact_ids.*.exists' => 'Выбранный контакт не существует',
            'documents.array' => 'Неверный формат документов',
            'documents.max' => 'Максимум 10 документов',
            'documents.*.file' => 'Ошибка загрузки файла',
            'documents.*.max' => 'Максимальный размер файла 5MB',
            'photos.array' => 'Неверный формат фотографий',
            'photos.max' => 'Максимум 20 фотографий',
            'photos.*.file' => 'Ошибка загрузки фото',
            'photos.*.mimes' => 'Разрешены только: JPEG, PNG, WebP, HEIC',
            'photos.*.max' => 'Максимальный размер фото 10MB',
            'building_number.required' => 'Укажите номер дома',
            'apartment_number.required' => 'Укажите номер квартиры',
        ];
    }
}
