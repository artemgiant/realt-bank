<?php

namespace App\Services\Migration\Mappers;

/**
 * Маппинг филиалов: старый filial_id из factor_dump → новый company_office_id в realt_bank.
 *
 * Также хранит ID компании "Factor" (одна компания для всех филиалов).
 * Заполняется в FilialMigrator, используется в UserMigrator для привязки сотрудника к офису.
 */
class FilialMapper
{
    // old filial_id → new company_office_id
    protected array $map = [];

    // ID компании "Factor" в новой базе (создаётся в FilialMigrator)
    protected ?int $companyId = null;

    public function setCompanyId(int $id): void
    {
        $this->companyId = $id;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function set(int $oldId, int $newId): void
    {
        $this->map[$oldId] = $newId;
    }

    public function get(?int $oldId): ?int
    {
        if (!$oldId) {
            return null;
        }
        return $this->map[$oldId] ?? null;
    }

    public function getStats(): array
    {
        return ['offices_mapped' => count($this->map)];
    }
}
