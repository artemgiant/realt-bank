<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Street extends Model
{
    use SoftDeletes;

    protected $table = 'streets';

    protected $fillable = [
        'name',
        'city_id',
        'district_id',
        'zone_id',
    ];

    // ========== Relationships ==========

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByDistrict($query, int $districtId)
    {
        return $query->where('district_id', $districtId);
    }

    public function scopeByZone($query, int $zoneId)
    {
        return $query->where('zone_id', $zoneId);
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

        if ($this->district) {
            $parts[] = $this->district->name;
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
