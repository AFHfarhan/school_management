<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    public const CATEGORY_SCHOOL = 'school';
    public const CATEGORY_BRANDING = 'branding';
    public const CATEGORY_ACADEMIC = 'academic';
    public const CATEGORY_REGISTRATION = 'registration';
    public const CATEGORY_PAYMENT = 'payment';
    public const CATEGORY_ATTENDANCE = 'attendance';

    public const DEFAULT_CATEGORIES = [
        self::CATEGORY_SCHOOL,
        self::CATEGORY_BRANDING,
        self::CATEGORY_ACADEMIC,
        self::CATEGORY_REGISTRATION,
        self::CATEGORY_PAYMENT,
        self::CATEGORY_ATTENDANCE,
    ];

    public const CODE_SCHOOL_PROFILE = 'school_profile';
    public const CODE_SCHOOL_BRANDING = 'school_branding';
    public const CODE_ACADEMIC_SETTING = 'academic_setting';
    public const CODE_REGISTRATION_SETTING = 'registration_setting';
    public const CODE_PAYMENT_SETTING = 'payment_setting';
    public const CODE_ATTENDANCE_SETTING = 'attendance_setting';

    public const DEFAULT_CODES = [
        self::CODE_SCHOOL_PROFILE,
        self::CODE_SCHOOL_BRANDING,
        self::CODE_ACADEMIC_SETTING,
        self::CODE_REGISTRATION_SETTING,
        self::CODE_PAYMENT_SETTING,
        self::CODE_ATTENDANCE_SETTING,
    ];

    protected $table = 'component_table';

    protected $fillable = [
        'code',
        'structure',
        'name',
        'description',
        'data',
        'category',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public $timestamps = true;

    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeNamed(Builder $query, string $name): Builder
    {
        return $query->where('name', $name);
    }

    public function scopeDefaultData(Builder $query): Builder
    {
        return $query->whereIn('code', self::DEFAULT_CODES);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getDataValue(string $key, mixed $default = null): mixed
    {
        $data = is_array($this->data) ? $this->data : (array) json_decode((string) $this->data, true);

        return $data[$key] ?? $default;
    }

    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->getDataValue($key, $default);
    }

    public function isMandatory(): bool
    {
        return $this->category === 'mandatory';
    }

    public function isDefaultComponent(): bool
    {
        return in_array($this->code, self::DEFAULT_CODES, true);
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function activate(): void
    {
        $this->forceFill(['is_active' => true])->save();
    }

    public function deactivate(): void
    {
        $this->forceFill(['is_active' => false])->save();
    }
}
