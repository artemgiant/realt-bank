<?php

namespace App\Services\Migration\Migrators;

use App\Models\Property\Property;
use App\Models\Property\PropertyTranslation;
use App\Models\Reference\Dictionary;
use App\Services\Migration\Mappers\ComplexMapper;
use App\Services\Migration\Mappers\DictionaryMapper;
use App\Services\Migration\Mappers\LocationMapper;
use App\Services\Migration\Mappers\UserMapper;
use App\Services\Migration\Migrators\ContactMigrator;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\DB;

/**
 * Мигратор объектов: factor_dump.objects → properties + property_features.
 *
 * Шаг 3 миграции (после филиалов и пользователей).
 * Переносит только: status IN(1,2,3), rent=0, deleted=0 (~6691 объектов).
 *
 * Типы объектов (поле status в старой базе — это тип недвижимости):
 *   status=1 → Квартиры
 *   status=2 → Дома
 *   status=3 → Коммерция
 *
 * Маппинг полей — см. README.md в папке сервиса.
 */
class PropertyMigrator
{
    protected LocationMapper $locationMapper;     // маппинг локаций
    protected DictionaryMapper $dictionaryMapper;  // маппинг справочников
    protected UserMapper $userMapper;              // маппинг пользователей
    protected ComplexMapper $complexMapper;        // маппинг ЖК
    protected ContactMigrator $contactMigrator;    // миграция контактов
    protected ?OutputStyle $output;

    // Результат: old object.id → new property.id
    // Используется в PropertyPhotoMigrator для привязки фото
    protected array $propertyMap = [];

    protected int $chunkSize;
    protected int $limit;  // лимит объектов (0 = все)

    /**
     * Маппинг старого status → новый property_type_id в dictionaries.
     * ВАЖНО: status в старой базе — это тип недвижимости, НЕ статус объекта!
     */
    protected const STATUS_TO_PROPERTY_TYPE = [
        1 => 23, // Квартиры → Квартира (dictionaries.id=23)
        2 => 28, // Дома → Дом (dictionaries.id=28)
        3 => 40, // Коммерция → Помещение свободного назначения (dictionaries.id=40)
    ];

    /**
     * Маппинг type_sale → contact_type_id в dictionaries.
     * Определяет тип контакта продавца на объекте.
     */
    protected const TYPE_SALE_TO_CONTACT_TYPE = [
        1 => 202, // Риелтор → "Агент (50/50)"
        2 => 195, // Собственник → "Эксклюзив / Владелец"
    ];

    public function __construct(
        LocationMapper $locationMapper,
        DictionaryMapper $dictionaryMapper,
        UserMapper $userMapper,
        ComplexMapper $complexMapper,
        ContactMigrator $contactMigrator,
        ?OutputStyle $output = null,
        int $chunkSize = 500,
        int $limit = 0
    ) {
        $this->locationMapper = $locationMapper;
        $this->dictionaryMapper = $dictionaryMapper;
        $this->userMapper = $userMapper;
        $this->complexMapper = $complexMapper;
        $this->contactMigrator = $contactMigrator;
        $this->output = $output;
        $this->chunkSize = $chunkSize;
        $this->limit = $limit;
    }

    public function getPropertyMap(): array
    {
        return $this->propertyMap;
    }

