<?php

namespace App\Services\Migration;

use App\Services\Migration\Mappers\BlockMapper;
use App\Services\Migration\Mappers\ComplexMapper;
use App\Services\Migration\Mappers\DictionaryMapper;
use App\Services\Migration\Mappers\FilialMapper;
use App\Services\Migration\Mappers\LocationMapper;
use App\Services\Migration\Mappers\UserMapper;
use App\Services\Migration\Migrators\ContactMigrator;
use App\Services\Migration\Migrators\FilialMigrator;
use App\Services\Migration\Migrators\PropertyMigrator;
use App\Services\Migration\Migrators\UserMigrator;
use Illuminate\Console\OutputStyle;

/**
 * Оркестратор миграции данных из factor_dump в realt_bank.
 *
 * Управляет порядком выполнения:
 * 1. Строит маперы (локации, справочники, ЖК)
 * 2. Филиалы → компания + офисы
 * 3. Пользователи → users + employees
 * 4. Объекты → properties + features + translations + contacts
 * 5. Фото → property_photos
 * 6. Отчёт о немаппящихся полях
 *
 * Поддерживает выборочный запуск через параметр $only.
 */
class DataMigrationService
{
    protected ?OutputStyle $output;
    protected int $chunkSize;
    protected int $limit;  // лимит объектов для тестового запуска (0 = все)

    // Маперы — хранят соответствие old ID → new ID
    protected LocationMapper $locationMapper;      // города, районы, зоны, улицы
    protected DictionaryMapper $dictionaryMapper;   // справочники (тип здания, состояние и т.д.)
    protected UserMapper $userMapper;               // old user_id → new user_id
    protected FilialMapper $filialMapper;           // old filial_id → new office_id
    protected ComplexMapper $complexMapper;         // old complex_id → new complex_id
    protected BlockMapper $blockMapper;             // old complex_id → new block_id + complex_id

    // Результаты миграции по каждой сущности
    protected array $results = [];

    public function __construct(?OutputStyle $output = null, int $chunkSize = 500, int $limit = 0)
    {
        $this->output = $output;
        $this->chunkSize = $chunkSize;
        $this->limit = $limit;

        // Инициализируем маперы (данные загрузятся в buildMappers())
        $this->locationMapper = new LocationMapper();
        $this->dictionaryMapper = new DictionaryMapper();
        $this->userMapper = new UserMapper();
        $this->filialMapper = new FilialMapper();
        $this->complexMapper = new ComplexMapper();
        $this->blockMapper = new BlockMapper();
    }

    /**
     * Запуск полной миграции.
     *
     * @param array|null $only Если указан, переносим только выбранные сущности:
     *                         ['filials', 'users', 'properties', 'photos']
     * @return array Статистика по каждой сущности + общее время
     */
    public function migrate(?array $only = null): array
    {
        $startTime = microtime(true);

        // Шаг 1: Загружаем маперы (соответствие старых ID новым)
        $this->output?->section('Building mappers...');
        $this->buildMappers();

        // Шаг 2: Филиалы → Компания "Factor" + офисы
        if ($this->shouldRun('filials', $only)) {
            $this->output?->section('Migrating filials...');
            $migrator = new FilialMigrator($this->filialMapper, $this->output);
            $this->results['filials'] = $migrator->migrate();
        }

        // Шаг 3: Пользователи → users + employees + роли
        if ($this->shouldRun('users', $only)) {
            $this->output?->section('Migrating users...');
            $migrator = new UserMigrator($this->userMapper, $this->filialMapper, $this->output);
            $this->results['users'] = $migrator->migrate();
        }

        // Шаг 4: Объекты → properties + features + translations + contacts
        $propertyMap = [];
        if ($this->shouldRun('properties', $only)) {
            $this->output?->section('Migrating properties...');
            $contactMigrator = new ContactMigrator();
            $migrator = new PropertyMigrator(
                $this->locationMapper,
                $this->dictionaryMapper,
                $this->userMapper,
                $this->complexMapper,
                $this->blockMapper,
                $contactMigrator,
                $this->output,
                $this->chunkSize,
                $this->limit
            );
            $this->results['properties'] = $migrator->migrate();
            $propertyMap = $migrator->getPropertyMap();

            // Статистика контактов
            $this->results['contacts'] = $contactMigrator->getStats();
        }

        // Шаг 5: Отчёт о немаппящихся полях
        $this->output?->section('Generating unmapped fields report...');
        $report = new UnmappedFieldsReport();
        $reportPath = $report->generate();
        $this->output?->info("Unmapped fields report: {$reportPath}");
        $this->results['unmapped_report'] = $reportPath;

        $duration = round(microtime(true) - $startTime, 2);
        $this->results['duration_seconds'] = $duration;

        return $this->results;
    }

    /**
     * Загрузка маперов: читаем старые справочники и локации,
     * находим соответствия в новой базе по имени.
     */
    protected function buildMappers(): void
    {
        // Локации: lib_towns→cities, lib_regions→districts, lib_zones→zones, lib_streets→streets
        $this->output?->info('Building location mapper...');
        $this->locationMapper->build();
        $locStats = $this->locationMapper->getStats();
        $this->output?->info("  Cities: {$locStats['cities']}, Districts: {$locStats['districts']}, Zones: {$locStats['zones']}, Streets: {$locStats['streets']}");

        // Справочники: lib_other → dictionaries (тип здания, состояние, стены и т.д.)
        $this->output?->info('Building dictionary mapper...');
        $this->dictionaryMapper->build();
        $dictStats = $this->dictionaryMapper->getStats();
        $this->output?->info("  Mapped: {$dictStats['mapped']} / {$dictStats['total_old_items']} items");

        // ЖК: lib_other (complex) → complexes
        $this->output?->info('Building complex mapper...');
        $this->complexMapper->build();
        $complexStats = $this->complexMapper->getStats();
        $this->output?->info("  Complexes: {$complexStats['mapped']} mapped / {$complexStats['total_old']} total");

        // Блоки: lib_other (complex) → blocks + complexes
        $this->output?->info('Building block mapper...');
        $this->blockMapper->build();
        $blockStats = $this->blockMapper->getStats();
        $this->output?->info("  Blocks: {$blockStats['mapped']} mapped, {$blockStats['ignored']} ignored, {$blockStats['pending_to_create']} to create, {$blockStats['unmapped']} unmapped");
    }

    protected function shouldRun(string $entity, ?array $only): bool
    {
        return $only === null || in_array($entity, $only);
    }
}
