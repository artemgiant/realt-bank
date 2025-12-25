<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactPhone extends Model
{
    protected $fillable = [
        'contact_id',
        'phone',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // ========== Relationships ==========

    /**
     * Контакт, которому принадлежит телефон
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    // ========== Methods ==========

    /**
     * Установить этот телефон как основной
     */
    public function setAsPrimary(): void
    {
        // Снимаем is_primary с других телефонов этого контакта
        static::where('contact_id', $this->contact_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        $this->update(['is_primary' => true]);
    }

    // ========== Scopes ==========

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