    public function migrate(): array
    {
        $stats = ['created' => 0, 'skipped' => 0, 'errors' => 0];

        $query = DB::connection('factor_dump')
            ->table('objects')
            ->whereIn('status', array_keys(self::STATUS_TO_PROPERTY_TYPE))
            ->where('rent', 0)
            ->where('deleted', 0);

        $total = $query->count();
        $migrating = $this->limit > 0 ? min($this->limit, $total) : $total;
        $this->output?->info("Migrating {$migrating} of {$total} properties (apartments, houses, commercial)...");

        // Get default country/state IDs
        $countryId = \App\Models\Location\Country::where('code', 'UA')->value('id');
        $stateId = \App\Models\Location\State::where('name', 'like', '%Одесс%')->value('id');

        // Если задан лимит — берём только N записей напрямую (без chunk),
        // иначе обрабатываем все чанками
        if ($this->limit > 0) {
            $objects = DB::connection('factor_dump')
                ->table('objects')
                ->whereIn('status', array_keys(self::STATUS_TO_PROPERTY_TYPE))
                ->where('rent', 0)
                ->where('deleted', 0)
                ->orderBy('id','DESC')
                ->limit($this->limit)
                ->get();

            foreach ($objects as $obj) {
                try {
                    $property = $this->migrateOne($obj, $countryId, $stateId);
                    $this->propertyMap[$obj->id] = $property->id;
                    $stats['created']++;
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    $this->output?->error("Object #{$obj->id}: {$e->getMessage()}");
                }
            }
        } else {
            DB::connection('factor_dump')
                ->table('objects')
                ->whereIn('status', array_keys(self::STATUS_TO_PROPERTY_TYPE))
                ->where('rent', 0)
                ->where('deleted', 0)
                ->orderBy('id')
                ->chunk($this->chunkSize, function ($objects) use (&$stats, $countryId, $stateId) {
                    foreach ($objects as $obj) {
                        try {
                            $property = $this->migrateOne($obj, $countryId, $stateId);
                            $this->propertyMap[$obj->id] = $property->id;
                            $stats['created']++;
                        } catch (\Throwable $e) {
                            $stats['errors']++;
                            $this->output?->error("Object #{$obj->id}: {$e->getMessage()}");
                        }
                    }

                    $this->output?->write("\r  Processed: {$stats['created']} created, {$stats['errors']} errors");
                });
        }

        $this->output?->newLine();
        $this->output?->info("Properties migrated: {$stats['created']}, errors: {$stats['errors']}");

        return $stats;
    }

