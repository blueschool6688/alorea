<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    use HasFactory;
    protected $table = 'transactions';

    protected $fillable = [
        'order_id',
        'transaction_no',
        'amount',
        'payment_method',
        'type',
        'payload',
    ];

    protected $casts = [
      'payload'=>'array',
    ];


    public static function addLog(array $data): self
    {
        return self::create($data);
    }

}
