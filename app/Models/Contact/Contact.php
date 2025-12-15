<?php

namespace App\Models\Contact;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'role',
        'phone',
        'phone_additional',
        'email',
        'has_whatsapp',
        'has_viber',
        'has_telegram',
        'notes',
    ];

    protected $casts = [
        'has_whatsapp' => 'boolean',
        'has_viber' => 'boolean',
        'has_telegram' => 'boolean',
    ];

    // ========== Relationships ==========

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    // ========== Scopes ==========

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // ========== Accessors ==========

    public function getFormattedPhoneAttribute(): string
    {
        // Базове форматування телефону
        return $this->phone;
    }

    public function getMessengersAttribute(): array
    {
        $messengers = [];

        if ($this->has_whatsapp) {
            $messengers[] = 'whatsapp';
        }
        if ($this->has_viber) {
            $messengers[] = 'viber';
        }
        if ($this->has_telegram) {
            $messengers[] = 'telegram';
        }

        return $messengers;
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->has_whatsapp) {
            return null;
        }
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        return "https://wa.me/{$phone}";
    }

    public function getViberLinkAttribute(): ?string
    {
        if (!$this->has_viber) {
            return null;
        }
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        return "viber://chat?number={$phone}";
    }

    public function getTelegramLinkAttribute(): ?string
    {
        if (!$this->has_telegram) {
            return null;
        }
        // Telegram може використовувати username або телефон
        return null; // Потребує окремого поля для username
    }

    public function getPropertiesCountAttribute(): int
    {
        return $this->properties()->count();
    }

    // ========== Static Methods ==========

    public static function getSelectOptions()
    {
        return static::orderBy('name')
            ->get()
            ->mapWithKeys(fn ($contact) => [
                $contact->id => $contact->name . ' (' . $contact->phone . ')'
            ]);
    }
}
