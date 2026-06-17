<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinces extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_code',
        'name',
        'short_name',
        'code',
        'place_type',
        'country',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function wards()
    {
        return $this->hasMany(Wards::class, 'province_code', 'province_code');
    }
}
