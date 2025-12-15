<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Developer extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'phone',
        'email',
        'website',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function complexes(): HasMany
    {
        return $this->hasMany(Complex::class);
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

    // ========== Accessors ==========

    public function getComplexesCountAttribute(): int
    {
        return $this->complexes()->count();
    }
}
