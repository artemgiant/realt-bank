<?php

namespace App\Models\Employee;

use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use App\Models\Reference\Dictionary;
use App\Models\User;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employees';

    protected static function newFactory(): EmployeeFactory
    {
        return EmployeeFactory::new();
    }

    protected $fillable = [
        'user_id',
        'company_id',
        'office_id',
        'position_id',
        'status_id',
        'tag_ids',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'birthday',
        'passport',
        'inn',
        'comment',
        'photo_path',
        'active_until',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tag_ids' => 'array',
        'birthday' => 'date',
        'active_until' => 'datetime',
    ];

    /**
     * Accessors to append to model's array form.
     */
    protected $appends = [
        'full_name',
    ];

    // ========== Relationships ==========

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(CompanyOffice::class, 'office_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'position_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'status_id');
    }

    // ========== Accessors ==========

    /**
     * Полное имя сотрудника
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->last_name,
            $this->first_name,
            $this->middle_name,
        ]);

        return implode(' ', $parts) ?: 'Без имени';
    }

    /**
     * Короткое имя (Фамилия И.О.)
     */
    public function getShortNameAttribute(): string
    {
        $name = $this->last_name ?? '';

        if ($this->first_name) {
            $name .= ' ' . mb_substr($this->first_name, 0, 1) . '.';
        }

        if ($this->middle_name) {
            $name .= mb_substr($this->middle_name, 0, 1) . '.';
        }

        return trim($name) ?: 'Без имени';
    }

    /**
     * URL фото
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path) {
            return Storage::disk('public')->url($this->photo_path);
        }
        return null;
    }

    /**
     * Теги сотрудника (из Dictionary)
     */
    public function getTagsAttribute()
    {
        if (empty($this->tag_ids)) {
            return collect();
        }

        return Dictionary::whereIn('id', $this->tag_ids)
            ->where('type', Dictionary::TYPE_AGENT_TAG)
            ->get();
    }

    // ========== Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByOffice($query, int $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    public function scopeByPosition($query, int $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    public function scopeByStatus($query, int $statusId)
    {
        return $query->where('status_id', $statusId);
    }
}
