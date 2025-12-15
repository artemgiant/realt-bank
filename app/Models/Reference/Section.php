<?php

namespace App\Models\Reference;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Region;
use App\Models\Location\Street;
use App\Models\Location\Zone;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'complex_id',
        'name',
        
        // Локація
        'country_id',
        'region_id',
        'city_id',
        'district_id',
        'zone_id',
        'street_id',
        'building_number',
        
        // Інше
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ========== Relationships ==========

    public function complex(): BelongsTo
    {
        return $this->belongsTo(Complex::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    // Location relationships
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

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

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeByComplex($query, int $complexId)
    {
        return $query->where('complex_id', $complexId);
    }

    // ========== Accessors ==========

    public function getFullNameAttribute(): string
    {
        return $this->complex->name . ' - ' . $this->name;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->street) {
            $parts[] = $this->street->name;
        }
        if ($this->building_number) {
            $parts[] = $this->building_number;
        }
        if ($this->city) {
            $parts[] = $this->city->name;
        }

        return implode(', ', $parts);
    }
}
