<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Перенос фотографій з factor_dump.object_images → property_photos.
 *
 * Переносить тільки фото для існуючих properties (object_id = property_id).
 * Зберігає оригінальний ID з factor_dump.
 *
 * ./vendor/bin/sail artisan app:migrate-photos --fresh --force
 * ./vendor/bin/sail artisan app:migrate-photos --fresh --dry-run
 */
class MigratePhotosFromFactorDump extends Command
{
    protected $signature = 'app:migrate-photos
                            {--fresh : Очистити property_photos перед переносом}
                            {--dry-run : Показати статистику без змін}
                            {--chunk=1000 : Розмір пакету}
                            {--force : Пропустити підтвердження}';

    protected $description = 'Перенести фото з factor_dump.object_images в property_photos';

    protected int $created = 0;
    protected int $skipped = 0;
    protected int $errors = 0;

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $isFresh = $this->option('fresh');
        $chunkSize = (int) $this->option('chunk');

        if ($isDryRun) {
            $this->warn('=== DRY RUN — дані НЕ будуть змінені ===');
        }

        // Завантажуємо всі property ID
        $this->info('Завантажую список properties...');
        $propertyIds = DB::table('properties')->pluck('id')->flip()->toArray();
        $this->info('Properties в БД: ' . count($propertyIds));

        // Очистка таблиці
        if ($isFresh) {
            if (!$isDryRun) {
                if (!$this->option('force') && !$this->confirm('Це видалить ВСІ записи з property_photos. Продовжити?')) {
                    $this->info('Скасовано.');
                    return self::SUCCESS;
                }

                $this->info('Очищую property_photos...');
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                DB::table('property_photos')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                $this->info('Таблиця очищена.');
            } else {
                $this->warn('(dry-run) Таблиця була б очищена.');
            }
        }

        // Рахуємо загальну кількість
        $total = DB::connection('factor_dump')
            ->table('object_images')
            ->whereNull('deleted_at')
            ->count();

        $this->info("Всього фото в factor_dump: {$total}");

        // Переносимо чанками
        DB::connection('factor_dump')
            ->table('object_images')
            ->whereNull('deleted_at')
            ->orderBy('object_id')
            ->orderBy('sort_at')
            ->chunk($chunkSize, function ($images) use ($propertyIds, $isDryRun) {
                $batch = [];

                foreach ($images as $img) {
                    // Пропускаємо фото без відповідного property
                    if (!isset($propertyIds[$img->object_id])) {
                        $this->skipped++;
                        continue;
                    }

                    $batch[] = [
                        'id' => $img->id,
                        'property_id' => $img->object_id,
                        'path' => $img->img_name ?? '',
                        'filename' => $img->img_name ?? '',
                        'sort_order' => $img->sort_at ?? 0,
                        'is_main' => (bool) ($img->is_main ?? false),
                        'created_at' => $img->created_at ?? now(),
                        'updated_at' => $img->updated_at ?? now(),
                    ];
                }

                if (!empty($batch) && !$isDryRun) {
                    try {
                        DB::table('property_photos')->insert($batch);
                        $this->created += count($batch);
                    } catch (\Throwable $e) {
                        // Fallback: по одному
                        foreach ($batch as $row) {
                            try {
                                DB::table('property_photos')->insert($row);
                                $this->created++;
                            } catch (\Throwable $e2) {
                                $this->errors++;
                            }
                        }
                    }
                } else {
                    $this->created += count($batch);
                }

                $this->output->write("\r  Створено: {$this->created}, пропущено: {$this->skipped}, помилок: {$this->errors}");
            });

        $this->newLine(2);
        $this->info('=== Результат ===');
        $this->table(
            ['Створено', 'Пропущено (немає property)', 'Помилок'],
            [[$this->created, $this->skipped, $this->errors]]
        );

        return self::SUCCESS;
    }
}
