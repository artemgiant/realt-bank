<?php

namespace App\Services\Migration;

use App\Services\Migration\Mappers\DictionaryMapper;
use App\Services\Migration\Mappers\FilialMapper;
use App\Services\Migration\Mappers\LocationMapper;
use App\Services\Migration\Mappers\UserMapper;
use App\Services\Migration\Migrators\FilialMigrator;
use App\Services\Migration\Migrators\PropertyMigrator;
use App\Services\Migration\Migrators\PropertyPhotoMigrator;
use App\Services\Migration\Migrators\UserMigrator;
use Illuminate\Console\OutputStyle;

/**
 * Оркестратор миграции данных из factor_dump в realt_bank.
 *
 * Управляет порядком выполнения:
 * 1. Строит маперы (локации, справочники)
 * 2. Филиалы → компания + офисы
 * 3. Пользователи → users + employees
 * 4. Объекты → properties + features
 * 5. Фото → property_photos
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
        // Должен идти первым — от него зависит привязка сотрудников к офисам
        if ($this->shouldRun('filials', $only)) {
            $this->output?->section('Migrating filials...');
            $migrator = new FilialMigrator($this->filialMapper, $this->output);
            $this->results['filials'] = $migrator->migrate();
        }

        // Шаг 3: Пользователи → users + employees + роли
        // Должен идти после филиалов — employees привязываются к офисам
        if ($this->shouldRun('users', $only)) {
            $this->output?->section('Migrating users...');
            $migrator = new UserMigrator($this->userMapper, $this->filialMapper, $this->output);
            $this->results['users'] = $migrator->migrate();
        }

        // Шаг 4: Объекты → properties + features
        // Только status IN(1,2,3), rent=0, deleted=0 (квартиры, дома, коммерция)
        $propertyMap = [];
        if ($this->shouldRun('properties', $only)) {
            $this->output?->section('Migrating properties...');
            $migrator = new PropertyMigrator(
                $this->locationMapper,
                $this->dictionaryMapper,
                $this->userMapper,
                $this->output,
                $this->chunkSize,
                $this->limit
            );
            $this->results['properties'] = $migrator->migrate();
            // Маппинг old object_id → new property_id (нужен для фото)
            $propertyMap = $migrator->getPropertyMap();
        }

        // Шаг 5: Фото объектов → property_photos
        // Переносятся только фото для объектов из propertyMap
        if ($this->shouldRun('photos', $only) && !empty($propertyMap)) {
            $this->output?->section('Migrating photos...');
            $migrator = new PropertyPhotoMigrator($propertyMap, $this->output, $this->chunkSize * 2);
            $this->results['photos'] = $migrator->migrate();
        }

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
    }

    /**
     * Проверка: нужно ли запускать миграцию для данной сущности.
     * Если --only не указан, запускаем все.
     */
    protected function shouldRun(string $entity, ?array $only): bool
    {
        return $only === null || in_array($entity, $only);
    }
}
