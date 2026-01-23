<?php

namespace App\Models\Reference;

use App\Models\Location\Street;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Block extends Model
{
    use HasFactory, SoftDeletes;

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
        'heating_type_id',
        'wall_type_id',
        'plan_path',
        'is_active',
        'source',
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

    /**
     * Тип отопления (из справочника)
     */
    public function heatingType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'heating_type_id');
    }

    /**
     * Тип стен (из справочника)
     */
    public function wallType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'wall_type_id');
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

    /**
     * URL плана блока
     */
    public function getPlanUrlAttribute(): ?string
    {
        if ($this->plan_path) {
            return Storage::disk('public')->url($this->plan_path);
        }

        return null;
    }
}
