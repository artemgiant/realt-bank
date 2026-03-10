<?php

namespace App\Http\Controllers\Property\Complex\Requests;

/**
 * Общие правила валидации для создания и обновления комплекса.
 *
 * Трейт используется в StoreComplexRequest и UpdateComplexRequest,
 * чтобы избежать дублирования ~90 строк одинаковых правил.
 */
trait ComplexValidationRules
{
    /** Базовые правила валидации полей комплекса */
    protected function baseRules(): array
    {
        return [
            // Названия (мультиязычные)
            'name_ua' => 'nullable|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',

            // Описания (мультиязычные)
            'description_ua' => 'nullable|string|max:10000',
            'description_ru' => 'nullable|string|max:10000',
            'description_en' => 'nullable|string|max:10000',

            // Основные поля
            'developer_id' => 'nullable|exists:developers,id',
            'website' => 'nullable|url|max:255',
            'company_website' => 'nullable|url|max:255',
            'materials_url' => 'nullable|url|max:255',
            'agent_notes' => 'nullable|string|max:5000',
            'special_conditions' => 'nullable|string|max:5000',

            // Мульти-выбор (JSON массивы)
            'housing_classes' => 'nullable|array',
            'housing_classes.*' => 'exists:dictionaries,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:dictionaries,id',
            'object_types' => 'nullable|array',
            'object_types.*' => 'exists:dictionaries,id',

            // Локация
            'city_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'zone_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',

            // Контакты
            'contact_ids' => 'nullable|array',
            'contact_ids.*' => 'exists:contacts,id',

            // Файлы
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'plans' => 'nullable|array',
            'plans.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',

            // Площадь и цена
            'area_from' => 'nullable|numeric|min:0',
            'area_to' => 'nullable|numeric|min:0',
            'price_per_m2' => 'nullable|numeric|min:0',
            'price_total' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:USD,UAH,EUR',

            // Количество объектов
            'objects_count' => 'nullable|integer|min:0',

            // Состояния и особенности
            'conditions' => 'nullable|array',
            'conditions.*' => 'exists:dictionaries,id',
            'features' => 'nullable|array',
            'features.*' => 'exists:dictionaries,id',

            // Блоки
            'blocks' => 'nullable|array',
            'blocks.*.name' => 'nullable|string|max:255',
            'blocks.*.street_id' => 'nullable|integer',
            'blocks.*.building_number' => 'nullable|string|max:50',
            'blocks.*.floors_total' => 'nullable|integer|min:1|max:200',
            'blocks.*.year_built' => 'nullable|integer|min:1900|max:2100',
            'blocks.*.heating_type_id' => 'nullable|exists:dictionaries,id',
            'blocks.*.wall_type_id' => 'nullable|exists:dictionaries,id',
        ];
    }

    /** Сообщения об ошибках валидации на русском */
    protected function baseMessages(): array
    {
        return [
            'name_ua.max' => 'Назва (UA) занадто довга',
            'name_ru.max' => 'Название (RU) слишком длинное',
            'name_en.max' => 'Name (EN) is too long',
            'website.url' => 'Введите корректную ссылку на сайт комплекса',
            'company_website.url' => 'Введите корректную ссылку на сайт компании',
            'materials_url.url' => 'Введите корректную ссылку на материалы',
            'logo.image' => 'Логотип должен быть изображением',
            'logo.mimes' => 'Разрешены только: JPEG, PNG, WebP',
            'logo.max' => 'Максимальный размер логотипа 2MB',
            'photos.*.image' => 'Файл должен быть изображением',
            'photos.*.max' => 'Максимальный размер фото 5MB',
            'plans.*.image' => 'План должен быть изображением',
            'plans.*.max' => 'Максимальный размер плана 5MB',
            'contact_ids.max' => 'Максимум 5 контактов',
            'photos.max' => 'Максимум 10 фото',
            'plans.max' => 'Максимум 10 планов',
        ];
    }
}
