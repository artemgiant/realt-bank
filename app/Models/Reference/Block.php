<?php

namespace App\Models\Reference;

use App\Models\Location\Street;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model
{
    use SoftDeletes;

    protected $table = 'blocks';

    protected $fillable = [
        'name',
        'complex_id',
        'developer_id',
        'street_id',
        'slug',
        'building_number',
        'floors_total',
        'year_built',
        'is_active',
    ];

    protected $casts = [
        'floors_total' => 'integer',
        'year_built' => 'integer',
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function complex(): BelongsTo
    {
        return $this->belongsTo(Complex::class);
    }

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
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

    public function scopeByComplex($query, int $complexId)
    {
        return $query->where('complex_id', $complexId);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // ========== Accessors ==========

    /**
     * Полный адрес блока: Улица, номер дома
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->street) {
            $parts[] = $this->street->name;
        }

        if ($this->building_number) {
            $parts[] = $this->building_number;
        }

        return implode(', ', $parts);
    }

    /**
     * Название с комплексом: Комплекс - Блок
     */
    public function getFullNameAttribute(): string
    {
        if ($this->complex) {
            return $this->complex->name . ' - ' . $this->name;
        }

        return $this->name;
    }
}
