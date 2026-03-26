<?php

namespace App\Console\Commands;

use App\Services\Migration\CleanupService;
use App\Services\Migration\DataMigrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Artisan-команда для переноса данных из старой CRM (factor_dump) в новую (realt_bank).
 *
 * ./vendor/bin/sail artisan app:migrate-from-factor-dump --fresh --force --limit=10
 *
 * Примеры запуска:
 *   sail artisan app:migrate-from-factor-dump --fresh --force    // очистка + полная миграция
 *   sail artisan app:migrate-from-factor-dump                    // миграция без очистки
 *   sail artisan app:migrate-from-factor-dump --only=users           // только пользователи
 *   sail artisan app:migrate-from-factor-dump --chunk=200        // меньший размер пакета
 *   sail artisan app:migrate-from-factor-dump --limit=10         // тестовый запуск: только 10 объектов
 *
 * Порядок: пользователи → объекты → сидеры (CompanySeeder + AdminUserSeeder)
 * Подробнее: app/Services/Migration/README.md
 */
class MigrateFromFactorDump extends Command
{
    protected $signature = 'app:migrate-from-factor-dump
                            {--fresh : Очистить целевые таблицы перед переносом (через CleanupService)}
                            {--force : Пропустить подтверждения}
                            {--only= : Только указанные сущности: users,properties}
                            {--chunk=500 : Размер пакета для batch-обработки}
                            {--limit=0 : Лимит объектов для тестового запуска (0 = все)}';

    protected $description = 'Перенос данных из factor_dump (старая CRM) в realt_bank (новая CRM)';

    public function handle(): int
    {
        $this->info('=== Factor Dump → Realt Bank Migration ===');
        $this->newLine();

        // Проверяем подключение к старой базе
        try {
            $count = DB::connection('factor_dump')->table('objects')->count();
            $this->info("factor_dump: {$count} objects found");
        } catch (\Throwable $e) {
            $this->error("Cannot connect to factor_dump: {$e->getMessage()}");
            return self::FAILURE;
        }

        // --fresh: очистить целевые таблицы через CleanupService
        // Сохраняет: справочники, локации, определения ролей
        // Удаляет: properties, contacts, users, employees, companies, offices
        if ($this->option('fresh')) {
            $this->warn('Будут очищены целевые таблицы (users, employees, properties, contacts, companies, offices)');

            if (!$this->option('force') && !$this->confirm('Очистить эти таблицы и перенести данные заново?')) {
                $this->info('Отменено.');
                return self::FAILURE;
            }

            $cleanup = new CleanupService($this->output);
            $cleanup->cleanup();
            $this->info('Целевые таблицы очищены.');
            $this->newLine();
        }

        // --only: выборочный перенос (например --only=properties,photos)
        $only = null;
        if ($this->option('only')) {
            $only = array_map('trim', explode(',', $this->option('only')));
            $this->info('Переносим только: ' . implode(', ', $only));
        }

        $chunkSize = (int) $this->option('chunk');
        $limit = (int) $this->option('limit');

        if ($limit > 0) {
            $this->warn("Тестовый режим: лимит {$limit} объектов");
        }

        // Запуск миграции через оркестратор DataMigrationService
        $service = new DataMigrationService($this->output, $chunkSize, $limit);
        $results = $service->migrate($only);

        // Выводим итоговый отчёт
        $this->newLine();
        $this->info('=== Отчёт ===');

        $rows = [];
        foreach ($results as $key => $value) {
            if ($key === 'duration_seconds') {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $metric => $count) {
                    $rows[] = [$key, $metric, $count];
                }
            }
        }

        $this->table(['Сущность', 'Метрика', 'Кол-во'], $rows);
        $this->info("Время: {$results['duration_seconds']}s");

        // Путь к отчёту немаппящихся полей
        if (!empty($results['unmapped_report']) && is_string($results['unmapped_report'])) {
            $this->newLine();
            $this->info("Отчёт немаппящихся полей: {$results['unmapped_report']}");
        }

        return self::SUCCESS;
    }
}
