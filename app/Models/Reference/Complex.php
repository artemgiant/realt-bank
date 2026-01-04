<?php

namespace App\Models\Reference;

use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Zone;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complex extends Model
{
    use SoftDeletes;

    protected $table = 'complexes';

    protected $fillable = [
        'name',
        'developer_id',
        'city_id',
        'district_id',
        'zone_id',
        'description',
        'website',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
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

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDeveloper($query, int $developerId)
    {
        return $query->where('developer_id', $developerId);
    }

    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // ========== Accessors ==========

    /**
     * Название с застройщиком
     */
    public function getFullNameAttribute(): string
    {
        if ($this->developer) {
            return $this->developer->name . ' - ' . $this->name;
        }

        return $this->name;
    }
}
