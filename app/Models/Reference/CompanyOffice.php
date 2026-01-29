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
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyOffice extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): \Database\Factories\CompanyOfficeFactory
    {
        return \Database\Factories\CompanyOfficeFactory::new();
    }

    protected $table = 'company_offices';

    protected $fillable = [
        'company_id',
        'name',
        'country_id',
        'state_id',
        'city_id',
        'district_id',
        'zone_id',
        'street_id',
        'building_number',
        'office_number',
        'full_address',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Контакты офиса (полиморфная many-to-many через contactables)
     */
    public function contacts(): MorphToMany
    {
        return $this->morphToMany(Contact::class, 'contactable')
            ->withPivot('role')
            ->withTimestamps();
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

    // ========== Accessors ==========

    /**
     * Сформировать и вернуть полный адрес
     */
    public function getFullAddressComputedAttribute(): string
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

        return implode(', ', $parts) ?: '-';
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
