<?php

namespace App\Models\Reference;

use App\Models\Contact\Contact;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\State;
use App\Models\Location\Street;
use App\Models\Location\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'companies';

    protected static function newFactory(): \Database\Factories\CompanyFactory
    {
        return \Database\Factories\CompanyFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'name_translations',
        'description_translations',
        'country_id',
        'state_id',
        'city_id',
        'district_id',
        'zone_id',
        'street_id',
        'building_number',
        'office_number',
        'website',
        'edrpou_code',
        'company_type',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'name_translations' => 'array',
        'description_translations' => 'array',
    ];

    // ========== Relationships: Contacts (полиморфная many-to-many) ==========

    /**
     * Контакты компании (полиморфная many-to-many через contactables)
     */
    public function contacts(): MorphToMany
    {
        return $this->morphToMany(Contact::class, 'contactable')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Получить основной контакт (директор)
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

    // ========== Relationships: Offices ==========

    public function offices(): HasMany
    {
        return $this->hasMany(CompanyOffice::class)->orderBy('sort_order');
    }

    public function activeOffices(): HasMany
    {
        return $this->hasMany(CompanyOffice::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    // ========== Relationships: Location ==========

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
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

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
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

    public function scopeByType($query, string $type)
    {
        return $query->where('company_type', $type);
    }

    // ========== Accessors ==========

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
     * Полный адрес
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
        if ($this->office_number) {
            $parts[] = 'оф. ' . $this->office_number;
        }
        if ($this->city) {
            $parts[] = $this->city->name;
        }
        if ($this->state) {
            $parts[] = $this->state->name;
        }

        return implode(', ', $parts) ?: '-';
    }

    /**
     * Название на определенном языке
     */
    public function getNameInLocale(string $locale = 'ru'): string
    {
        if ($this->name_translations && isset($this->name_translations[$locale])) {
            return $this->name_translations[$locale];
        }
        return $this->name;
    }

    /**
     * Описание на определенном языке
     */
    public function getDescriptionInLocale(string $locale = 'ru'): ?string
    {
        if ($this->description_translations && isset($this->description_translations[$locale])) {
            return $this->description_translations[$locale];
        }
        return null;
    }

    /**
     * Количество офисов
     */
    public function getOfficesCountAttribute(): int
    {
        return $this->offices()->count();
    }
}