    /**
     * Перенос одного объекта из factor_dump в properties.
     */
    protected function migrateOne(object $obj, ?int $countryId, ?int $stateId): Property
    {
        // --- JSON data ---
        // Многие поля хранятся в JSON-поле data (title, description, notes, контакты и т.д.)
        $data = json_decode($obj->data ?? '{}', false);

        // --- Локации ---
        $cityId = $this->locationMapper->getCityId($obj->town_id);
        $districtId = $this->locationMapper->getDistrictId($obj->region_id);
        $zoneId = $this->locationMapper->getZoneId($obj->zone_id);
        $streetId = $this->locationMapper->getStreetId($obj->street_id);

        // --- Справочники ---
        $dealKindId = $this->dictionaryMapper->resolve($obj->type_object, 'type_object');
        $buildingTypeId = $this->dictionaryMapper->resolve($obj->project, 'project');
        $conditionId = $this->dictionaryMapper->resolve($obj->situation, 'situation');
        $heatingTypeId = $this->dictionaryMapper->resolve($obj->type_height ?? null, 'type_height');
        $roomCountId = $this->dictionaryMapper->resolveRoomCount($obj->rooms);
        $bathroomCountId = $this->dictionaryMapper->resolveBathroomCount($obj->bothroom);

        // Тип стен: wall_type_g → wall_type_home → material (fallback)
        $wallTypeId = $this->dictionaryMapper->resolve($obj->wall_type_g ?? null, 'wall_type_g')
            ?? $this->dictionaryMapper->resolve($obj->wall_type_home ?? null, 'wall_type_home')
            ?? $this->dictionaryMapper->resolveMaterial($obj->material ?? null);

        $propertyTypeId = self::STATUS_TO_PROPERTY_TYPE[$obj->status] ?? null;
        $dealTypeId = $this->dictionaryMapper->resolveDealType($obj->rent ?? 0);

        // --- ЖК (жилой комплекс) ---
        $complexId = $this->complexMapper->getComplexId($obj->complex ?? null);

        // --- Координаты ---
        $latitude = null;
        $longitude = null;
        if (!empty($obj->coords)) {
            $parts = explode(',', $obj->coords);
            if (count($parts) === 2) {
                $latitude = trim($parts[0]) ?: null;
                $longitude = trim($parts[1]) ?: null;
            }
        }

        // --- Год постройки ---
        $yearBuilt = !empty($data->year_building) ? (int) $data->year_building : null;

        // --- Комиссия ---
        // commission_type NOT NULL в БД — ставим только если есть значение комиссии
        $commission = null;
        $commissionType = 'percent'; // default
        if (!empty($data->price_rieltor_proc)) {
            $commission = $data->price_rieltor_proc;
            $commissionType = 'percent';
        } elseif (!empty($data->price_rieltor)) {
            $commission = $data->price_rieltor;
            $commissionType = 'fixed';
        }

        // --- Ссылка: приоритет linkToAd → rem_url ---
        $externalUrl = !empty($data->linkToAd) ? $data->linkToAd : ($obj->rem_url ?: null);

        // --- Employee ID: через маппинг user_id → Employee ---
        $newUserId = $this->userMapper->get($obj->user_id);
        // Если пользователь удалён в старой базе — назначаем admin (id=1)
        if (!$newUserId) {
            $newUserId = 1;
        }
        $employeeId = \App\Models\Employee\Employee::where('user_id', $newUserId)->value('id');

        // --- Заметки для коллег (agent_notes) ---
        $agentNotes = !empty($data->notes) ? $data->notes : null;

        // --- contact_type_id из type_sale ---
        $contactTypeId = self::TYPE_SALE_TO_CONTACT_TYPE[$obj->type_sale ?? 0] ?? null;

        // --- Notes (поля без маппинга) ---
        $notes = $this->buildNotes($obj);

        $property = new Property();
        $property->forceFill([
            // Сохраняем оригинальный ID из старой базы
            'id' => $obj->id,

            // Relations
            'user_id' => $newUserId,
            'employee_id' => $employeeId,
            'currency_id' => 1,
            'complex_id' => $complexId,
            'contact_type_id' => $contactTypeId,

            // Location
            'country_id' => $countryId,
            'state_id' => $stateId,
            'city_id' => $cityId,
            'district_id' => $districtId,
            'zone_id' => $zoneId,
            'street_id' => $streetId,
            'building_number' => $obj->number_house ?: null,
            'apartment_number' => $obj->num_flat ?: null,
            'latitude' => $latitude,
            'longitude' => $longitude,

            // Dictionaries
            'deal_type_id' => $dealTypeId,
            'deal_kind_id' => $dealKindId,
            'building_type_id' => $buildingTypeId,
            'property_type_id' => $propertyTypeId,
            'condition_id' => $conditionId,
            'wall_type_id' => $wallTypeId,
            'heating_type_id' => $heatingTypeId,
            'room_count_id' => $roomCountId,
            'bathroom_count_id' => $bathroomCountId,

            // Characteristics
            'area_total' => $obj->total_area ?: null,
            'area_living' => $obj->area_live ?: null,
            'area_kitchen' => $obj->area_kitchen ?: null,
            'area_land' => $obj->area_total_ych_t ?: ($this->parseAreaHome($obj->area_home)),
            // В старой базе поля перепутаны: floor_build = этажность, all_floors = этаж
            'floor' => $obj->all_floors ?: null,
            'floors_total' => $obj->floor_build ?: null,
            'year_built' => $yearBuilt,

            // Price
            'price' => $obj->price ?: null,
            'price_per_m2' => $obj->price_area ?: null,
            'commission' => $commission,
            'commission_type' => $commissionType,

            // Media
            'youtube_url' => !empty($data->youtube) ? $data->youtube : null,
            'external_url' => $externalUrl,

            // Settings
            'is_visible_to_agents' => ($obj->open ?? 1) == 1,
            'status' => 'active',
            'notes' => $notes ?: null,
            'agent_notes' => $agentNotes,

            // Timestamps
            'created_at' => $obj->date_created,
            'updated_at' => $obj->date_updated ?? $obj->date_created,
        ]);
        $property->save();

        // Перенос характеристик (features: балкон, парковка, вид)
        $this->migrateFeatures($property, $obj);

        // Перенос заголовка и описания → property_translations
        $this->migrateTranslations($property, $data);

        // Перенос контакта продавца → contacts + contactables
        $this->contactMigrator->migrateForProperty($property, $data, $obj->type_sale ?? null);

        return $property;
    }

