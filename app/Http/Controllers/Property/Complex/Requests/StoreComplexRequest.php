<?php

namespace App\Http\Controllers\Property\Complex\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса на создание комплекса.
 *
 * Использует общие правила из ComplexValidationRules.
 * Добавляет ограничения для создания: максимум 5 контактов, 10 фото, 10 планов.
 */
class StoreComplexRequest extends FormRequest
{
    use ComplexValidationRules;

    /** Авторизация — доступно всем авторизованным */
    public function authorize(): bool
    {
        return true;
    }

    /** Правила валидации для создания комплекса */
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'contact_ids' => 'nullable|array|max:5',
            'photos' => 'nullable|array|max:10',
            'plans' => 'nullable|array|max:10',
        ]);
    }

    /** Сообщения об ошибках */
    public function messages(): array
    {
        return $this->baseMessages();
    }
}
