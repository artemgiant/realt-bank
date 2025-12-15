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

    public const TYPE_DEAL_TYPE = 'deal_type';           // Тип угоди
    public const TYPE_DEAL_KIND = 'deal_kind';           // Вид угоди
    public const TYPE_BUILDING_TYPE = 'building_type';   // Тип будівлі
    public const TYPE_PROPERTY_TYPE = 'property_type';   // Тип нерухомості
    public const TYPE_CONDITION = 'condition';           // Стан
    public const TYPE_WALL_TYPE = 'wall_type';           // Тип стін
    public const TYPE_HEATING_TYPE = 'heating_type';     // Опалення
    public const TYPE_ROOM_COUNT = 'room_count';         // Кількість кімнат
    public const TYPE_BATHROOM_COUNT = 'bathroom_count'; // Кількість ванних
    public const TYPE_CEILING_HEIGHT = 'ceiling_height'; // Висота стелі
    public const TYPE_FEATURE = 'feature';               // Особливості
    public const TYPE_CONTACT_TAG = 'contact_tag';       // Теги контактів

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

    // ========== Helper for Select Options ==========

    public static function getSelectOptions(string $type): Collection
    {
        return static::getByType($type)->pluck('name', 'id');
    }
}
