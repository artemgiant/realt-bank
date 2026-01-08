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

    /**
     * URL оригинального изображения
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk(config('photos.disk', 'public'))->url($this->path);
    }

    /**
     * URL миниатюры
     */
    public function getThumbnailUrlAttribute(): string
    {
        $thumbnailPath = $this->getThumbnailPath();

        if ($thumbnailPath && Storage::disk(config('photos.disk', 'public'))->exists($thumbnailPath)) {
            return Storage::disk(config('photos.disk', 'public'))->url($thumbnailPath);
        }

        // Если миниатюры нет - возвращаем оригинал
        return $this->url;
    }

    /**
     * Полный путь к файлу в файловой системе
     */
    public function getFullPathAttribute(): string
    {
        return Storage::disk(config('photos.disk', 'public'))->path($this->path);
    }

    /**
     * Расширение файла
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    // ========== Methods ==========

    /**
     * Установить это фото как главное
     */
    public function setAsMain(): void
    {
        // Снимаем is_main с других фото этого объекта
        static::where('property_id', $this->property_id)
            ->where('id', '!=', $this->id)
            ->update(['is_main' => false]);

        $this->update(['is_main' => true]);
    }

    /**
     * Получить путь к миниатюре
     */
    public function getThumbnailPath(): ?string
    {
        if (!$this->path) {
            return null;
        }

        $originals = config('photos.paths.originals', 'originals');
        $thumbnails = config('photos.paths.thumbnails', 'thumbnails');

        return str_replace("/{$originals}/", "/{$thumbnails}/", $this->path);
    }

    /**
     * Проверить существование файла
     */
    public function fileExists(): bool
    {
        return Storage::disk(config('photos.disk', 'public'))->exists($this->path);
    }

    /**
     * Проверить существование миниатюры
     */
    public function thumbnailExists(): bool
    {
        $thumbnailPath = $this->getThumbnailPath();
        return $thumbnailPath && Storage::disk(config('photos.disk', 'public'))->exists($thumbnailPath);
    }

    /**
     * Получить размер файла в байтах
     */
    public function getFileSize(): ?int
    {
        if (!$this->fileExists()) {
            return null;
        }

        return Storage::disk(config('photos.disk', 'public'))->size($this->path);
    }

    /**
     * Получить размер файла в человекочитаемом формате
     */
    public function getFileSizeFormatted(): string
    {
        $bytes = $this->getFileSize();

        if ($bytes === null) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }
}
