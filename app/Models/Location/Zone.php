<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use SoftDeletes;

    protected $table = 'zones';

    protected $fillable = [
        'name',
        'city_id',
        'district_id',
        'state_id',
    ];

    // ========== Relationships ==========

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function streets(): HasMany
    {
        return $this->hasMany(Street::class);
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }
}
