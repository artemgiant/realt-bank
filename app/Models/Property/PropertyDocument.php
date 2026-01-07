<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyDocument extends Model
{
    protected $fillable = [
        'property_id',
        'hash',
        'name',
        'path',
        'filename',
    ];

    // ========== Boot ==========

    protected static function boot()
    {
        parent::boot();

        // Автоматическая генерация hash при создании
        static::creating(function ($document) {
            $document->hash = bin2hex(random_bytes(32));
        });
    }

    // ========== Relationships ==========

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // ========== Accessors ==========

    /**
     * URL для скачивания документа
     */
    public function getUrlAttribute(): string
    {
        return route('documents.download', ['hash' => $this->hash]);
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
    }

    public function getSizeAttribute(): ?int
    {
        if (Storage::exists($this->path)) {
            return Storage::size($this->path);
        }
        return null;
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes === null) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Проверка является ли документ изображением
     */
    public function getIsImageAttribute(): bool
    {
        return in_array($this->extension, ['png', 'jpg', 'jpeg']);
    }

    /**
     * Проверка является ли документ PDF
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->extension === 'pdf';
    }
}
