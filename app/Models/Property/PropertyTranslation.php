<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyTranslation extends Model
{

    use HasFactory;

    protected $fillable = [
        'property_id',
        'locale',
        'title',
        'description',
    ];

    // ========== Relationships ==========

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // ========== Scopes ==========

    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
}
