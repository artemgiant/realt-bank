<?php

namespace App\Models\Property;

use App\Models\Contact\Contact;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Landmark;
use App\Models\Location\Region;
use App\Models\Location\Street;
use App\Models\Location\Zone;
use App\Models\Reference\Complex;
use App\Models\Reference\Currency;
use App\Models\Reference\Dictionary;
use App\Models\Reference\Section;
use App\Models\Reference\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Зв'язки
        'user_id',
        'contact_id',
        'source_id',
        'currency_id',
        
        // Комплекс
        'complex_id',
        'section_id',
        
        // Локація
        'country_id',
        'region_id',
        'city_id',
        'district_id',
        'zone_id',
        'street_id',
        'landmark_id',
        'building_number',
        'apartment_number',
        'location_name',
        'latitude',
        'longitude',
        
        // Довідники
        'deal_type_id',
        'deal_kind_id',
        'building_type_id',
        'property_type_id',
        'condition_id',
        'wall_type_id',
        'heating_type_id',
        'room_count_id',
        'bathroom_count_id',
        'ceiling_height_id',
        
        // Характеристики
        'area_total',
        'area_living',
        'area_kitchen',
        'area_land',
        'floor',
        'floors_total',
        'year_built',
        
        // Ціна
        'price',
        'commission',
        'commission_type',
        
        // Медіа
        'youtube_url',
        'external_url',
        
        // Налаштування
        'is_visible_to_agents',
        'notes',
        'agent_notes',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'area_total' => 'decimal:2',
        'area_living' => 'decimal:2',
        'area_kitchen' => 'decimal:2',
        'area_land' => 'decimal:2',
        'price' => 'decimal:2',
        'commission' => 'decimal:2',
        'is_visible_to_agents' => 'boolean',
    ];

    // ========== Relationships: User & Contact ==========

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    // ========== Relationships: Complex ==========

    public function complex(): BelongsTo
    {
        return $this->belongsTo(Complex::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    // ========== Relationships: Location ==========

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
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

    public function landmark(): BelongsTo
    {
        return $this->belongsTo(Landmark::class);
    }

    // ========== Relationships: Dictionaries ==========

    public function dealType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'deal_type_id');
    }

    public function dealKind(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'deal_kind_id');
    }

    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'building_type_id');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'property_type_id');
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'condition_id');
    }

    public function wallType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'wall_type_id');
    }

    public function heatingType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'heating_type_id');
    }

    public function roomCount(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'room_count_id');
    }

    public function bathroomCount(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'bathroom_count_id');
    }

    public function ceilingHeight(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'ceiling_height_id');
    }

    // ========== Relationships: Features (many-to-many) ==========

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Dictionary::class, 'property_features', 'property_id', 'feature_id');
    }

    // ========== Relationships: Media ==========

    public function translations(): HasMany
    {
        return $this->hasMany(PropertyTranslation::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class);
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByDealType($query, int $dealTypeId)
    {
        return $query->where('deal_type_id', $dealTypeId);
    }

    public function scopePriceRange($query, ?float $min, ?float $max)
    {
        if ($min) {
            $query->where('price', '>=', $min);
        }
        if ($max) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    // ========== Accessors ==========

    public function getMainPhotoAttribute(): ?PropertyPhoto
    {
        return $this->photos->firstWhere('is_main', true) 
            ?? $this->photos->first();
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->street) {
            $parts[] = $this->street->name;
        }
        if ($this->building_number) {
            $parts[] = $this->building_number;
        }
        if ($this->city) {
            $parts[] = $this->city->name;
        }

        return implode(', ', $parts);
    }

    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price) {
            return '-';
        }

        $symbol = $this->currency?->symbol ?? '$';
        return $symbol . ' ' . number_format($this->price, 0, '.', ' ');
    }

    // ========== Methods ==========

    public function getTranslation(string $locale): ?PropertyTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }
}
