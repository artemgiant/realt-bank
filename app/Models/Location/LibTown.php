<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibTown extends Model
{
    protected $table = 'lib_towns';

    protected $fillable = [
        'name',
        'deleted',
    ];

    public $timestamps = false;

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('deleted', 0);
    }

    // ========== Relationships ==========

    public function regions(): HasMany
    {
        return $this->hasMany(LibRegion::class, 'town_id');
    }

    public function zones(): HasMany
    {
        return $this->hasMany(LibZone::class, 'town_id');
    }

    public function streets(): HasMany
    {
        return $this->hasMany(LibStreet::class, 'town_id');
    }
}
