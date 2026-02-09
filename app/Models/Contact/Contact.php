<?php

namespace App\Models\Contact;

use App\Models\Property\Property;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use App\Models\Reference\Developer;
use App\Models\Reference\Dictionary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'contact_type',
        'tags',
        'telegram',
        'viber',
        'whatsapp',
        'passport',
        'inn',
        'photo',
        'comment',
    ];

    protected $casts = [
        'contact_type' => 'string',
    ];

    // ========== Константы типов контактов ==========

    const TYPE_OWNER = 'owner';
    const TYPE_AGENT = 'agent';
    const TYPE_DEVELOPER = 'developer';
    const TYPE_DEVELOPER_REPRESENTATIVE = 'developer_representative';

    const TYPES = [
        self::TYPE_OWNER => 'Владелец',
        self::TYPE_AGENT => 'Агент',
        self::TYPE_DEVELOPER => 'Девелопер',
        self::TYPE_DEVELOPER_REPRESENTATIVE => 'Представитель девелопера',
    ];

    // ========== Relationships ==========

    /**
     * Телефоны контакта (один контакт - много телефонов)
     */
    public function phones(): HasMany
    {
        return $this->hasMany(ContactPhone::class);
    }

    /**
     * Объекты недвижимости (полиморфная many-to-many)
     */
    public function properties(): MorphToMany
    {
        return $this->morphedByMany(Property::class, 'contactable')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Девелоперы (полиморфная many-to-many)
     */
    public function developers(): MorphToMany
    {
        return $this->morphedByMany(Developer::class, 'contactable')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Компании (полиморфная many-to-many)
     */
    public function companies(): MorphToMany
    {
        return $this->morphedByMany(Company::class, 'contactable')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Офисы компаний (полиморфная many-to-many)
     */
    public function companyOffices(): MorphToMany
    {
        return $this->morphedByMany(CompanyOffice::class, 'contactable')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Все связанные сущности (универсальный метод)
     * Использовать для получения всех contactables определённой модели
     */
    public function contactables(string $modelClass): MorphToMany
    {
        return $this->morphedByMany($modelClass, 'contactable')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Роли контакта (many-to-many через pivot таблицу)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Dictionary::class, 'contact_role', 'contact_id', 'role_id')
            ->where('dictionaries.type', Dictionary::TYPE_CONTACT_ROLE)
            ->withTimestamps();
    }

    // ========== Accessors: Имя ==========

    /**
     * Полное имя (ФИО)
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->last_name,
            $this->first_name,
            $this->middle_name,
        ]);

        return implode(' ', $parts) ?: '-';
    }

    /**
     * Короткое имя (Фамилия И.О.)
     */
    public function getShortNameAttribute(): string
    {
        $name = $this->last_name;

        if ($this->first_name) {
            $name .= ' ' . mb_substr($this->first_name, 0, 1) . '.';
        }
        if ($this->middle_name) {
            $name .= mb_substr($this->middle_name, 0, 1) . '.';
        }

        return $name ?: '-';
    }

    // ========== Accessors: Тип контакта ==========

    /**
     * Название типа контакта
     */
    public function getContactTypeNameAttribute(): string
    {
        return self::TYPES[$this->contact_type] ?? '-';
    }

    /**
     * Роли контакта как строка (для отображения)
     */
    public function getRolesNamesAttribute(): string
    {
        $roles = $this->roles()->pluck('name')->toArray();
        return !empty($roles) ? implode(', ', $roles) : '-';
    }

    // ========== Accessors: Телефоны ==========

    /**
     * Основной телефон
     */
    public function getPrimaryPhoneAttribute(): ?string
    {
        $primary = $this->phones->firstWhere('is_primary', true);

        return $primary?->phone ?? $this->phones->first()?->phone;
    }

    /**
     * Форматированный основной телефон
     */
    public function getFormattedPhoneAttribute(): string
    {
        return $this->primary_phone ?? '-';
    }

    // ========== Accessors: Теги ==========

    /**
     * Теги как массив
     */
    public function getTagsArrayAttribute(): array
    {
        if (empty($this->tags)) {
            return [];
        }

        return array_map('trim', explode(',', $this->tags));
    }

    // ========== Accessors: Мессенджеры ==========

    /**
     * Список доступных мессенджеров
     */
    public function getMessengersAttribute(): array
    {
        $messengers = [];

        if ($this->whatsapp) {
            $messengers[] = 'whatsapp';
        }
        if ($this->viber) {
            $messengers[] = 'viber';
        }
        if ($this->telegram) {
            $messengers[] = 'telegram';
        }

        return $messengers;
    }

    /**
     * Ссылка WhatsApp
     */
    public function getWhatsappLinkAttribute(): ?string
    {
        return $this->whatsapp ?: null;
    }

    /**
     * Ссылка Viber
     */
    public function getViberLinkAttribute(): ?string
    {
        return $this->viber ?: null;
    }

    /**
     * Ссылка Telegram
     */
    public function getTelegramLinkAttribute(): ?string
    {
        return $this->telegram ?: null;
    }

    // ========== Accessors: Счетчики ==========

    /**
     * Количество объектов у контакта
     */
    public function getPropertiesCountAttribute(): int
    {
        return $this->properties()->count();
    }

    // ========== Mutators ==========

    /**
     * Установка тегов из массива
     */
    public function setTagsFromArray(array $tags): void
    {
        $this->tags = implode(',', array_filter($tags));
    }

    // ========== Scopes ==========

    public function scopeByType($query, string $type)
    {
        return $query->where('contact_type', $type);
    }

    public function scopeOwners($query)
    {
        return $query->where('contact_type', self::TYPE_OWNER);
    }

    public function scopeAgents($query)
    {
        return $query->where('contact_type', self::TYPE_AGENT);
    }

    public function scopeDevelopers($query)
    {
        return $query->where('contact_type', self::TYPE_DEVELOPER);
    }

    public function scopeDeveloperRepresentatives($query)
    {
        return $query->where('contact_type', self::TYPE_DEVELOPER_REPRESENTATIVE);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('middle_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('inn', 'like', "%{$search}%")
                ->orWhereHas('phones', function ($pq) use ($search) {
                    $pq->where('phone', 'like', "%{$search}%");
                });
        });
    }

    // ========== Static Methods ==========

    /**
     * Опции для select
     */
    public static function getSelectOptions()
    {
        return static::with('phones')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->mapWithKeys(fn($contact) => [
                $contact->id => $contact->full_name . ' (' . ($contact->primary_phone ?? '-') . ')'
            ]);
    }

    /**
     * Получить типы контактов для select
     */
    public static function getTypeOptions(): array
    {
        return self::TYPES;
    }
}
