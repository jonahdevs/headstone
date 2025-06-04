<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'memorial_id',
        'customer_id',
        'memorial_order_id',
        'deleted_by',

        'rating',
        'review',
        'status',
    ];

    // relationships
    public function memorial(): BelongsTo
    {
        return $this->belongsTo(Memorial::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    //scopes
    #[Scope]
    protected function published(Builder $query)
    {
        $query->where('status', 'published');
    }

    // events
    public static function booted(): void
    {
        static::creating(function ($review) {
            $review->status = $review->status ?: 'pending';
        });

        static::deleting(function ($review) {
            $review->deleted_by = auth()->id();
        });
    }
}
