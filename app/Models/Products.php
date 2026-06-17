<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class Products extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sku',
        'barcode',
        'category_id',
        'stock',
        'status',
        'is_featured',
        'scrent_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'type',
        'rating',
        'review_count',
        'attributes',
        // Technical specifications
        'concentration',
        'volume_ml',
        'longevity',
        'sillage',
        'main_ingredients'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'gallery' => 'array',
        'attributes' => 'array',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'stock' => 'integer',
        'rating' => 'decimal:1',
        'review_count' => 'integer',
        'volume_ml' => 'integer'

    ];

    protected $dates = [
        'deleted_at'
    ];


    protected $appends = [
        'formatted_price',
        'main_image_url',
        'gallery_urls',
        'technical_arr',
        'has_flash_sale',
        'flash_sale_price',
        'flash_sale_discount_type',
        'flash_sale_discount'
    ];

    protected $with = ['category'];

    public function flash_sale()
    {
        return $this->belongsToMany(FlashSale::class, 'flash_sale_products', 'product_id', 'flash_sale_id')
            ->whereDate('flash_sales.start_time', '<=', now())
            ->where(function ($query) {
                $query->whereNull('flash_sales.end_time')
                    ->orWhereDate('flash_sales.end_time', '>', now());
            })
            ->where('flash_sales.status', 'active')
            ->orderBy('flash_sales.start_time', 'desc');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function scrents()
    {
        return $this->belongsTo(Scent::class, 'scrent_id');
    }

    public function collections()
    {
        return $this->belongsToMany(Collections::class, 'collection_products', 'product_id', 'collection_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . 'đ';
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->format('webp')
            ->quality(85)
            ->optimize()
            ->performOnCollections('main_image', 'gallery')
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(800)
            ->format('webp')
            ->quality(85)
            ->optimize()
            ->performOnCollections('main_image', 'gallery')
            ->nonQueued();
    }

    public function getTechnicalArrAttribute()
    {
        $technical = [];

        // Nồng độ tinh dầu
        if (!empty($this->concentration)) {
            $technical['concentration'] = [
                'label' => 'Nồng độ tinh dầu',
                'value' => $this->concentration,
            ];
        }

        // Dung tích
        if (!empty($this->volume_ml)) {
            $technical['volume_ml'] = [
                'label' => 'Dung tích',
                'value' => $this->volume_ml . 'ml',
            ];
        }

        if (!empty($this->longevity)) {
            $technical['longevity'] = [
                'label' => 'Độ lưu hương',
                'value' => $this->longevity,
            ];
        }
        if (!empty($this->sillage)) {
            $technical['sillage'] = [
                'label' => 'Độ tỏa hương',
                'value' => $this->sillage,
            ];
        }
        if (!empty($this->main_ingredients)) {
            $technical['main_ingredients'] = [
                'label' => 'Thành phần chính',
                'value' => $this->main_ingredients,
            ];
        }

        return $technical;
    }

    public function getMainImageUrlAttribute()
    {
        $media = $this->getFirstMedia('main_image');

        if (!$media) {
            return null;
        }

        if ($media->hasGeneratedConversion('medium')) {
            return $media->getUrl('medium');
        }
        return $media->getUrl();
    }

    public function getGalleryUrlsAttribute()
    {
        return $this->getMedia('gallery')->map(function ($media) {
            if ($media->hasGeneratedConversion('medium')) {
                return $media->getUrl('medium');
            }
            return $media->getUrl();
        })->toArray();
    }


    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function updateRating()
    {
        $avgRating = $this->reviews->where('status', 'approved')->avg('rating');
        $reviewCount = $this->reviews->where('status', 'approved')->count();

        $this->update([
            'rating' => $avgRating ? round($avgRating, 1) : 0,
            'review_count' => $reviewCount
        ]);
    }

    public function canBePurchased()
    {
        return $this->status === 'active' && $this->stock > 0;
    }

    public function getAvailableStock()
    {
        $reservedStock = $this->orderItems()
            ->whereHas('order', function ($query) {
                $query->whereIn('status', ['pending', 'processing']);
            })
            ->sum('quantity');

        return max(0, $this->stock - $reservedStock);
    }

    // Additional review methods
    public function getRatingBreakdown()
    {
        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $this->approvedReviews()->where('rating', $i)->count();
            $percentage = $this->review_count > 0 ? round(($count / $this->review_count) * 100) : 0;

            $breakdown[$i] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }
        return $breakdown;
    }

    public function hasUserReviewed($userId)
    {
        return $this->reviews()->where('user_id', $userId)->exists();
    }

    public static function getTypeData()
    {
        return [
            'men' => 'Nam',
            'women' => 'Nữ',
            'unisex' => 'Unisex',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = 'PRD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function getHasFlashSaleAttribute()
    {
        if ($this->relationLoaded('flash_sale')) {
            $flashSale = $this->flash_sale->first();
            return $flashSale !== null;
        }


        // Check for type_all flash sales or specific product flash sales
        $hasActiveFlashSale = FlashSale::where('status', 'active')
            ->whereDate('start_time', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_time')
                    ->orWhereDate('end_time', '>', now());
            })
            ->whereHas('products', function ($q) {
                $q->where('product_id', $this->id);
            })
            ->where(function ($query) {
                $query->where('type_all', true)
                    ->orWhereHas('products', function ($q) {
                        $q->where('product_id', $this->id);
                    });
            })
            ->where(function ($query) {
                $query->whereNull('max_quantity')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('max_quantity')
                            ->whereRaw('used_quantity < max_quantity');
                    });
            })
            ->exists();
        return $hasActiveFlashSale;
    }

    public function getFlashSalePriceAttribute()
    {
        if (!$this->has_flash_sale) {
            return null;
        }

        // Get active flash sale
        $flashSale = $this->getActiveFlashSale();

        if (!$flashSale) {
            return null;
        }
        return $this->calculateFlashSalePrice($flashSale);
    }

    public function getFlashSaleDiscountAttribute()
    {
        if (!$this->has_flash_sale) {
            return null;
        }

        $flashSale = $this->getActiveFlashSale();

        if (!$flashSale) {
            return null;
        }

        return $flashSale ? $flashSale->discount_value : null;
    }

    public function getFlashSaleDiscountTypeAttribute()
    {
        if (!$this->has_flash_sale) {
            return null;
        }

        $flashSale = $this->getActiveFlashSale();

        return $flashSale ? $flashSale->discount_type : null;
    }

    // Helper methods
    private function getActiveFlashSale()
    {
        // First check loaded relationship
        if ($this->relationLoaded('flash_sale')) {
            return $this->flash_sale->first();
        }
        $typeAllFlashSale = FlashSale::where('status', 'active')
            ->where('type_all', true)
            ->where('start_time', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_time')
                    ->orWhere('end_time', '>', now());
            })
            ->orderBy('start_time', 'desc')
            ->first();

        if ($typeAllFlashSale) {
            return $typeAllFlashSale;
        }

        // Check for specific product flash sale
        return $this->flash_sale()->first();
    }

    private function calculateFlashSalePrice($flashSale)
    {
        $originalPrice = $this->price;
        $discountAmount = 0;

        if ($flashSale->discount_type === 'percent') {
            $discountAmount = ($originalPrice * $flashSale->discount_value) / 100;
        } else if ($flashSale->discount_type === 'fixed') {
            $discountAmount = min($flashSale->discount_value, $originalPrice);
        }

        return max(0, $originalPrice - $discountAmount);
    }
}
