<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = "order_items";
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'sku',
        'total',
        'price',
        'pv_sales',
        'created_at',
        'updated_at'
    ];

    // public static function boot()
    
    // {
    //     parent::boot();

    //     static::created(function ($model) {
    //         $model->load('product');
    //         if ($model->product->has_flash_sale && !is_null($model->product?->flash_sale?->max_quantity)) {
    //             $model->product->flash_sale()->increment('used_quantity', $model->quantity);
    //         }
    //     });

    //     static::updating(function ($model) {
    //         $model->updated_at = $model->freshTimestamp();
    //     });
    // }


    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
