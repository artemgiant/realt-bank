<?php

namespace App\Models\Reference;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ========== Static Methods ==========

    public static function getSelectOptions()
    {
        return static::active()->orderBy('name')->pluck('name', 'id');
    }
}
