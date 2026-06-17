<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Collections extends Model implements HasMedia
{

    use HasFactory, InteractsWithMedia;
    protected $table = 'collections';

    protected $fillable = [
        'title',
        'slug',
        'sub_title',
        'description',
    ];

    protected $appends = [
        'video_url',
        'video_stream_url'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('video')
            ->singleFile()
            ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-m4v']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('video_data')
            ->performOnCollections('video')
            ->optimize()
            ->queued();
    }

    public function getVideoStreamUrlAttribute()
    {
        $media = $this->getFirstMedia('video');
        if ($media) {
            return route('api.stream.video', ['mediaId' => $media->id]);
        }
        return null;
    }



    public static function addLog(array $data): self
    {
        return self::create($data);
    }

    public function getVideoUrlAttribute()
    {
        return $this->getFirstMediaUrl('video') ?: null;
    }

    public function products()
    {
        return $this->belongsToMany(Products::class, 'collection_products', 'collection_id', 'product_id');
    }
}
