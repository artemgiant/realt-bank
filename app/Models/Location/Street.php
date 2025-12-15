<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Street extends Model
{
    protected $fillable = [
        'city_id',
        'district_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ========== Accessors ==========

    public function getFullNameAttribute(): string
    {
        $parts = [$this->name];
        
        if ($this->district) {
            $parts[] = $this->district->name;
        }
        
        $parts[] = $this->city->name;
        
        return implode(', ', $parts);
    }
}
