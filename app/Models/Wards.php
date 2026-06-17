<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wards extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_code',
        'name',
        'province_code',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function province()
    {
        return $this->belongsTo(Provinces::class, 'province_code', 'province_code');
    }
}
