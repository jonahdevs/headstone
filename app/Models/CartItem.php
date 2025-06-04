<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'customer_id',
        'memorial_id',
        'quantity',
        'font',
        'first_name',
        'last_name',
        'epitaph',
        'birth_date',
        'death_date',
        'instructions',
        'image',
        'terms'
    ];

    // relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function memorial(): BelongsTo
    {
        return $this->belongsTo(Memorial::class);
    }

    // events
    protected static function booted()
    {

        static::creating(function (self $cartItem) {
            $cartItem->terms ??= false;
            $cartItem->customer_id ??= auth()->id();
        });

    }
}
