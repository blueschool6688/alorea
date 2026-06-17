<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BlogCategory extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'color',
        'icon',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
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
        $this->addMediaConversion('image_thumb')
            ->optimize()
            ->queued()
            ->format('webp')
            ->performOnCollections('image');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

    public function publishedBlogs()
    {
        return $this->hasMany(Blog::class, 'category_id')->published();
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Methods
    public function updatePostsCount()
    {
        $this->update(['posts_count' => $this->publishedBlogs()->count()]);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
