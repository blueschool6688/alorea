<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;

    protected $table = "orders";
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_address',
        'customer_phone',
        'province_id',
        'ward_id',
        'total',
        'shipping_address',
        'delivery_date',
        'shipping_fee',
        'payment_method',
        'payment_status',
        'status',
        'paid_at',
        'discount',
        'subtotal',
        'system_discount',
        'system_discount_percentage',
        'notes',
    ];

    protected $append = [
        'full_address',
        'time_at'
    ];



    public function getTimeAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }



    public function getFullAddressAttribute()
    {
        $province = $this->provinces ? $this->provinces->name : '';
        $ward = $this->ward ? $this->ward->name : '';
        $address = $this->shipping_address ?: $this->customer_address;
        return "{$address}, {$ward}, {$province}";
    }
    // 'status' =>  0. pending -> 1.confirmed -> 2.processing -> 3.shipped -> 4.delivered -> 5.cancelled

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'id');
    }

    public function provinces()
    {
        return $this->belongsTo(Provinces::class, 'province_id', 'province_code');
    }

    public function ward()
    {
        return $this->belongsTo(Wards::class, 'ward_id', 'ward_code');
    }
}
