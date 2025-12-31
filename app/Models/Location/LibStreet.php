<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibStreet extends Model
{
    protected $table = 'lib_streets';

    protected $fillable = [
        'name',
        'town_id',
        'region_id',
        'zone_id',
        'deleted',
    ];

    public $timestamps = false;

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('deleted', 0);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // ========== Relationships ==========

    public function town(): BelongsTo
    {
        return $this->belongsTo(LibTown::class, 'town_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(LibRegion::class, 'region_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(LibZone::class, 'zone_id');
    }

    // ========== Accessors ==========

    /**
     * Полный адрес: Улица, Зона, Район, Город
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [$this->name];

        if ($this->zone) {
            $parts[] = $this->zone->name;
        }

        if ($this->region) {
            $parts[] = $this->region->name;
        }

        return implode(', ', $parts);
    }

    /**
     * Короткий адрес: Улица, Зона
     */
    public function getShortAddressAttribute(): string
    {
        $parts = [$this->name];

        if ($this->zone) {
            $parts[] = $this->zone->name;
        }

        return implode(', ', $parts);
    }
}
