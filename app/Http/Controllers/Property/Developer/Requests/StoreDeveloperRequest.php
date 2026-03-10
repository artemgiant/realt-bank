<?php

namespace App\Http\Controllers\Property\Developer\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса на создание девелопера.
 *
 * Использует общие правила из DeveloperValidationRules.
 */
class StoreDeveloperRequest extends FormRequest
{
    use DeveloperValidationRules;

    /**
     * Проверка доступа — разрешено всем авторизованным пользователям.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для создания девелопера.
     */
    public function rules(): array
    {
        return $this->baseRules();
    }

    /**
     * Сообщения об ошибках валидации.
     */
    public function messages(): array
    {
        return $this->baseMessages();
    }
}
