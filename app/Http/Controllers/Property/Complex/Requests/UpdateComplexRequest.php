<?php

namespace App\Http\Controllers\Property\Complex\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса на обновление комплекса.
 *
 * Использует общие правила из ComplexValidationRules.
 * Добавляет поля для удаления медиа и редактирования блоков (id, delete).
 */
class UpdateComplexRequest extends FormRequest
{
    use ComplexValidationRules;

    /** Авторизация — доступно всем авторизованным */
    public function authorize(): bool
    {
        return true;
    }

    /** Правила валидации для обновления комплекса */
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            // Удаление медиа (по индексу в JSON массиве)
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'integer',
            'delete_plans' => 'nullable|array',
            'delete_plans.*' => 'integer',

            // Блоки — дополнительные поля для обновления
            'blocks.*.id' => 'nullable|integer',
            'blocks.*.delete' => 'nullable|boolean',
        ]);
    }

    /** Сообщения об ошибках */
    public function messages(): array
    {
        return $this->baseMessages();
    }
}
