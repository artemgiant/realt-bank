<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyDocument extends Model
{
    protected $fillable = [
        'property_id',
        'name',
        'path',
        'filename',
    ];

    // ========== Relationships ==========

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // ========== Accessors ==========

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getExtensionAttribute(): string
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    public function getSizeAttribute(): int
    {
        return Storage::size($this->path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
