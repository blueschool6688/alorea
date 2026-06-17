<?php
// filepath: app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Categories extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'image',
        'status',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $appends = [
        'image_url'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('image')
            ->optimize()
            ->queued()
            ->format('webp')
            ->performOnCollections('image');
    }


    // Relationships
    public function parent()
    {
        return $this->belongsTo(Categories::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Categories::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function products()
    {
        return $this->hasMany(Products::class, 'category_id');
    }


    // Accessors & Mutators
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }


    public function getActiveProductsCountAttribute()
    {
        return $this->activeProducts()->count();
    }

    public function getIsParentAttribute()
    {
        return is_null($this->parent_id);
    }

    public function getHasChildrenAttribute()
    {
        return $this->children()->count() > 0;
    }

    public function getBreadcrumbAttribute()
    {
        $breadcrumb = collect([$this->name]);
        $parent = $this->parent;

        while ($parent) {
            $breadcrumb->prepend($parent->name);
            $parent = $parent->parent;
        }

        return $breadcrumb->implode(' > ');
    }

    public function getFullPathAttribute()
    {
        $path = collect([$this->slug]);
        $parent = $this->parent;

        while ($parent) {
            $path->prepend($parent->slug);
            $parent = $parent->parent;
        }

        return $path->implode('/');
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }


    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            // Prevent deletion if has children
            if ($category->children()->count() > 0) {
                throw new \Exception('Cannot delete category with subcategories.');
            }

            // Move products to parent category or uncategorized
            $category->products()->update([
                'category_id' => $category->parent_id ?? null
            ]);
        });
    }
}
