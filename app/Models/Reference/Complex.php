<?php

namespace App\Models\Reference;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complex extends Model
{
    protected $table = 'complexes';

    protected $fillable = [
        'developer_id',
        'name',
        'slug',
        'description',
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

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
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

    // ========== Static Methods ==========

    public static function getSelectOptions()
    {
        return static::active()->orderBy('name')->pluck('name', 'id');
    }

    // ========== Accessors ==========

    public function getPropertiesCountAttribute(): int
    {
        return $this->properties()->count();
    }

    public function getFullNameAttribute(): string
    {
        if ($this->developer) {
            return $this->name . ' (' . $this->developer->name . ')';
        }
        return $this->name;
    }
}
