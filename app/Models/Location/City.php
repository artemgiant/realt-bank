<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $table = 'cities';

    protected $fillable = [
        'name',
        'type',
        'state_id',
    ];

    // ========== Константы типов населённых пунктов ==========

    const TYPE_CITY = 'city';
    const TYPE_TOWN = 'town';
    const TYPE_VILLAGE = 'village';
    const TYPE_SETTLEMENT = 'settlement';

    const TYPES = [
        self::TYPE_CITY => 'Город',
        self::TYPE_TOWN => 'Посёлок городского типа',
        self::TYPE_VILLAGE => 'Село',
        self::TYPE_SETTLEMENT => 'Посёлок',
    ];

    // ========== Relationships ==========

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function streets(): HasMany
    {
        return $this->hasMany(Street::class);
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeCities($query)
    {
        return $query->where('type', self::TYPE_CITY);
    }

    public function scopeTowns($query)
    {
        return $query->where('type', self::TYPE_TOWN);
    }

    public function scopeVillages($query)
    {
        return $query->where('type', self::TYPE_VILLAGE);
    }

    // ========== Accessors ==========

    /**
     * Название типа населённого пункта
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? '-';
    }
}
