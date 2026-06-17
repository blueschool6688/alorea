<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Blog extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'category_id',
        'author_name',
        'status',
        'is_featured',
        'published_at',
        'source_url',
        'reading_time',
        'social_shares'
    ];

    protected $casts = [
        'meta_keywords' => 'array',
        'social_shares' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'time_at'
    ];

    protected $appends = [
        'featured_image_url',
        'time_at'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('image_featured')
            ->optimize()
            ->format('webp')
            ->queued()
            ->performOnCollections('image');
    }


    // Relationships
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    // Accessors
    public function getFeaturedImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public function getTimeAtAttribute()
    {
        if ($this->published_at) {
            return \Carbon\Carbon::parse($this->published_at)->diffForHumans();
        }
        return null;
    }


    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'draft' => 'bg-secondary',
            'published' => 'bg-success',
            'archived' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'draft' => 'Nháp',
            'published' => 'Đã xuất bản',
            'archived' => 'Lưu trữ',
            default => 'Không xác định'
        };
    }

    public function getReadingTimeAttribute()
    {
        if ($this->attributes['reading_time']) {
            return $this->attributes['reading_time'];
        }

        // Auto calculate reading time (average 200 words per minute)
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200));
    }

    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Auto generate excerpt from content
        return Str::limit(strip_tags($this->content), 160);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }



    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    // Methods
    public function isPublished()
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    public function canBeViewed()
    {
        return $this->isPublished();
    }

    public function incrementViews($userId = null, $ipAddress = null)
    {
        // Record view
        $this->views()->create([
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
            'viewed_at' => now()
        ]);

        // Update counter
        $this->increment('views_count');
    }

    public function getRelatedPosts($limit = 4)
    {
        $query = self::published()
            ->where('id', '!=', $this->id)
            ->orderBy('published_at', 'desc');

        // Try to find posts in the same category first
        if ($this->category_id) {
            $related = $query->where('category_id', $this->category_id)
                ->limit($limit)
                ->get();

            if ($related->count() >= $limit) {
                return $related;
            }
        }

        // If not enough, get any recent posts
        return $query->limit($limit)->get();
    }

    public static function getFeaturedPosts($limit = 5)
    {
        return self::published()->featured()->recent($limit)->get();
    }

    public static function getRecentPosts($limit = 10)
    {
        return self::published()->recent($limit)->get();
    }

    // Boot method for auto-generating slug and published_at
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }

            if ($blog->status === 'published' && !$blog->published_at) {
                $blog->published_at = now();
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('status') && $blog->status === 'published' && !$blog->published_at) {
                $blog->published_at = now();
            }
        });
    }
}
