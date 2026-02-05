<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // ========== Основні зв'язки ==========
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'source_id' => ['nullable', 'exists:sources,id'],
            'currency_id' => ['required', 'exists:currencies,id'],

            // ========== Комплекс ==========
            'complex_id' => ['nullable', 'exists:complexes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],

            // ========== Локація ==========
            'country_id' => ['nullable', 'exists:countries,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'street_id' => ['nullable', 'exists:streets,id'],
            'building_number' => ['nullable', 'string', 'max:50'],
            'apartment_number' => ['nullable', 'string', 'max:50'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            // ========== Довідники ==========
            'deal_type_id' => ['required', 'exists:dictionaries,id'],
            'deal_kind_id' => ['nullable', 'exists:dictionaries,id'],
            'building_type_id' => ['nullable', 'exists:dictionaries,id'],
            'property_type_id' => ['nullable', 'exists:dictionaries,id'],
            'condition_id' => ['nullable', 'exists:dictionaries,id'],
            'wall_type_id' => ['required', 'exists:dictionaries,id'],
            'heating_type_id' => ['nullable', 'exists:dictionaries,id'],
            'room_count_id' => ['nullable', 'exists:dictionaries,id'],
            'bathroom_count_id' => ['nullable', 'exists:dictionaries,id'],
            'ceiling_height_id' => ['nullable', 'exists:dictionaries,id'],

            // ========== Характеристики ==========
            'area_total' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'area_living' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'area_kitchen' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'area_land' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'floor' => ['nullable', 'integer', 'min:-5', 'max:200'],
            'floors_total' => ['nullable', 'integer', 'min:1', 'max:200'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 10)],

            // ========== Ціна ==========
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'commission' => ['nullable', 'string', 'max:100'],
            'commission_type' => ['nullable', 'in:percent,fixed'],

            // ========== Медіа ==========
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'external_url' => ['nullable', 'url', 'max:255'],

            // ========== Налаштування ==========
            'is_visible_to_agents' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'agent_notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'in:draft,active,inactive,sold,rented'],

            // ========== Переклади ==========
            'title_ua' => ['nullable', 'string', 'max:255'],
            'title_ru' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description_ua' => ['nullable', 'string', 'max:10000'],
            'description_ru' => ['nullable', 'string', 'max:10000'],
            'description_en' => ['nullable', 'string', 'max:10000'],

            // ========== Особливості ==========
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:dictionaries,id'],

            // ========== Файли ==========
            'photos' => ['nullable', 'array', 'max:20'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp,heic', 'max:10240'],
            'documents' => ['nullable', 'array', 'max:10'],
            'documents.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpeg,png,jpg', 'max:20480'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'contact_id' => 'контакт',
            'source_id' => 'джерело',
            'currency_id' => 'валюта',
            'complex_id' => 'комплекс',
            'section_id' => 'секція',
            'country_id' => 'країна',
            'region_id' => 'регіон',
            'city_id' => 'місто',
            'district_id' => 'район',
            'zone_id' => 'зона',
            'street_id' => 'вулиця',
            'building_number' => 'номер будинку',
            'apartment_number' => 'номер квартири',
            'location_name' => 'назва локації',
            'latitude' => 'широта',
            'longitude' => 'довгота',
            'deal_type_id' => 'тип угоди',
            'deal_kind_id' => 'вид угоди',
            'building_type_id' => 'тип будівлі',
            'property_type_id' => 'тип нерухомості',
            'condition_id' => 'стан',
            'wall_type_id' => 'тип стін',
            'heating_type_id' => 'опалення',
            'room_count_id' => 'кількість кімнат',
            'bathroom_count_id' => 'кількість ванних',
            'ceiling_height_id' => 'висота стелі',
            'area_total' => 'загальна площа',
            'area_living' => 'житлова площа',
            'area_kitchen' => 'площа кухні',
            'area_land' => 'площа ділянки',
            'floor' => 'поверх',
            'floors_total' => 'поверховість',
            'year_built' => 'рік побудови',
            'price' => 'ціна',
            'commission' => 'комісія',
            'commission_type' => 'тип комісії',
            'youtube_url' => 'посилання YouTube',
            'external_url' => 'зовнішнє посилання',
            'is_visible_to_agents' => 'видимість для агентів',
            'notes' => 'нотатки',
            'agent_notes' => 'нотатки агента',
            'status' => 'статус',
            'title_ua' => 'заголовок (UA)',
            'title_ru' => 'заголовок (RU)',
            'title_en' => 'заголовок (EN)',
            'description_ua' => 'опис (UA)',
            'description_ru' => 'опис (RU)',
            'description_en' => 'опис (EN)',
            'features' => 'особливості',
            'photos' => 'фото',
            'photos.*' => 'фото',
            'documents' => 'документи',
            'documents.*' => 'документ',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'required' => 'Поле :attribute є обов\'язковим.',
            'exists' => 'Вибране значення :attribute недійсне.',
            'numeric' => 'Поле :attribute має бути числом.',
            'integer' => 'Поле :attribute має бути цілим числом.',
            'string' => 'Поле :attribute має бути текстом.',
            'max' => [
                'string' => 'Поле :attribute не може перевищувати :max символів.',
                'numeric' => 'Поле :attribute не може бути більше :max.',
                'file' => 'Файл :attribute не може бути більше :max кілобайт.',
                'array' => 'Поле :attribute не може містити більше :max елементів.',
            ],
            'min' => [
                'numeric' => 'Поле :attribute має бути не менше :min.',
            ],
            'between' => [
                'numeric' => 'Поле :attribute має бути між :min та :max.',
            ],
            'url' => 'Поле :attribute має бути дійсним URL.',
            'in' => 'Вибране значення :attribute недійсне.',
            'boolean' => 'Поле :attribute має бути true або false.',
            'image' => 'Файл :attribute має бути зображенням.',
            'mimes' => 'Файл :attribute має бути типу: :values.',
            'array' => 'Поле :attribute має бути масивом.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Конвертуємо checkbox в boolean
        $this->merge([
            'is_visible_to_agents' => $this->boolean('is_visible_to_agents'),
        ]);

        // Очищаємо пусті значення площ
        $numericFields = ['area_total', 'area_living', 'area_kitchen', 'area_land', 'price'];
        foreach ($numericFields as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }
}
