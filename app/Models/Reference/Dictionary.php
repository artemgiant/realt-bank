<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Dictionary extends Model
{
    protected $table = 'dictionaries';

    protected $fillable = [
        'type',
        'name',
        'value',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ========== Dictionary Types ==========

    public const TYPE_DEAL_TYPE = 'deal_type';           // Тип сделки
    public const TYPE_DEAL_KIND = 'deal_kind';           // Вид сделки
    public const TYPE_BUILDING_TYPE = 'building_type';   // Тип здания
    public const TYPE_PROPERTY_TYPE = 'property_type';   // Тип недвижимости
    public const TYPE_CONDITION = 'condition';           // Состояние
    public const TYPE_WALL_TYPE = 'wall_type';           // Тип стен
    public const TYPE_HEATING_TYPE = 'heating_type';     // Отопление
    public const TYPE_ROOM_COUNT = 'room_count';         // Количество комнат
    public const TYPE_BATHROOM_COUNT = 'bathroom_count'; // Количество ванных
    public const TYPE_CEILING_HEIGHT = 'ceiling_height'; // Высота потолков
    public const TYPE_FEATURE = 'feature';               // Особенности
    public const TYPE_CONTACT_TAG = 'contact_tag';       // Теги контактов
    public const TYPE_YEAR_BUILT = 'year_built';         // Год постройки
    public const TYPE_CONTACT_TYPE = 'contact_type';     // Тип контакта
    public const TYPE_CONTACT_ROLE = 'contact_role';     // Роль контакта
    public const TYPE_HOUSING_CLASS = 'housing_class';   // Класс жилья
    public const TYPE_COMPLEX_FEATURE = 'complex_feature'; // Особенности комплексов
    public const TYPE_COMPLEX_CATEGORY = 'complex_category'; // Категории комплексов
    public const TYPE_AGENCY_TYPE = 'agency_type';         // Тип агентства
    public const TYPE_AGENT_TAG = 'agent_tag';             // Теги агентов
    public const TYPE_EMPLOYEE_POSITION = 'employee_position'; // Должности сотрудников
    public const TYPE_EMPLOYEE_STATUS = 'employee_status';     // Статусы сотрудников
    public const TYPE_PROPERTY_STATUS = 'property_status';     // Статус объекта (фильтр)

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public static function getContactTypes(): Collection
    {
        return static::getByType(self::TYPE_CONTACT_TYPE);
    }

    public static function getContactRoles(): Collection
    {
        return static::getByType(self::TYPE_CONTACT_ROLE);
    }

    // ========== Static Methods ==========

    public static function getByType(string $type): Collection
    {
        return static::active()
            ->ofType($type)
            ->ordered()
            ->get();
    }

    public static function getDealTypes(): Collection
    {
        return static::getByType(self::TYPE_DEAL_TYPE);
    }

    public static function getDealKinds(): Collection
    {
        return static::getByType(self::TYPE_DEAL_KIND);
    }

    public static function getBuildingTypes(): Collection
    {
        return static::getByType(self::TYPE_BUILDING_TYPE);
    }

    public static function getPropertyTypes(): Collection
    {
        return static::getByType(self::TYPE_PROPERTY_TYPE);
    }

    public static function getConditions(): Collection
    {
        return static::getByType(self::TYPE_CONDITION);
    }

    public static function getWallTypes(): Collection
    {
        return static::getByType(self::TYPE_WALL_TYPE);
    }

    public static function getHeatingTypes(): Collection
    {
        return static::getByType(self::TYPE_HEATING_TYPE);
    }

    public static function getRoomCounts(): Collection
    {
        return static::getByType(self::TYPE_ROOM_COUNT);
    }

    public static function getBathroomCounts(): Collection
    {
        return static::getByType(self::TYPE_BATHROOM_COUNT);
    }

    public static function getCeilingHeights(): Collection
    {
        return static::getByType(self::TYPE_CEILING_HEIGHT);
    }

    public static function getFeatures(): Collection
    {
        return static::getByType(self::TYPE_FEATURE);
    }

    public static function getContactTags(): Collection
    {
        return static::getByType(self::TYPE_CONTACT_TAG);
    }

    public static function getYearsBuilt(): Collection
    {
        return static::getByType(self::TYPE_YEAR_BUILT);
    }

    public static function getHousingClasses(): Collection
    {
        return static::getByType(self::TYPE_HOUSING_CLASS);
    }

    public static function getComplexFeatures(): Collection
    {
        return static::getByType(self::TYPE_COMPLEX_FEATURE);
    }

    public static function getComplexCategories(): Collection
    {
        return static::getByType(self::TYPE_COMPLEX_CATEGORY);
    }

    public static function getAgencyTypes(): Collection
    {
        return static::getByType(self::TYPE_AGENCY_TYPE);
    }

    public static function getAgentTags(): Collection
    {
        return static::getByType(self::TYPE_AGENT_TAG);
    }

    public static function getEmployeePositions(): Collection
    {
        return static::getByType(self::TYPE_EMPLOYEE_POSITION);
    }

    public static function getEmployeeStatuses(): Collection
    {
        return static::getByType(self::TYPE_EMPLOYEE_STATUS);
    }

    public static function getPropertyStatuses(): Collection
    {
        return static::getByType(self::TYPE_PROPERTY_STATUS);
    }

    // ========== Helper for Select Options ==========

    public static function getSelectOptions(string $type): Collection
    {
        return static::getByType($type)->pluck('name', 'id');
    }

    // ========== Deal Type → Property Type Mapping ==========

    /**
     * Маппинг: название типа сделки → названия типов недвижимости.
     */
    public static function getDealTypePropertyTypeMap(): array
    {
        $комнаты = ['Комната'];
        $квартиры = ['Квартира', 'Пентхаус', 'Студия', 'Апартаменты', 'Квартира на земле'];
        $дома = ['Дом', 'Таунхаус', 'Дуплекс', 'Часть дома', 'Коттедж', 'Вилла', 'Дача'];
        $земля = ['Земля под жилую застройку', 'Земля под садоводство', 'Земля коммерческого назначения', 'Земля сельскохозяйственного назначения', 'Земля рекреационного назначения'];
        $коммерция = ['Офисное помещение', 'Торговое помещение', 'Ресторан/кафе', 'Гостиница/отель', 'Медицинское помещение', 'Складские помещения', 'Производственные помещения', 'Здание', 'Готовый бизнес', 'Помещение свободного назначения'];
        $паркинг = ['Паркинг', 'Машино-место', 'Гараж', 'Бокс'];

        return [
            'Продажа комнат' => $комнаты,
            'Аренда комнат' => $комнаты,
            'Продажа квартир' => $квартиры,
            'Аренда квартир' => $квартиры,
            'Продажа домов' => $дома,
            'Аренда домов' => $дома,
            'Продажа земли' => $земля,
            'Аренда земли' => $земля,
            'Продажа коммерции' => $коммерция,
            'Аренда коммерции' => $коммерция,
            'Продажа паркинг/гараж' => $паркинг,
            'Аренда паркинг/гараж' => $паркинг,
        ];
    }

    /**
     * Маппинг в формате ID: deal_type_id → [property_type_id, ...]
     */
    public static function getDealTypePropertyTypeMapIds(): array
    {
        $map = static::getDealTypePropertyTypeMap();
        $dealTypes = static::getDealTypes()->keyBy('name');
        $propertyTypes = static::getPropertyTypes()->keyBy('name');

        $result = [];
        foreach ($map as $dealTypeName => $propertyTypeNames) {
            $dealType = $dealTypes->get($dealTypeName);
            if (!$dealType) {
                continue;
            }

            $propertyTypeIds = [];
            foreach ($propertyTypeNames as $ptName) {
                $pt = $propertyTypes->get($ptName);
                if ($pt) {
                    $propertyTypeIds[] = $pt->id;
                }
            }
            $result[$dealType->id] = $propertyTypeIds;
        }

        return $result;
    }
}
