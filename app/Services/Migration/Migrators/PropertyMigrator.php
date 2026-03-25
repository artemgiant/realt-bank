<?php

namespace App\Services\Migration\Migrators;

use App\Models\Property\Property;
use App\Models\Property\PropertyTranslation;
use App\Models\Reference\Dictionary;
use App\Services\Migration\Mappers\BlockMapper;
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
    protected ComplexMapper $complexMapper;        // маппинг ЖК (fallback)
    protected BlockMapper $blockMapper;            // маппинг блоков → block_id + complex_id
    protected ContactMigrator $contactMigrator;    // миграция контактов
    protected ?OutputStyle $output;

    // Результат: old object.id → new property.id
    // Используется в PropertyPhotoMigrator для привязки фото
    protected array $propertyMap = [];

    protected int $chunkSize;
    protected int $limit;  // лимит объектов (0 = все)

    /**
     * Маппинг status=1 (Квартиры): type_nedvizemos → property_type_id.
     */
    protected const APARTMENT_TYPE_MAP = [
        'комната'  => 27, // Комната
        'квартира' => 23, // Квартира
    ];
    protected const APARTMENT_TYPE_DEFAULT = 23; // Квартира

    /**
     * Маппинг status=2 (Дома): type_nedviz_t → property_type_id.
     */
    protected const HOUSE_TYPE_MAP = [
        'участок' => 33, // Земля под жилую застройку
        'дом'     => 28, // Дом
    ];
    protected const HOUSE_TYPE_DEFAULT = 28; // Дом

    /**
     * Маппинг status=3 (Коммерция): type_nedviz_t → property_type_id.
     */
    protected const COMMERCIAL_TYPE_MAP = [
        'помещения свободного назначения'         => 40,  // Помещение свободного назначения
        'здание свободного назначения'            => 41,  // Здание
        'офисное помещение'                       => 36,  // Офисное помещение
        'офисное здание'                          => 41,  // Здание
        'земля коммерческого назначения'           => 34,  // Земля коммерческого назначения
        'земля рекреационного назначения'          => 383, // Земля рекреационного назначения
        'земля сельскохозяйственного назначения'   => 35,  // Земля сельскохозяйственного назначения
        'участок под жилую застройку'              => 33,  // Земля под жилую застройку
        'гостинично- оздоровительные объекты'      => 385, // Гостиница/отель
        'объект сферы услуг'                       => 40,  // Помещение свободного назначения
        'кафе, бар, ресторан'                      => 384, // Ресторан/кафе
        'торговые площади'                         => 37,  // Торговое помещение
        'складские помещения'                      => 38,  // Складские помещения
        'производственные помещения'                => 39,  // Производственные помещения
        'готовый бизнес'                           => 42,  // Готовый бизнес
        'подземный паркинг'                        => 43,  // Паркинг
        'отдельно стоящий гараж'                   => 45,  // Гараж
    ];
    protected const COMMERCIAL_TYPE_DEFAULT = 40; // Помещение свободного назначения

    /**
     * property_type_id → deal_type_id (продажа, т.к. rent=0).
     * Группировка: квартиры=1, комнаты=2, дома=3, земля=4, коммерция=5, паркинг/гараж=6.
     */
    protected const PROPERTY_TYPE_TO_DEAL_TYPE = [
        23  => 1, // Квартира → Продажа квартир
        27  => 2, // Комната → Продажа комнат
        28  => 3, // Дом → Продажа домов
        33  => 4, // Земля под жилую застройку → Продажа земли
        34  => 4, // Земля коммерческого назначения → Продажа земли
        35  => 4, // Земля сельскохозяйственного назначения → Продажа земли
        383 => 4, // Земля рекреационного назначения → Продажа земли
        43  => 6, // Паркинг → Продажа паркинг/гараж
        45  => 6, // Гараж → Продажа паркинг/гараж
        // Всё остальное — коммерция
        36  => 5, // Офисное помещение → Продажа коммерции
        37  => 5, // Торговое помещение → Продажа коммерции
        38  => 5, // Складские помещения → Продажа коммерции
        39  => 5, // Производственные помещения → Продажа коммерции
        40  => 5, // Помещение свободного назначения → Продажа коммерции
        41  => 5, // Здание → Продажа коммерции
        42  => 5, // Готовый бизнес → Продажа коммерции
        384 => 5, // Ресторан/кафе → Продажа коммерции
        385 => 5, // Гостиница/отель → Продажа коммерции
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
        BlockMapper $blockMapper,
        ContactMigrator $contactMigrator,
        ?OutputStyle $output = null,
        int $chunkSize = 500,
        int $limit = 0
    ) {
        $this->locationMapper = $locationMapper;
        $this->dictionaryMapper = $dictionaryMapper;
        $this->userMapper = $userMapper;
        $this->complexMapper = $complexMapper;
        $this->blockMapper = $blockMapper;
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
            ->whereIn('status', [1, 2, 3])
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
                ->whereIn('status', [1, 2, 3])
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
                ->whereIn('status', [1, 2, 3])
                ->where('rent', 0)
                ->where('deleted', 0)
                ->chunkById($this->chunkSize, function ($objects) use (&$stats, $countryId, $stateId) {
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

        // state_id и region_id берём из города (а не хардкодим)
        $cityStateId = null;
        $cityRegionId = null;
        if ($cityId) {
            $city = \App\Models\Location\City::find($cityId);
            if ($city) {
                $cityStateId = $city->state_id;
                $cityRegionId = $city->region_id;
            }
        }

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

        [$propertyTypeId, $dealTypeId] = $this->resolvePropertyAndDealType($obj);

        // --- Блок + ЖК ---
        // BlockMapper определяет block_id и complex_id по таблице соответствий.
        $blockResult = $this->blockMapper->resolve($obj->complex ?? null, $obj);
        $blockId = $blockResult['block_id'];
        $complexId = $blockResult['complex_id'];

        // Если блок не найден и не ignored — логируем в JSON для разбора
        if (!$blockId && ($obj->complex ?? 0) > 0 && !$this->blockMapper->isIgnored($obj->complex)) {
            $this->logUnmappedBlock($obj, $streetId);
        }

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
            'source_id' => 55, // Запрос по телефону
            'complex_id' => $complexId,
            'block_id' => $blockId,
            'contact_type_id' => $contactTypeId,

            // Location
            'country_id' => $countryId,
            'state_id' => $cityStateId ?? $stateId,
            'region_id' => $cityRegionId,
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
     * Определение property_type_id и deal_type_id по полям старой базы.
     *
     * status=1 (Квартиры) → type_nedvizemos (квартира/комната)
     * status=2 (Дома)     → type_nedviz_t (дом/участок)
     * status=3 (Коммерция) → type_nedviz_t (офис, склад, земля и т.д.)
     *
     * @return array{int, int} [property_type_id, deal_type_id]
     */
    protected function resolvePropertyAndDealType(object $obj): array
    {
        $typeNedvizT = mb_strtolower(trim($obj->type_nedviz_t ?? ''));
        $typeNedvizemos = mb_strtolower(trim($obj->type_nedvizemos ?? ''));

        $propertyTypeId = match ($obj->status) {
            1 => self::APARTMENT_TYPE_MAP[$typeNedvizemos] ?? self::APARTMENT_TYPE_DEFAULT,
            2 => self::HOUSE_TYPE_MAP[$typeNedvizT] ?? self::HOUSE_TYPE_DEFAULT,
            3 => self::COMMERCIAL_TYPE_MAP[$typeNedvizT] ?? self::COMMERCIAL_TYPE_DEFAULT,
            default => self::APARTMENT_TYPE_DEFAULT,
        };

        $dealTypeId = self::PROPERTY_TYPE_TO_DEAL_TYPE[$propertyTypeId] ?? 5; // fallback: Продажа коммерции

        return [$propertyTypeId, $dealTypeId];
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

    /**
     * Логирование незамапленного блока в JSON-файл.
     * Файл: storage/app/migration/unmapped_blocks.json
     *
     * Собирает данные о старом блоке и связанном объекте,
     * чтобы потом можно было разобрать и добавить соответствия.
     */
    protected function logUnmappedBlock(object $obj, ?int $newStreetId): void
    {
        $oldName = $this->blockMapper->getOldName($obj->complex);
        if (!$oldName) return;

        // Ключ — old complex ID, чтобы не дублировать записи
        $key = (string) $obj->complex;

        // Если уже логировали этот блок — только добавляем object_id
        if (isset($this->unmappedBlocks[$key])) {
            $this->unmappedBlocks[$key]['object_ids'][] = $obj->id;
            $this->unmappedBlocks[$key]['objects_count'] = count($this->unmappedBlocks[$key]['object_ids']);
            $this->saveUnmappedBlocks();
            return;
        }

        // Получаем название улицы
        $streetName = null;
        if ($newStreetId) {
            $streetName = \App\Models\Location\Street::where('id', $newStreetId)->value('name');
        }

        $this->unmappedBlocks[$key] = [
            'old_complex_id' => $obj->complex,
            'old_block_name' => $oldName,
            'object_ids' => [$obj->id],
            'objects_count' => 1,
            'sample_object' => [
                'id' => $obj->id,
                'street' => $streetName,
                'building_number' => $obj->number_house ?: null,
                'city' => $obj->town_id ?? null,
                'price' => $obj->price ?: null,
                'status' => $obj->status,
            ],
        ];

        $this->saveUnmappedBlocks();
    }

    /**
     * Сохранить unmapped блоки в JSON.
     */
    protected function saveUnmappedBlocks(): void
    {
        $dir = storage_path('app/migration');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(
            $dir . '/unmapped_blocks.json',
            json_encode(array_values($this->unmappedBlocks), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /** @var array Кеш незамапленных блоков для JSON-отчёта */
    protected array $unmappedBlocks = [];
}
