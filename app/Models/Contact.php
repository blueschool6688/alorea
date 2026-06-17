<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'subject',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ xử lý',
            'read' => 'Đã đọc',
            'replied' => 'Đã trả lời',
            'closed' => 'Đã đóng'
        ];

        return $labels[$this->status] ?? 'Không xác định';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'read' => 'info',
            'replied' => 'success',
            'closed' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }
}
