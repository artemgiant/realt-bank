<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'symbol',
        'name',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ========== Static Methods ==========

    public static function getDefault(): ?self
    {
        return static::default()->first();
    }

    public static function getSelectOptions()
    {
        return static::active()->pluck('code', 'id');
    }

    // ========== Methods ==========

    public function setAsDefault(): void
    {
        // Знімаємо is_default з інших валют
        static::where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function format(float $amount): string
    {
        return $this->symbol . ' ' . number_format($amount, 2, '.', ' ');
    }
}
