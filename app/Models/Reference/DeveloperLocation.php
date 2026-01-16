<?php

namespace App\Models\Reference;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperLocation extends Model
{
    protected $table = 'developer_locations';

    protected $fillable = [
        'developer_id',
        'location_type',
        'location_id',
        'location_name',
        'full_location_name',
    ];

    // ========== Relationships ==========

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    /**
     * Получить связанную локацию (полиморфная связь вручную)
     */
    public function getLocationAttribute()
    {
        return match ($this->location_type) {
            'country' => Country::find($this->location_id),
            'state' => State::find($this->location_id),
            'city' => City::find($this->location_id),
            default => null,
        };
    }

    // ========== Scopes ==========

    public function scopeCountries($query)
    {
        return $query->where('location_type', 'country');
    }

    public function scopeStates($query)
    {
        return $query->where('location_type', 'state');
    }

    public function scopeCities($query)
    {
        return $query->where('location_type', 'city');
    }
}
