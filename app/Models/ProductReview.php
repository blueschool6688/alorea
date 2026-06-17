<?php
// filepath: app/Models/ProductReview.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'comment',
        'title',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'approved_at' => 'datetime',

    ];
    protected $appends = [
        'time_at'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTimeAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }


    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsGuestReviewAttribute()
    {
        return is_null($this->user_id);
    }

    public function getReviewerDisplayNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }

        // Anonymize guest names: "John Doe" -> "J***e"
        $name = $this->attributes['reviewer_name'];
        if (strlen($name) > 2) {
            return substr($name, 0, 1) . str_repeat('*', strlen($name) - 2) . substr($name, -1);
        }
        return $name;
    }

    public function getRatingStarsAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'approved' => '<span class="badge bg-success">Approved</span>',
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">Unknown</span>'
        };
    }

    // Methods
    public function approve($adminId = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId
        ]);

        // Update product rating
        $this->product->updateRating();
    }

    public function reject($adminNote = null, $adminId = null)
    {
        $this->update([
            'status' => 'rejected',
            'admin_note' => $adminNote,
            'approved_by' => $adminId
        ]);

        $this->product->updateRating();
    }
}
