<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $context = $this->input('context', 'properties');

        $baseRules = [
            'first_name' => 'required|string|max:255',
            'phones' => 'required|array|min:1',
            'phones.*.phone' => 'required|string|max:50',
            'phones.*.is_primary' => 'nullable|boolean',
            'contact_role' => 'nullable|array',
            'contact_role.*' => 'exists:dictionaries,id',
            'telegram' => 'nullable|string|max:255',
            'viber' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'passport' => 'nullable|string|max:50',
            'inn' => 'nullable|string|max:20',
            'comment' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|max:2048',
            'birthday' => 'nullable|date',
            'property_id' => 'nullable|exists:properties,id',
            'context' => 'nullable|string|in:properties,companies,complexes,developers',
        ];

        return match ($context) {
            'properties' => array_merge($baseRules, [
                'last_name' => 'nullable|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'roles' => 'required|array|min:1',
                'roles.*' => 'exists:dictionaries,id',
                'tags' => 'nullable|string|max:500',
            ]),
            'companies' => array_merge($baseRules, [
                'last_name' => 'nullable|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'exists:dictionaries,id',
            ]),
            default => array_merge($baseRules, [
                'last_name' => 'nullable|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'tags' => 'nullable|string|max:500',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:dictionaries,id',
            ]),
        };
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Введите имя контакта',
            'last_name.required' => 'Введите фамилию контакта',
            'middle_name.required' => 'Введите отчество контакта',
            'email.required' => 'Введите email контакта',
            'email.email' => 'Введите корректный email',
            'tags.required' => 'Выберите тег',
            'roles.required' => 'Выберите роли контакта',
            'roles.min' => 'Выберите хотя бы одну роль',
            'phones.required' => 'Добавьте хотя бы один телефон',
            'phones.min' => 'Добавьте хотя бы один телефон',
            'phones.*.phone.required' => 'Введите номер телефона',
            'photo.image' => 'Файл должен быть изображением',
            'photo.max' => 'Максимальный размер фото 2MB',
        ];
    }
}
