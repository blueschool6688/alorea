<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Scent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'category',
        'color_hex',
        'is_popular',
        'intensity',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'intensity' => 'integer',
    ];

    // Scent types
    const TYPE_TOP = 'top';
    const TYPE_MIDDLE = 'middle';
    const TYPE_BASE = 'base';

    public static function getTypes()
    {
        return [
            self::TYPE_TOP => 'Top Note (Hương đầu)',
            self::TYPE_MIDDLE => 'Middle Note (Hương giữa)',
            self::TYPE_BASE => 'Base Note (Hương cuối)'
        ];
    }

    // Scent categories - 4 categories chính
    public static function getCategories()
    {
        return [
            'floral' => 'Hoa',
            'woody' => 'Gỗ',
            'gourmand' => 'Ngọt',
            'fresh' => 'Tươi mát'
        ];
    }

    // Automatically generate slug when creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($scent) {
            if (empty($scent->slug)) {
                $scent->slug = Str::slug($scent->name);
            }
        });

        static::updating(function ($scent) {
            if ($scent->isDirty('name') && empty($scent->slug)) {
                $scent->slug = Str::slug($scent->name);
            }
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getTypeNameAttribute()
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function getCategoryNameAttribute()
    {
        return self::getCategories()[$this->category] ?? $this->category;
    }

    public function getIntensityLevelAttribute()
    {
        $levels = [
            1 => 'Rất nhẹ',
            2 => 'Nhẹ',
            3 => 'Nhẹ vừa',
            4 => 'Vừa phải',
            5 => 'Trung bình',
            6 => 'Vừa mạnh',
            7 => 'Mạnh',
            8 => 'Rất mạnh',
            9 => 'Cực mạnh',
            10 => 'Tối đa'
        ];

        return $levels[$this->intensity] ?? 'Không xác định';
    }

    // Relationships with products (if needed)
    public function products()
    {
        return $this->belongsToMany(Products::class, 'product_scents', 'scent_id', 'product_id');
    }
}
