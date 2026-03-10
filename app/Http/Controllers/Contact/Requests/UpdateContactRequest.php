<?php

namespace App\Http\Controllers\Contact\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса на обновление контакта.
 *
 * Извлечено из ContactController::ajaxUpdate.
 * Содержит правила для всех полей формы редактирования контакта.
 */
class UpdateContactRequest extends FormRequest
{
    /**
     * Проверка доступа — разрешено всем авторизованным пользователям.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для обновления контакта.
     */
    public function rules(): array
    {
        return [
            'company_id'         => 'required|exists:companies,id',
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'nullable|string|max:255',
            'middle_name'        => 'nullable|string|max:255',
            'email'              => 'nullable|email|max:255',
            'tags'               => 'nullable|string|max:500',
            'telegram'           => 'nullable|string|max:255',
            'viber'              => 'nullable|string|max:255',
            'whatsapp'           => 'nullable|string|max:255',
            'passport'           => 'nullable|string|max:50',
            'inn'                => 'nullable|string|max:20',
            'comment'            => 'nullable|string|max:2000',
            'photo'              => 'nullable|image|max:2048',
            'remove_photo'       => 'nullable|boolean',
            'phones'             => 'required|array|min:1',
            'phones.*.phone'     => 'required|string|max:50',
            'phones.*.is_primary' => 'nullable|boolean',
            'roles'              => 'required|array|min:1',
            'roles.*'            => 'exists:dictionaries,id',
        ];
    }

    /**
     * Сообщения об ошибках валидации на русском языке.
     */
    public function messages(): array
    {
        return [
            'first_name.required'      => 'Введите имя контакта',
            'last_name.required'       => 'Введите фамилию контакта',
            'middle_name.required'     => 'Введите отчество контакта',
            'email.required'           => 'Введите email контакта',
            'email.email'              => 'Введите корректный email',
            'tags.required'            => 'Выберите тег',
            'roles.required'           => 'Выберите роли контакта',
            'roles.min'                => 'Выберите хотя бы одну роль',
            'phones.required'          => 'Добавьте хотя бы один телефон',
            'phones.min'               => 'Добавьте хотя бы один телефон',
            'phones.*.phone.required'  => 'Введите номер телефона',
            'photo.image'              => 'Файл должен быть изображением',
            'photo.max'                => 'Максимальный размер фото 2MB',
        ];
    }
}
