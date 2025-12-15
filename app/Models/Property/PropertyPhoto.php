<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyPhoto extends Model
{
    protected $fillable = [
        'property_id',
        'path',
        'filename',
        'sort_order',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ========== Relationships ==========

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // ========== Scopes ==========

    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // ========== Accessors ==========

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getThumbnailUrlAttribute(): string
    {
        // Можна додати логіку для thumbnails пізніше
        return $this->url;
    }

    // ========== Methods ==========

    public function setAsMain(): void
    {
        // Знімаємо is_main з інших фото цього об'єкта
        static::where('property_id', $this->property_id)
            ->where('id', '!=', $this->id)
            ->update(['is_main' => false]);

        $this->update(['is_main' => true]);
    }
}
