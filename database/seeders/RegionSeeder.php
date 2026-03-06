<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $stateId = 14; // Одесская область
        $now = now();

        // Шаг 1: Создать 27 районов из мастер-списка (lib_regions town_id=4)
        $masterRegions = DB::table('lib_regions')
            ->where('town_id', 4)
            ->pluck('name')
            ->unique();

        foreach ($masterRegions as $name) {
            // Нормализация: убрать лишние пробелы (напр. "Белгород- Днестровский" → "Белгород-Днестровский")
            $normalizedName = preg_replace('/\s+/', ' ', trim($name));
            $normalizedName = str_replace('- ', '-', $normalizedName);

            DB::table('regions')->updateOrInsert(
                ['name' => $normalizedName, 'state_id' => $stateId],
                ['is_active' => 1, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $this->command->info("Создано районов: " . DB::table('regions')->where('state_id', $stateId)->count());

        // Построить маппинг name => id для районов
        $regionMap = DB::table('regions')
            ->where('state_id', $stateId)
            ->pluck('id', 'name');

        // Шаг 2a: Автоматическая привязка городов через lib_regions
        $libRegions = DB::table('lib_regions')
            ->whereNotIn('town_id', [1, 3, 4])
            ->where('deleted', 0)
            ->get();

        $linked = 0;
        foreach ($libRegions as $lr) {
            $regionId = $regionMap[$lr->name] ?? null;
            if ($regionId) {
                $updated = DB::table('cities')
                    ->where('id', $lr->town_id)
                    ->whereNull('deleted_at')
                    ->update(['region_id' => $regionId]);
                if ($updated) {
                    $linked++;
                }
            } else {
                $this->command->warn("Район '{$lr->name}' не найден для города town_id={$lr->town_id}");
            }
        }

        $this->command->info("Автоматически привязано городов: {$linked}");

        // Шаг 2b: Ручная привязка городов без записей в lib_regions
        $manualBindings = [
            1  => 'Одесский',               // Одесса
            2  => 'Белгород-Днестровский',   // Белгород-Днестровский
            // 3 => NULL,                    // Николаев — другая область, без района
            13 => 'Овидиопольский',          // Совиньон
            90 => 'Лиманский',              // Крыжановка
        ];

        foreach ($manualBindings as $cityId => $regionName) {
            $regionId = $regionMap[$regionName] ?? null;
            if ($regionId) {
                DB::table('cities')->where('id', $cityId)->update(['region_id' => $regionId]);
                $this->command->info("Вручную привязан город id={$cityId} → район '{$regionName}'");
            } else {
                $this->command->warn("Район '{$regionName}' не найден для ручной привязки города id={$cityId}");
            }
        }

        // Шаг 3: Soft-delete дубликаты городов
        $duplicateCityIds = [6, 7, 8, 11, 12, 72];
        $deleted = DB::table('cities')
            ->whereIn('id', $duplicateCityIds)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => $now]);

        $this->command->info("Soft-deleted дубликатов городов: {$deleted}");

        // Итог
        $withRegion = DB::table('cities')->whereNotNull('region_id')->whereNull('deleted_at')->count();
        $withoutRegion = DB::table('cities')->whereNull('region_id')->whereNull('deleted_at')->count();
        $this->command->info("Итого: городов с районом={$withRegion}, без района={$withoutRegion}");
    }
}
