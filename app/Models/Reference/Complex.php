<?php

namespace App\Models\Reference;

use App\Models\Contact\Contact;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Zone;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Complex extends Model
{
    use SoftDeletes;

    protected $table = 'complexes';

    protected $fillable = [
        'name',
        'developer_id',
        'city_id',
        'district_id',
        'zone_id',
        'description',
        'website',
        'company_website',
        'materials_url',
        'agent_notes',
        'special_conditions',
        'housing_class_id',
        'logo_path',
        'photos',
        'plans',
        'name_translations',
        'description_translations',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'name_translations' => 'array',
        'description_translations' => 'array',
        'photos' => 'array',
        'plans' => 'array',
    ];

    // ========== Relationships ==========

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Класс жилья (из справочника)
     */
    public function housingClass(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'housing_class_id');
    }

    /**
     * Контакты комплекса (полиморфная many-to-many)
     */
    public function contacts(): MorphToMany
    {
        return $this->morphToMany(Contact::class, 'contactable')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Получить URL фото по индексу
     */
    public function getPhotoUrl(int $index = 0): ?string
    {
        if ($this->photos && isset($this->photos[$index])) {
            return Storage::disk('public')->url($this->photos[$index]);
        }
        return null;
    }

    /**
     * Получить URL плана по индексу
     */
    public function getPlanUrl(int $index = 0): ?string
    {
        if ($this->plans && isset($this->plans[$index])) {
            return Storage::disk('public')->url($this->plans[$index]);
        }
        return null;
    }

    /**
     * Получить все URL фото
     */
    public function getPhotoUrlsAttribute(): array
    {
        if (!$this->photos) {
            return [];
        }
        return array_map(function ($path) {
            return Storage::disk('public')->url($path);
        }, $this->photos);
    }

    /**
     * Получить все URL планов
     */
    public function getPlanUrlsAttribute(): array
    {
        if (!$this->plans) {
            return [];
        }
        return array_map(function ($path) {
            return Storage::disk('public')->url($path);
        }, $this->plans);
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

    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // ========== Accessors ==========

    /**
     * Название с застройщиком
     */
    public function getFullNameAttribute(): string
    {
        if ($this->developer) {
            return $this->developer->name . ' - ' . $this->name;
        }

        return $this->name;
    }

    /**
     * URL логотипа
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path) {
            return Storage::disk('public')->url($this->logo_path);
        }

        return null;
    }

    /**
     * Получить основной контакт через полиморфную связь
     */
    public function getPrimaryContactAttribute(): ?Contact
    {
        if ($this->relationLoaded('contacts')) {
            return $this->contacts->firstWhere('pivot.role', 'primary')
                ?? $this->contacts->first();
        }

        return $this->contacts()->wherePivot('role', 'primary')->first()
            ?? $this->contacts()->first();
    }

    /**
     * Получить название на определенном языке
     */
    public function getNameInLocale(string $locale = 'ru'): string
    {
        if ($this->name_translations && isset($this->name_translations[$locale])) {
            return $this->name_translations[$locale];
        }

        return $this->name;
    }

    /**
     * Получить описание на определенном языке
     */
    public function getDescriptionInLocale(string $locale = 'ru'): ?string
    {
        if ($this->description_translations && isset($this->description_translations[$locale])) {
            return $this->description_translations[$locale];
        }

        return $this->description;
    }
}
