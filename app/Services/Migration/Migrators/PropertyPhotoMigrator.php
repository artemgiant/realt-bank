<?php

namespace App\Services\Migration\Migrators;

use App\Models\Property\PropertyPhoto;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\DB;

/**
 * Мигратор фото: factor_dump.object_images → property_photos.
 *
 * Шаг 4 миграции (последний, после объектов).
 * Переносит только фото для объектов, которые были успешно перенесены (есть в propertyMap).
 * Фото без соответствующего объекта пропускаются (skipped).
 *
 * Маппинг полей:
 *   object_id → property_id (через propertyMap)
 *   img_name  → filename + path (с префиксом "legacy/")
 *   is_main   → is_main
 *   sort_at   → sort_order
 */
class PropertyPhotoMigrator
{
    // old object_id → new property_id (получаем из PropertyMigrator)
    protected array $propertyMap;
    protected ?OutputStyle $output;
    protected int $chunkSize;

    public function __construct(
        array $propertyMap,
        ?OutputStyle $output = null,
        int $chunkSize = 1000
    ) {
        $this->propertyMap = $propertyMap;
        $this->output = $output;
        $this->chunkSize = $chunkSize;
    }

    public function migrate(): array
    {
        $stats = ['created' => 0, 'skipped' => 0, 'errors' => 0];

        // Берём только фото для перенесённых объектов (по object_id из propertyMap)
        $objectIds = array_keys($this->propertyMap);

        $total = DB::connection('factor_dump')->table('object_images')
            ->whereIn('object_id', $objectIds)
            ->whereNull('deleted_at')
            ->count();

        $this->output?->info("Migrating {$total} photos for " . count($objectIds) . " objects...");

        DB::connection('factor_dump')
            ->table('object_images')
            ->whereIn('object_id', $objectIds)
            ->whereNull('deleted_at')
            ->orderBy('object_id')
            ->orderBy('sort_at')
            ->chunk($this->chunkSize, function ($images) use (&$stats) {
                $batch = [];

                foreach ($images as $img) {
                    $propertyId = $this->propertyMap[$img->object_id] ?? null;

                    if (!$propertyId) {
                        $stats['skipped']++;
                        continue;
                    }

                    $batch[] = [
                        // Сохраняем оригинальный ID фото из старой базы
                        'id' => $img->id,
                        'property_id' => $propertyId,
                        'path' => $this->buildPath($img),
                        'filename' => $img->img_name ?? '',
                        'sort_order' => $img->sort_at ?? 0,
                        'is_main' => (bool) ($img->is_main ?? false),
                        'created_at' => $img->created_at ?? now(),
                        'updated_at' => $img->updated_at ?? now(),
                    ];
                }

                if (!empty($batch)) {
                    try {
                        PropertyPhoto::insert($batch);
                        $stats['created'] += count($batch);
                    } catch (\Throwable $e) {
                        // Fallback: insert one by one
                        foreach ($batch as $row) {
                            try {
                                PropertyPhoto::create($row);
                                $stats['created']++;
                            } catch (\Throwable $e2) {
                                $stats['errors']++;
                            }
                        }
                    }
                }

                $this->output?->write("\r  Photos: {$stats['created']} created, {$stats['skipped']} skipped, {$stats['errors']} errors");
            });

        $this->output?->newLine();
        $this->output?->info("Photos migrated: {$stats['created']}, skipped: {$stats['skipped']}, errors: {$stats['errors']}");

        return $stats;
    }

    protected function buildPath(object $img): string
    {
        $imgName = $img->img_name ?? '';

        // Если img_name — полный URL (AWS S3 и т.п.), сохраняем как есть
        if (str_starts_with($imgName, 'http')) {
            return $imgName;
        }

        // Локальный путь — сохраняем с префиксом legacy
        return 'legacy' . ($imgName ?: '/unknown_' . $img->id);
    }
}
