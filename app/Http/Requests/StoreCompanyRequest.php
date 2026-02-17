<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ========== Мультиязычные названия ==========
            'name_ua' => ['nullable', 'string', 'max:255'],
            'name_ru' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],

            // ========== Мультиязычные описания ==========
            'description_ua' => ['nullable', 'string', 'max:10000'],
            'description_ru' => ['nullable', 'string', 'max:10000'],
            'description_en' => ['nullable', 'string', 'max:10000'],

            // ========== Локация компании ==========
            'country_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'street_id' => ['nullable', 'exists:streets,id'],
            'building_number' => ['nullable', 'string', 'max:50'],
            'office_number' => ['nullable', 'string', 'max:50'],

            // ========== Основные поля ==========
            'website' => ['nullable', 'url', 'max:255'],
            'edrpou_code' => ['required', 'string', 'max:20'],
            'company_type' => ['required', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],

            // ========== Контакты компании ==========
            'contact_ids' => ['required', 'array', 'min:1'],
            'contact_ids.*' => ['exists:contacts,id'],

            // ========== Офисы ==========
            'offices' => ['required', 'array', 'min:1'],
            'offices.*.name_ua' => ['nullable', 'string', 'max:255'],
            'offices.*.name_ru' => ['nullable', 'string', 'max:255'],
            'offices.*.name_en' => ['nullable', 'string', 'max:255'],
            'offices.*.country_id' => ['nullable', 'exists:countries,id'],
            'offices.*.state_id' => ['nullable', 'exists:states,id'],
            'offices.*.city_id' => ['nullable', 'exists:cities,id'],
            'offices.*.district_id' => ['nullable', 'exists:districts,id'],
            'offices.*.zone_id' => ['nullable', 'exists:zones,id'],
            'offices.*.street_id' => ['nullable', 'exists:streets,id'],
            'offices.*.building_number' => ['nullable', 'string', 'max:50'],
            'offices.*.office_number' => ['nullable', 'string', 'max:50'],
            'offices.*.contact_ids' => ['nullable', 'array'],
            'offices.*.contact_ids.*' => ['exists:contacts,id'],
            'offices.*.photos' => ['nullable', 'array'],
            'offices.*.photos.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_ua' => 'название (UA)',
            'name_ru' => 'название (RU)',
            'name_en' => 'название (EN)',
            'description_ua' => 'описание (UA)',
            'description_ru' => 'описание (RU)',
            'description_en' => 'описание (EN)',
            'country_id' => 'страна',
            'state_id' => 'регион',
            'city_id' => 'город',
            'district_id' => 'район',
            'zone_id' => 'зона',
            'street_id' => 'улица',
            'building_number' => 'номер дома',
            'office_number' => 'номер офиса',
            'website' => 'сайт агентства',
            'edrpou_code' => 'код ЕГРПОУ/ИНН',
            'company_type' => 'тип агентства',
            'logo' => 'логотип',
            'contact_ids' => 'контакты',
            'offices' => 'офисы',
            'offices.*.name_ua' => 'название офиса (UA)',
            'offices.*.name_ru' => 'название офиса (RU)',
            'offices.*.name_en' => 'название офиса (EN)',
            'offices.*.photos' => 'фото офиса',
        ];
    }

    public function messages(): array
    {
        return [
            'name_ua.max' => 'Название (UA) слишком длинное',
            'name_ru.max' => 'Название (RU) слишком длинное',
            'name_en.max' => 'Название (EN) слишком длинное',
            'website.url' => 'Введите корректную ссылку на сайт',
            'logo.image' => 'Файл должен быть изображением',
            'logo.mimes' => 'Разрешены только: JPEG, PNG, WebP',
            'logo.max' => 'Максимальный размер логотипа 2MB',
            'edrpou_code.required' => 'Укажите код ЕГРПОУ/ИНН',
            'company_type.required' => 'Выберите тип агентства',
            'company_type.in' => 'Выберите корректный тип агентства',
            'contact_ids.required' => 'Необходимо добавить хотя бы один контакт',
            'contact_ids.min' => 'Необходимо добавить хотя бы один контакт',
            'offices.required' => 'Необходимо добавить хотя бы один офис',
            'offices.min' => 'Необходимо добавить хотя бы один офис',
            'offices.*.photos.*.image' => 'Файл должен быть изображением',
            'offices.*.photos.*.mimes' => 'Разрешены только: JPEG, PNG, WebP',
            'offices.*.photos.*.max' => 'Максимальный размер фото 5MB',
        ];
    }

    /**
     * Валидация: хотя бы одно название должно быть заполнено + минимум 1 офис с названием
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Проверка названия компании
            $nameUa = $this->input('name_ua');
            $nameRu = $this->input('name_ru');
            $nameEn = $this->input('name_en');

            if (empty($nameUa) && empty($nameRu) && empty($nameEn)) {
                $validator->errors()->add('name_ru', 'Укажите название хотя бы на одном языке');
            }

            // Проверка наличия хотя бы одного офиса с названием
            $offices = $this->input('offices', []);
            $validOfficeCount = 0;

            if (is_array($offices)) {
                foreach ($offices as $office) {
                    $officeNameUa = $office['name_ua'] ?? null;
                    $officeNameRu = $office['name_ru'] ?? null;
                    $officeNameEn = $office['name_en'] ?? null;

                    if (!empty($officeNameUa) || !empty($officeNameRu) || !empty($officeNameEn)) {
                        $validOfficeCount++;
                    }
                }
            }

            if ($validOfficeCount === 0) {
                $validator->errors()->add('offices', 'Необходимо добавить хотя бы один офис с названием');
            }
        });
    }
}
