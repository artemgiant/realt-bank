<?php

namespace App\Services\Migration\Mappers;

/**
 * Маппинг пользователей: старый user_id из factor_dump → новый user_id в realt_bank.
 *
 * Заполняется в UserMigrator при создании каждого пользователя.
 * Используется в PropertyMigrator для привязки объекта к агенту.
 */
class UserMapper
{
    // old user_id → new user_id
    protected array $map = [];

    /**
     * Сохранить соответствие: старый ID → новый ID.
     */
    public function set(int $oldId, int $newId): void
    {
        $this->map[$oldId] = $newId;
    }

    /**
     * Получить новый user_id по старому. Null если не найден.
     */
    public function get(?int $oldId): ?int
    {
        if (!$oldId) {
            return null;
        }
        return $this->map[$oldId] ?? null;
    }

    public function getStats(): array
    {
        return ['users_mapped' => count($this->map)];
    }
}
