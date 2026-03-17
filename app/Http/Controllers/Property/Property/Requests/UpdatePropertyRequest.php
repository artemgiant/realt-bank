<?php

namespace App\Http\Controllers\Property\Property\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса на обновление объекта недвижимости.
 *
 * Использует общие правила из PropertyValidationRules.
 * Добавляет поля tiktok_url и external_url, которых нет при создании.
 */
class UpdatePropertyRequest extends FormRequest
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
     * Правила валидации для обновления объекта.
     * Расширяет базовые правила полями tiktok и external ссылок.
     */
    public function rules(): array
    {
        return array_merge($this->applyDealTypeRules($this->baseRules()), [
            'tiktok_url' => 'nullable|url|max:255',      // Ссылка на TikTok
            'external_url' => 'nullable|url|max:255',    // Внешняя ссылка
        ]);
    }

    /**
     * Сообщения об ошибках валидации.
     */
    public function messages(): array
    {
        return $this->baseMessages();
    }
}
