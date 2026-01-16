<?php

namespace App\Models\Reference;

use App\Models\Contact\Contact;
use Database\Factories\Reference\DeveloperFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Developer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'developers';

    protected static function newFactory(): DeveloperFactory
    {
        return DeveloperFactory::new();
    }

    protected $fillable = [
        'name',
        'contact_id',
        'website',
        'description',
        'slug',
        'is_active',
        'source',
        'logo_path',
        'year_founded',
        'agent_notes',
        'name_translations',
        'description_translations',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'name_translations' => 'array',
        'description_translations' => 'array',
    ];

    // ========== Relationships ==========

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function complexes(): HasMany
    {
        return $this->hasMany(Complex::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(DeveloperLocation::class);
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // ========== Accessors ==========

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->logo_path);
        }

        return null;
    }

    /**
     * Get name in specific locale
     */
    public function getNameInLocale(string $locale = 'ru'): string
    {
        if ($this->name_translations && isset($this->name_translations[$locale])) {
            return $this->name_translations[$locale];
        }

        return $this->name;
    }

    /**
     * Get description in specific locale
     */
    public function getDescriptionInLocale(string $locale = 'ru'): ?string
    {
        if ($this->description_translations && isset($this->description_translations[$locale])) {
            return $this->description_translations[$locale];
        }

        return $this->description;
    }
}
