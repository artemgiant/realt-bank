<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibZone extends Model
{
    protected $table = 'lib_zones';

    protected $fillable = [
        'name',
        'town_id',
        'region_id',
        'deleted',
    ];

    public $timestamps = false;

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('deleted', 0);
    }

    // ========== Relationships ==========

    public function town(): BelongsTo
    {
        return $this->belongsTo(LibTown::class, 'town_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(LibRegion::class, 'region_id');
    }

    public function streets(): HasMany
    {
        return $this->hasMany(LibStreet::class, 'zone_id');
    }
}