    /**
     * Перенос характеристик объекта в property_features (many-to-many).
     * Балкон, парковка, вид — маппятся через DictionaryMapper как feature.
     */
    protected function migrateFeatures(Property $property, object $obj): void
    {
        $featureIds = [];

        // Балкон (the_balkon → feature)
        $id = $this->dictionaryMapper->resolve($obj->the_balkon ?? null, 'the_balkon');
        if ($id) $featureIds[] = $id;

        // Парковка (the_plase_auto → feature)
        $id = $this->dictionaryMapper->resolve($obj->the_plase_auto ?? null, 'the_plase_auto');
        if ($id) $featureIds[] = $id;

        // Вид из окна (the_vid_na → feature)
        $id = $this->dictionaryMapper->resolve($obj->the_vid_na ?? null, 'the_vid_na');
        if ($id) $featureIds[] = $id;

        if (!empty($featureIds)) {
            $property->features()->sync($featureIds);
        }
    }

    /**
     * Перенос заголовка и описания → property_translations.
     * Данные берутся из JSON data (title, description).
     */
    protected function migrateTranslations(Property $property, ?object $data): void
    {
        if (!$data) return;

        $title = !empty($data->title) ? $data->title : null;
        $description = !empty($data->description) ? $data->description : null;

        if ($title || $description) {
            PropertyTranslation::create([
                'property_id' => $property->id,
                'locale' => 'ru',
                'title' => $title ?? '',
                'description' => $description,
            ]);
        }
    }

    /**
     * Сборка текста заметок из полей без маппинга.
     * Включает: kitchen, plan, lest, type_material, orient, скидка, модератор,
     * а также немаппящиеся поля (окна, санузел и т.д.)
     *
     * ВАЖНО: JSON data.notes теперь идёт в agent_notes, а не сюда.
     */
    protected function buildNotes(object $obj): string
    {
        $parts = [];

        // Справочники без прямого маппинга → текст
        $unmapped = [
            'kitchen' => 'Кухня',
            'plan' => 'Планировка',
            'lest' => 'Лестница',
            'type_material' => 'Материал перекрытий',
        ];

        foreach ($unmapped as $field => $label) {
            $val = $obj->$field ?? null;
            if ($val) {
                $name = $this->dictionaryMapper->getOldName($val, $field);
                if ($name) {
                    $parts[] = "{$label}: {$name}";
                }
            }
        }

        // Немаппящиеся справочники → текст
        $unmappedExtras = [
            'the_windows' => 'Окна',
            'the_sanuzel' => 'Санузел',
            'the_sanuzel_tip' => 'Кол-во санузлов',
            'the_catnedv' => 'Категория недвижимости',
            'the_tipvhoda' => 'Тип входа',
        ];

        foreach ($unmappedExtras as $field => $label) {
            $val = $obj->$field ?? null;
            if ($val) {
                $name = $this->dictionaryMapper->getOldName($val, $field);
                if ($name) {
                    $parts[] = "{$label}: {$name}";
                }
            }
        }

        // Текстовые поля
        if (!empty($obj->sale_off_comment)) {
            $parts[] = "Скидка: {$obj->sale_off_comment}";
        }
        if (!empty($obj->commention_moderator)) {
            $parts[] = "Модератор: {$obj->commention_moderator}";
        }
        if (!empty($obj->orient)) {
            $parts[] = "Ориентация: {$obj->orient}";
        }

        return implode("\n", $parts);
    }

    protected function parseAreaHome(?string $areaHome): ?float
    {
        if (!$areaHome) {
            return null;
        }
        $val = (float) preg_replace('/[^\d.]/', '', $areaHome);
        return $val > 0 ? $val : null;
    }
}
