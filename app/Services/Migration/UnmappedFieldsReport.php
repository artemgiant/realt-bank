<?php

namespace App\Services\Migration;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Отчёт о немаппящихся полях миграции.
 *
 * Генерирует JSON-файл с полями, для которых нет прямого маппинга
 * в новую базу. Для каждого значения показывает:
 * - Название (из lib_other)
 * - Количество объектов
 * - Список ID объектов (первые 50)
 *
 * Файл: storage/app/migration_unmapped_report.json
 * Пользователь просматривает и решает что делать с каждым полем.
 */
class UnmappedFieldsReport
{
    /**
     * Поля для включения в отчёт.
     * Ключ — имя поля в objects, значение — описание на русском.
     */
    protected const REPORT_FIELDS = [
        'the_windows' => 'Окна',
        'the_sanuzel' => 'Санузел (тип)',
        'the_sanuzel_tip' => 'Санузел (количество)',
        'the_catnedv' => 'Категория недвижимости',
        'the_tipvhoda' => 'Тип входа',
        'the_tippomesh' => 'Тип помещения',
        'material' => 'Материал стен (material)',
        'perecritie' => 'Перекрытие',
        'the_document_naflat' => 'Документ на квартиру',
        'the_document_nahouse' => 'Документ на дом',
        'the_document_nacommerc' => 'Документ на коммерцию',
    ];

    /**
     * Генерация отчёта.
     * Сканирует все мигрированные объекты и собирает немаппящиеся значения.
     *
     * @return string Путь к JSON-файлу с отчётом
     */
    public function generate(): string
    {
        $report = [];

        foreach (self::REPORT_FIELDS as $field => $label) {
            // Собираем уникальные значения и ID объектов
            $rows = DB::connection('factor_dump')
                ->table('objects')
                ->whereIn('status', [1, 2, 3])
                ->where('rent', 0)
                ->where('deleted', 0)
                ->where($field, '>', 0)
                ->select($field, 'id')
                ->get();

            if ($rows->isEmpty()) continue;

            // Группируем по значению
            $grouped = [];
            foreach ($rows as $row) {
                $val = $row->$field;
                if (!isset($grouped[$val])) {
                    $grouped[$val] = ['object_ids' => [], 'count' => 0];
                }
                $grouped[$val]['count']++;
                // Сохраняем первые 50 ID для примера
                if (count($grouped[$val]['object_ids']) < 50) {
                    $grouped[$val]['object_ids'][] = $row->id;
                }
            }

            // Получаем названия из lib_other
            $ids = array_keys($grouped);
            $names = DB::connection('factor_dump')
                ->table('lib_other')
                ->whereIn('id', $ids)
                ->pluck('name', 'id')
                ->toArray();

            // Формируем запись отчёта
            $fieldReport = [
                'label' => $label,
                'total_objects' => $rows->count(),
                'unique_values' => count($grouped),
                'values' => [],
            ];

            foreach ($grouped as $valueId => $info) {
                $fieldReport['values'][$valueId] = [
                    'name' => $names[$valueId] ?? "ID:{$valueId}",
                    'count' => $info['count'],
                    'object_ids' => $info['object_ids'],
                ];
            }

            $report[$field] = $fieldReport;
        }

        // Сохраняем в storage/app/
        $path = 'migration_unmapped_report.json';
        Storage::disk('local')->put(
            $path,
            json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return storage_path('app/' . $path);
    }
}
