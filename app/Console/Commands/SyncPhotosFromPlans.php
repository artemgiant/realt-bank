<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Синхронізація фотографій з JSON-планів у property_photos.
 *
 * Сканує всі migration/plans/*.json файли.
 * Для кожного фото:
 *   - Якщо id є в property_photos → UPDATE path і filename
 *   - Якщо id немає → INSERT (property_id = object_id, бо ID збігаються)
 *
 * ./vendor/bin/sail artisan app:sync-photos-from-plans
 * ./vendor/bin/sail artisan app:sync-photos-from-plans --dry-run
 * ./vendor/bin/sail artisan app:sync-photos-from-plans --file=2021-03.json
 */
class SyncPhotosFromPlans extends Command
{
    protected $signature = 'app:sync-photos-from-plans
                            {--dry-run : Показати статистику без змін в БД}
                            {--file= : Обробити тільки один файл (наприклад 2021-03.json)}
                            {--chunk=500 : Розмір пакету}';

    protected $description = 'Синхронізувати фото з JSON-планів: оновити існуючі + додати відсутні';

    protected int $updated = 0;
    protected int $inserted = 0;
    protected int $skipped = 0;
    protected int $noProperty = 0;

    /** Існуючі property ID */
    protected array $propertyIds = [];

    public function handle(): int
    {
        $plansDir = base_path('migration/plans');

        if (!is_dir($plansDir)) {
            $this->error("Директорія {$plansDir} не знайдена");
            return self::FAILURE;
        }

        $isDryRun = $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');
        $singleFile = $this->option('file');

        if ($isDryRun) {
            $this->warn('=== DRY RUN — дані НЕ будуть змінені ===');
        }

        // Завантажуємо всі property ID для перевірки існування
        $this->info('Завантажую список properties...');
        $this->propertyIds = DB::table('properties')->pluck('id')->flip()->toArray();
        $this->info('Properties в БД: ' . count($this->propertyIds));

        // Збираємо список файлів
        if ($singleFile) {
            $files = ["{$plansDir}/{$singleFile}"];
            if (!file_exists($files[0])) {
                $this->error("Файл {$files[0]} не знайдено");
                return self::FAILURE;
            }
        } else {
            $files = glob("{$plansDir}/*.json");
            sort($files);
        }

        $this->info('Файлів для обробки: ' . count($files));

        foreach ($files as $file) {
            $this->processFile($file, $chunkSize, $isDryRun);
        }

        $this->newLine();
        $this->info('=== Результат ===');
        $this->table(
            ['Оновлено', 'Додано', 'Пропущено (вже OK)', 'Без property'],
            [[$this->updated, $this->inserted, $this->skipped, $this->noProperty]]
        );

        return self::SUCCESS;
    }

    protected function processFile(string $filePath, int $chunkSize, bool $isDryRun): void
    {
        $basename = basename($filePath);
        $content = file_get_contents($filePath);

        // Прибираємо BOM та зайві символи перед [
        $bracketPos = strpos($content, '[');
        if ($bracketPos !== false && $bracketPos > 0) {
            $content = substr($content, $bracketPos);
        }

        $photos = json_decode($content, true);

        if (!is_array($photos) || empty($photos)) {
            $this->warn("  {$basename}: порожній або невалідний JSON, пропуск");
            return;
        }

        $this->line("  {$basename}: " . count($photos) . ' фото...');

        $chunks = array_chunk($photos, $chunkSize);
        foreach ($chunks as $chunk) {
            $this->processChunk($chunk, $isDryRun);
        }
    }

    protected function processChunk(array $photos, bool $isDryRun): void
    {
        $ids = array_column($photos, 'id');

        // Отримуємо існуючі записи: id → path
        $existing = DB::table('property_photos')
            ->whereIn('id', $ids)
            ->pluck('path', 'id')
            ->toArray();

        $toInsert = [];

        foreach ($photos as $photo) {
            $id = $photo['id'] ?? null;
            $source = $photo['source'] ?? null;
            $url = ($source === 'aws_s3')
                ? ($photo['img_url'] ?? $photo['s3_url'] ?? null)
                : ($photo['s3_url'] ?? $photo['img_url'] ?? null);
            $filename = $photo['filename'] ?? null;
            $objectId = $photo['object_id'] ?? null;

            if (!$id || !$url) {
                $this->skipped++;
                continue;
            }

            if (isset($existing[$id])) {
                // Фото є в БД — оновлюємо path/filename
                if ($existing[$id] === $url) {
                    $this->skipped++;
                    continue;
                }

                if (!$isDryRun) {
                    DB::table('property_photos')
                        ->where('id', $id)
                        ->update([
                            'path' => $url,
                            'filename' => $filename ?? basename($url),
                            'updated_at' => now(),
                        ]);
                }
                $this->updated++;
            } else {
                // Фото немає в БД — вставляємо
                // property_id = object_id (ID збігаються)
                $propertyId = $objectId;

                if (!$propertyId || !isset($this->propertyIds[$propertyId])) {
                    $this->noProperty++;
                    continue;
                }

                $toInsert[] = [
                    'id' => $id,
                    'property_id' => $propertyId,
                    'path' => $url,
                    'filename' => $filename ?? basename($url),
                    'sort_order' => 0,
                    'is_main' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $this->inserted++;
            }
        }

        // Batch insert нових фото
        if (!empty($toInsert) && !$isDryRun) {
            foreach (array_chunk($toInsert, 500) as $batch) {
                DB::table('property_photos')->insert($batch);
            }
        }
    }
}
