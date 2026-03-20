<?php

namespace App\Services\Migration\Mappers;

use Illuminate\Support\Facades\DB;
use App\Models\Reference\Dictionary;

/**
 * Маппинг справочников: старые lib_other записи → новые dictionaries.
 *
 * Сопоставление по имени. Если имя не совпадает — используется NAME_MAP для ручных соответствий.
 * Поля без маппинга (kitchen, plan, lest и т.д.) записываются в notes текстом.
 */
class DictionaryMapper
{
    // Соответствие: тип в старой базе (lib_other.type) → тип в новой (Dictionary::TYPE_*)
    protected const TYPE_MAP = [
        // type_object в старой базе = вид сделки (deal_kind)
        'type_object' => Dictionary::TYPE_DEAL_KIND,
        // project = тип здания (building_type)
        'project' => Dictionary::TYPE_BUILDING_TYPE,
        // situation = состояние
        'situation' => Dictionary::TYPE_CONDITION,
        // type_height на самом деле = отопление (heating_type)
        'type_height' => Dictionary::TYPE_HEATING_TYPE,
        // wall types
        'wall_type_g' => Dictionary::TYPE_WALL_TYPE,
        'wall_type_home' => Dictionary::TYPE_WALL_TYPE,
        // property types
        'type_nedv' => Dictionary::TYPE_PROPERTY_TYPE,
        'type_nedv_g' => Dictionary::TYPE_PROPERTY_TYPE,
        'type_nedv_h' => Dictionary::TYPE_PROPERTY_TYPE,
        'the_tipe_object_com' => Dictionary::TYPE_PROPERTY_TYPE,
        // features
        'the_balkon' => Dictionary::TYPE_FEATURE,
        'the_plase_auto' => Dictionary::TYPE_FEATURE,
        'the_vid_na' => Dictionary::TYPE_FEATURE,
    ];

    // Ручной маппинг имён: old name → new Dictionary name
    protected const NAME_MAP = [
        // situation → condition
        'Евроремонт' => 'С ремонтом',
        'Капитальный' => 'С ремонтом',
        // type_object → deal_kind
        'Отдел Продаж' => 'Отдел продаж',
        // type_nedv_g → property_type
        'квартира' => 'Квартира',
        'комната' => 'Комната',
        // type_nedv_h → property_type
        'дом' => 'Дом',
        'участок' => 'Земля под жилую застройку',
    ];

    // Главный маппинг: "тип:old_id" → new Dictionary.id
    // Например: "project:15" → 42 (Новострой)
    protected array $idMap = [];

    // Кеш имён старых записей: "тип:old_id" → название
    // Используется для записи в notes, если маппинг не найден
    protected array $nameCache = [];

    // Кеш новых Dictionary записей: "type|имя" → id
    // Для быстрого поиска по имени без запросов к БД
    protected array $dictionaryCache = [];

    /**
     * Загрузить все справочники из обеих баз и построить маппинг.
     */
    public function build(): void
    {
        $this->loadDictionaryCache();  // сначала загружаем новые Dictionary
        $this->buildIdMap();           // затем сопоставляем со старыми lib_other
    }

    /**
     * Получить новый Dictionary ID по старому lib_other ID и типу.
     * Например: resolve(15, 'project') → 42 (Новострой)
     */
    public function resolve(?int $oldId, string $oldType): ?int
    {
        if (!$oldId) {
            return null;
        }

        $key = $oldType . ':' . $oldId;
        return $this->idMap[$key] ?? null;
    }

    /**
     * Получить старое название записи (для полей без маппинга — пишем в notes).
     */
    public function getOldName(?int $oldId, string $oldType): ?string
    {
        if (!$oldId) {
            return null;
        }
        return $this->nameCache[$oldType . ':' . $oldId] ?? null;
    }

    /**
     * Маппинг количества комнат: число → Dictionary ID типа room_count.
     * Например: 3 → id записи "3" в dictionaries
     */
    public function resolveRoomCount(?int $rooms): ?int
    {
        if (!$rooms || $rooms < 1) {
            return null;
        }

        $value = $rooms >= 10 ? '10+' : (string) $rooms;

        return Dictionary::where('type', Dictionary::TYPE_ROOM_COUNT)
            ->where('value', $value)
            ->value('id');
    }

    /**
     * Маппинг количества санузлов: число → Dictionary ID типа bathroom_count.
     */
    public function resolveBathroomCount(?int $count): ?int
    {
        if (!$count || $count < 1) {
            return null;
        }

        $value = $count >= 10 ? '10+' : (string) $count;

        return Dictionary::where('type', Dictionary::TYPE_BATHROOM_COUNT)
            ->where('value', $value)
            ->value('id');
    }

    /**
     * Маппинг типа сделки по флагу rent: 0=Продажа, 1=Аренда.
     * Ищет в dictionaries запись типа deal_type с нужным префиксом.
     */
    public function resolveDealType(int $rent, ?string $propertyCategory = null): ?int
    {
        $prefix = $rent ? 'Аренда' : 'Продажа';
        $suffix = $propertyCategory ?? 'квартир';

        $name = $prefix . ' ' . $suffix;

        return Dictionary::where('type', Dictionary::TYPE_DEAL_TYPE)
            ->where('name', $name)
            ->value('id')
            ?? Dictionary::where('type', Dictionary::TYPE_DEAL_TYPE)
                ->where('name', 'like', $prefix . '%')
                ->value('id');
    }

    protected function loadDictionaryCache(): void
    {
        $all = Dictionary::where('is_active', true)->get();

        foreach ($all as $dict) {
            $key = $dict->type . '|' . mb_strtolower(trim($dict->name));
            $this->dictionaryCache[$key] = $dict->id;
        }
    }

    protected function buildIdMap(): void
    {
        $oldItems = DB::connection('factor_dump')
            ->table('lib_other')
            ->where('deleted', 0)
            ->get();

        foreach ($oldItems as $item) {
            $oldKey = $item->type . ':' . $item->id;
            $this->nameCache[$oldKey] = $item->name;

            $newType = self::TYPE_MAP[$item->type] ?? null;
            if (!$newType) {
                continue;
            }

            // Try name mapping first
            $mappedName = self::NAME_MAP[$item->name] ?? $item->name;

            // Search in dictionary cache (case-insensitive)
            $cacheKey = $newType . '|' . mb_strtolower(trim($mappedName));

            if (isset($this->dictionaryCache[$cacheKey])) {
                $this->idMap[$oldKey] = $this->dictionaryCache[$cacheKey];
            } else {
                // Try original name if mapped didn't work
                $origKey = $newType . '|' . mb_strtolower(trim($item->name));
                if (isset($this->dictionaryCache[$origKey])) {
                    $this->idMap[$oldKey] = $this->dictionaryCache[$origKey];
                }
                // else: unmapped — will return null, value goes to notes
            }
        }
    }

    public function getStats(): array
    {
        return [
            'mapped' => count($this->idMap),
            'total_old_items' => count($this->nameCache),
        ];
    }
}
