<?php

namespace App\Services\Migration\Mappers;

use App\Models\Reference\Complex;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Маппинг жилых комплексов: старые lib_other записи (complex) → новые complexes.
 *
 * В старой базе ЖК хранятся как записи в lib_other (type=complex),
 * а в objects.complex — ID записи из lib_other.
 *
 * Сопоставление по имени (case-insensitive). Если ЖК не найден — создаётся.
 */
class ComplexMapper
{
    // old lib_other.id → new complexes.id
    protected array $map = [];

    // old lib_other.id → name (кеш названий)
    protected array $nameCache = [];

    /**
     * Загрузить все ЖК из старой базы и построить маппинг.
     */
    public function build(): void
    {
        // Загружаем все complex записи из lib_other, которые используются в objects
        $usedIds = DB::connection('factor_dump')
            ->table('objects')
            ->whereIn('status', [1, 2, 3])
            ->where('rent', 0)
            ->where('deleted', 0)
            ->where('complex', '>', 0)
            ->distinct()
            ->pluck('complex');

        if ($usedIds->isEmpty()) return;

        // Получаем названия из lib_other
        $oldItems = DB::connection('factor_dump')
            ->table('lib_other')
            ->whereIn('id', $usedIds)
            ->get();

        foreach ($oldItems as $item) {
            $this->nameCache[$item->id] = $item->name;
        }

        // Кеш новых ЖК по имени (case-insensitive)
        $newComplexes = Complex::all();
        $newByName = [];
        foreach ($newComplexes as $c) {
            $newByName[mb_strtolower(trim($c->name))] = $c->id;
        }

        // Строим маппинг
        foreach ($this->nameCache as $oldId => $name) {
            $key = mb_strtolower(trim($name));
            if (isset($newByName[$key])) {
                $this->map[$oldId] = $newByName[$key];
            }
            // Не создаём автоматически — создадим по требованию в getComplexId()
        }
    }

    /**
     * Получить new complex_id по старому lib_other.id.
     * Если ЖК не существует — создаёт запись в complexes.
     */
    public function getComplexId(?int $oldComplexId): ?int
    {
        if (!$oldComplexId || $oldComplexId <= 0) {
            return null;
        }

        // Уже замаплен
        if (isset($this->map[$oldComplexId])) {
            return $this->map[$oldComplexId];
        }

        // Получаем название из кеша или из БД
        $name = $this->nameCache[$oldComplexId] ?? null;
        if (!$name) {
            $name = DB::connection('factor_dump')
                ->table('lib_other')
                ->where('id', $oldComplexId)
                ->value('name');
        }

        if (!$name) {
            return null;
        }

        // Ищем или создаём в новой базе
        $complex = Complex::firstOrCreate(
            ['name' => $name],
            [
                'slug' => Str::slug($name),
                'is_active' => true,
            ]
        );

        $this->map[$oldComplexId] = $complex->id;
        return $complex->id;
    }

    public function getStats(): array
    {
        return [
            'mapped' => count($this->map),
            'total_old' => count($this->nameCache),
        ];
    }
}
