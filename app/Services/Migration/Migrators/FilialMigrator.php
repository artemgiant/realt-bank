<?php

namespace App\Services\Migration\Migrators;

use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use App\Services\Migration\Mappers\FilialMapper;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\DB;

/**
 * Мигратор филиалов: factor_dump.filials → companies + company_offices.
 *
 * Шаг 1 миграции (должен идти первым — от него зависят employees).
 * Создаёт одну компанию "Factor" и переносит все филиалы как офисы этой компании.
 */
class FilialMigrator
{
    protected FilialMapper $mapper;
    protected ?OutputStyle $output;

    public function __construct(FilialMapper $mapper, ?OutputStyle $output = null)
    {
        $this->mapper = $mapper;
        $this->output = $output;
    }

    /**
     * Перенос филиалов.
     * 1. Создаёт компанию "Factor"
     * 2. Каждый filial → company_office привязанный к этой компании
     * 3. Сохраняет маппинг old filial_id → new office_id в FilialMapper
     */
    public function migrate(): array
    {
        $stats = ['created' => 0, 'errors' => 0];

        // Создаём родительскую компанию (одна на все филиалы)
        $company = Company::create([
            'name' => 'Factor',
            'slug' => 'factor',
            'is_active' => true,
        ]);
        $this->mapper->setCompanyId($company->id);

        $this->output?->info("Company 'Factor' created (id: {$company->id})");

        // Migrate filials → company_offices
        $filials = DB::connection('factor_dump')->table('filials')->get();

        foreach ($filials as $filial) {
            try {
                $office = CompanyOffice::create([
                    'company_id' => $company->id,
                    'name' => $filial->name ?? 'Офис #' . $filial->id,
                    'is_active' => true,
                    'sort_order' => $filial->id,
                ]);

                $this->mapper->set($filial->id, $office->id);
                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['errors']++;
                $this->output?->error("Filial #{$filial->id}: {$e->getMessage()}");
            }
        }

        $this->output?->info("Offices migrated: {$stats['created']}, errors: {$stats['errors']}");

        return $stats;
    }
}
