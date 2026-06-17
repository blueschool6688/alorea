<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FlashSale extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'start_time',
        'end_time',
        'max_quantity',
        'type_all',
        'discount_type',
        'discount_value',
        'used_quantity',
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'type_all' => 'boolean',


    ];
    protected $appends = [
        'banner_url',
        'time_status'
    ];

    // Relationships
    public function products()
    {
        return $this->belongsToMany(Products::class, 'flash_sale_products', 'flash_sale_id', 'product_id');
    }

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
            ->format('webp')
            ->nonQueued()
            ->performOnCollections('image');
    }

    // Accessors
    public function getBannerUrlAttribute()
    {
        $media = $this->getFirstMedia('image');

        if (!$media) {
            return null;
        }

        if ($media->hasGeneratedConversion('image')) {
            return $media->getUrl('image');
        }
        return $media->getUrl();
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'draft' => 'bg-secondary',
            'active' => 'bg-success',
            'paused' => 'bg-warning',
            'ended' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'draft' => 'Nháp',
            'active' => 'Đang chạy',
            'paused' => 'Tạm dừng',
            'ended' => 'Đã kết thúc',
            default => 'Không xác định'
        };
    }

    public function getTimeStatusAttribute()
    {
        $now = Carbon::now();

        if ($now->lt($this->start_time)) {
            return 'upcoming'; // Sắp diễn ra
        } elseif ($now->between($this->start_time, $this->end_time)) {
            return 'running'; // Đang diễn ra
        } else {
            return 'expired'; // Đã hết hạn
        }
    }
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRunning($query)
    {
        $now = Carbon::now();
        return $query->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now);
    }

    public function scopeUpcoming($query)
    {
        $now = Carbon::now();
        return $query->where('start_time', '>', $now);
    }

    public function scopeExpired($query)
    {
        $now = Carbon::now();
        return $query->where('end_time', '<', $now);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Methods
    public function isActive()
    {
        return $this->status === 'active' && $this->time_status === 'running';
    }

    public function canAddProducts()
    {
        return in_array($this->status, ['draft', 'paused']);
    }

    public function updateStatus()
    {
        $now = Carbon::now();

        if ($this->status === 'active') {
            if ($now->gt($this->end_time)) {
                $this->update(['status' => 'ended']);
            } elseif ($this->max_quantity && $this->used_quantity >= $this->max_quantity) {
                $this->update(['status' => 'ended']);
            }
        }
    }

    // Static methods
    public static function getActiveFlashSales()
    {
        return self::active()->running()->get();
    }
}
