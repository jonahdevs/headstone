<?php

namespace App\Models;

use App\Jobs\ProcessNewOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'deleted_by',

        'transaction_id',
        'reference_id',
        'amount',
        'paid_at',
        'payment_method',
        'description',
        'currency',
        'status',

        'payment_receipt'
    ];

    // relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // events
    public static function booted(): void
    {
        static::creating(function ($transaction) {
            $transaction->status = $transaction->status ?: 'pending';
            $transaction->paid_at = $transaction->payment_date ?: now();
        });

        static::updated(function (self $transaction) {
            if ($transaction->status == 'success') {
                $order = $transaction->order;
                ProcessNewOrder::dispatch($order);
            }
        });

        static::deleting(function ($transaction) {
            $transaction->deleted_by = auth()->id();
        });
    }
}
