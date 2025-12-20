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

    // ========== Helper for Select Options ==========

    public static function getSelectOptions(string $type): Collection
    {
        return static::getByType($type)->pluck('name', 'id');
    }
}
