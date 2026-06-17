<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Setting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        // 'logo_name',
        'address',
        'hotline',
        'email_contact',
        'link_social_facebook',
        'link_social_tiktok',
        'link_social_youtube',
        'link_social_instagram',
        'banner_is_image',
        // 'banner_image',
        // 'banner_video',
        'title_banner',
        'sub_title_banner',
        'color_title_banner',
        'color_subtitle_banner',
        // 'introduce_video_manufacture',
        // 'introduce_video_design',
        'video_tiktok_review',
        'shipping_fee',
        'discount_global'
    ];

    protected $casts = [
        'banner_is_image' => 'boolean',
    ];

    protected $appends = [
        'logo_url',
        'logo_thumb_url',
        'banner_image_url',
        'banner_thumb_urls',
        'banner_responsive_urls',
        'intro_video_design_url',
        'intro_video_manufacture_url',
        'intro_video_design_stream_url',
        'intro_video_manufacture_stream_url'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml']);

        $this->addMediaCollection('banner_image')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);

        $this->addMediaCollection('intro_videos_design')
            ->singleFile()
            ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime']);

        $this->addMediaCollection('intro_videos_manufacture')
            ->singleFile()
            ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('logo-thumb')
            ->optimize()
            ->format('webp')
            ->performOnCollections('logo');

        $this->addMediaConversion('banner-thumb')
            ->optimize()
            ->width(1900)
            ->height(800)
            ->fit(Fit::Contain, 1900, 800)
            ->performOnCollections('banner_image');

        $this->addMediaConversion('video')
            ->performOnCollections('intro_videos_design', 'intro_videos_manufacture')
            ->optimize();
    }

    public function getLogoUrlAttribute()
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }

    public function getLogoThumbUrlAttribute()
    {
        return $this->getFirstMediaUrl('logo', 'logo-thumb') ?: null;
    }

    public function getBannerImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('banner_image') ?: null;
    }

    public function getBannerThumbUrlsAttribute()
    {
        return $this->getMedia('banner_image')->map(function ($media) {
            if ($media->hasGeneratedConversion('banner-thumb')) {
                return $media->getUrl('banner-thumb');
            }
            return $media->getUrl();
        })->toArray();
    }

    public function getBannerResponsiveUrlsAttribute()
    {
        $media = $this->getFirstMedia('banner_image');
        if (!$media) {
            return null;
        }

        return $media->getResponsiveImageUrls('banner-thumb');
    }

    public function getIntroVideoDesignUrlAttribute()
    {
        return $this->getFirstMediaUrl('intro_videos_design') ?: null;
    }

    public function getIntroVideoManufactureUrlAttribute()
    {
        return $this->getFirstMediaUrl('intro_videos_manufacture') ?: null;
    }

    public function getIntroVideoDesignStreamUrlAttribute()
    {
        $media = $this->getFirstMedia('intro_videos_design');
        if ($media) {
            return route('api.stream.video', ['mediaId' => $media->id]);
        }
        return null;
    }

    public function getIntroVideoManufactureStreamUrlAttribute()
    {
        $media = $this->getFirstMedia('intro_videos_manufacture');
        if ($media) {
            return route('api.stream.video', ['mediaId' => $media->id]);
        }
        return null;
    }

    // Get first setting record (single settings table)
    public static function getSettings()
    {
        return Cache::remember('site_settings', 3600, function () {
            return self::first() ?? new self();
        });
    }

    // Clear cache when updating
    public static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('site_settings');
        });

        static::deleted(function () {
            Cache::forget('site_settings');
        });
    }
}
