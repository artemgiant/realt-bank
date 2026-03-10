<?php

namespace App\Http\Controllers\Company\Employee\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса на обновление сотрудника.
 *
 * Извлечено из EmployeeController::update().
 * Отличия от Store: email необязателен и без unique, пароль не нужен, добавлен is_active.
 */
class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Проверка доступа — разрешено всем авторизованным пользователям.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для обновления сотрудника.
     */
    public function rules(): array
    {
        return [
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'birthday'    => 'nullable|date',
            'company_id'  => 'nullable|exists:companies,id',
            'office_id'   => 'nullable|exists:company_offices,id',
            'position_id' => 'nullable|exists:dictionaries,id',
            'status_id'   => 'nullable|exists:dictionaries,id',
            'tag_ids'     => 'nullable|array',
            'passport'    => 'nullable|string|max:50',
            'inn'         => 'nullable|string|max:20',
            'comment'     => 'nullable|string',
            'photo'       => 'nullable|image|max:5120',
            'active_until' => 'nullable|date',
            'is_active'   => 'boolean',
        ];
    }
}
