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
        'contact_role',
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
        'contact_role' => 'array',
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

    // ========== Accessors: Роли контакта ==========

    /**
     * Названия ролей контакта (из JSON поля contact_role)
     */
    public function getContactRoleNamesAttribute(): string
    {
        if (empty($this->contact_role)) {
            return '-';
        }

        $roleNames = Dictionary::whereIn('id', $this->contact_role)
            ->where('type', Dictionary::TYPE_CONTACT_ROLE)
            ->pluck('name')
            ->toArray();

        return !empty($roleNames) ? implode(', ', $roleNames) : '-';
    }

    /**
     * Проверить, есть ли у контакта определённая роль
     */
    public function hasRole(int $roleId): bool
    {
        return is_array($this->contact_role) && in_array($roleId, $this->contact_role);
    }

    /**
     * Проверить, есть ли у контакта роль по slug
     */
    public function hasRoleBySlug(string $slug): bool
    {
        if (empty($this->contact_role)) {
            return false;
        }

        return Dictionary::whereIn('id', $this->contact_role)
            ->where('type', Dictionary::TYPE_CONTACT_ROLE)
            ->where('slug', $slug)
            ->exists();
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

    /**
     * Фильтр по ID роли (JSON contains)
     */
    public function scopeByRole($query, int $roleId)
    {
        return $query->whereJsonContains('contact_role', $roleId);
    }

    /**
     * Фильтр по slug роли
     */
    public function scopeByRoleSlug($query, string $slug)
    {
        $roleId = Dictionary::where('type', Dictionary::TYPE_CONTACT_ROLE)
            ->where('slug', $slug)
            ->value('id');

        if ($roleId) {
            return $query->whereJsonContains('contact_role', $roleId);
        }

        return $query->whereRaw('1 = 0'); // Роль не найдена
    }

    public function scopeOwners($query)
    {
        return $query->byRoleSlug(self::TYPE_OWNER);
    }

    public function scopeAgents($query)
    {
        return $query->byRoleSlug(self::TYPE_AGENT);
    }

    public function scopeDevelopers($query)
    {
        return $query->byRoleSlug(self::TYPE_DEVELOPER);
    }

    public function scopeDeveloperRepresentatives($query)
    {
        return $query->byRoleSlug(self::TYPE_DEVELOPER_REPRESENTATIVE);
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
     * Получить роли контактов для select (из dictionaries)
     */
    public static function getRoleOptions(): array
    {
        return Dictionary::getContactRoles()->pluck('name', 'id')->toArray();
    }
}
