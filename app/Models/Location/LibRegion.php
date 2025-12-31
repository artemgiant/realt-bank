<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibRegion extends Model
{
    protected $table = 'lib_regions';

    protected $fillable = [
        'town_id',
        'name',
        'deleted',
        'id_1',
        'id_2',
        'dom_ria',
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

    public function zones(): HasMany
    {
        return $this->hasMany(LibZone::class, 'region_id');
    }

    public function streets(): HasMany
    {
        return $this->hasMany(LibStreet::class, 'region_id');
    }
}
