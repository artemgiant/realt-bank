<?php

namespace App\Http\Controllers\Property\Property\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса на создание объекта недвижимости.
 *
 * Использует общие правила из PropertyValidationRules.
 * Добавляет специфичное сообщение для документов.
 */
class StorePropertyRequest extends FormRequest
{
    use PropertyValidationRules;

    /**
     * Проверка доступа — разрешено всем авторизованным пользователям.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для создания объекта.
     */
    public function rules(): array
    {
        return $this->applyDealTypeRules($this->baseRules());
    }

    /**
     * Сообщения об ошибках валидации.
     */
    public function messages(): array
    {
        return array_merge($this->baseMessages(), [
            'documents.*.mimes' => 'Разрешены только файлы: PNG, JPEG, PDF',
        ]);
    }
}
