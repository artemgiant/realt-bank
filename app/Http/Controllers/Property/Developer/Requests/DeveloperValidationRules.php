<?php

namespace App\Http\Controllers\Property\Developer\Requests;

/**
 * Общие правила валидации для создания и обновления девелопера.
 *
 * Используется в StoreDeveloperRequest и UpdateDeveloperRequest.
 * Содержит базовые правила и сообщения об ошибках, которые идентичны для обоих операций.
 */
trait DeveloperValidationRules
{
    /**
     * Базовые правила валидации полей девелопера.
     * Общие для store и update.
     */
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
            'year_founded' => 'nullable|integer|min:1900|max:' . date('Y'),
            'company_website' => 'nullable|url|max:255',
            'materials_url' => 'nullable|url|max:255',
            'agent_notes' => 'nullable|string|max:5000',

            // Контакты
            'contact_ids' => 'nullable|array',
            'contact_ids.*' => 'exists:contacts,id',

            // Логотип
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ];
    }

    /**
     * Кастомные сообщения об ошибках валидации на русском языке.
     */
    protected function baseMessages(): array
    {
        return [
            'name_ua.max' => 'Название (UA) слишком длинное',
            'name_ru.max' => 'Название (RU) слишком длинное',
            'name_en.max' => 'Название (EN) слишком длинное',
            'year_founded.integer' => 'Год основания должен быть числом',
            'year_founded.min' => 'Год основания не может быть меньше 1900',
            'year_founded.max' => 'Год основания не может быть больше текущего года',
            'company_website.url' => 'Введите корректную ссылку',
            'materials_url.url' => 'Введите корректную ссылку',
            'logo.image' => 'Файл должен быть изображением',
            'logo.mimes' => 'Разрешены только: JPEG, PNG, WebP',
            'logo.max' => 'Максимальный размер логотипа 2MB',
        ];
    }
}
