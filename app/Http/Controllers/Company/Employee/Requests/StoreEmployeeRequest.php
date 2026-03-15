<?php

namespace App\Http\Controllers\Company\Employee\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса на создание сотрудника.
 *
 * Извлечено из EmployeeController::store().
 * Правила: обязательные имя/фамилия/email/пароль, опциональные справочники и фото.
 */
class StoreEmployeeRequest extends FormRequest
{
    /**
     * Проверка доступа — разрешено всем авторизованным пользователям.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для создания сотрудника.
     */
    public function rules(): array
    {
        return [
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email',
            'password'    => 'required|string|min:8',
            'phone'       => 'required|string|max:50|unique:users,phone',
            'birthday'    => 'nullable|date',
            'company_id'  => 'nullable|exists:companies,id',
            'office_id'   => 'nullable|exists:company_offices,id',
            'position_id' => 'required|exists:dictionaries,id',
            'status_id'   => 'nullable|exists:dictionaries,id',
            'tag_ids'     => 'nullable|array',
            'passport'    => 'nullable|string|max:50',
            'inn'         => 'nullable|string|max:20',
            'comment'     => 'nullable|string',
            'photo'       => 'nullable|image|max:5120',
            'active_until' => 'nullable|date',
        ];
    }
}
