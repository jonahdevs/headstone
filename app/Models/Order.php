<?php

namespace App\Models;

use App\Notifications\OrderUpdated;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, SoftDeletes, BroadcastsEvents;

    protected $fillable = [
        'customer_id',
        'deleted_by',

        'subtotal',
        'discount',
        'shipping_fee',
        'tax',
        'total',
        'status',
        'transaction_summary_path'
    ];

    // relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function memorials(): BelongsToMany
    {
        return $this->belongsToMany(Memorial::class, 'memorial_order')
            ->withPivot([
                'quantity',
                'price',
                'discount',
                'shipping_fee',
                'tax',
                'total',
                'font',
                'first_name',
                'last_name',
                'epitaph',
                'DOB',
                'DOD',
                'instructions',
                'image',
                'status',
            ])
            ->withTimestamps();
    }
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    // scopes
    #[Scope]
    protected function pending($query)
    {
        return $query->where('status', 'pending');
    }

    #[Scope]
    protected function processing($query)
    {
        return $query->where('status', 'processing');
    }

    #[Scope]
    protected function completed($query)
    {
        return $query->where('status', 'completed');
    }

    #[Scope()]
    protected function cancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function broadcastOn()
    {
        return [
            new Channel('admins'),
            new PrivateChannel("App.Models.Order.$this->id"),
        ];
    }

    public function broadcastAs(string $event): string|null
    {
        return match ($event) {
            'created' => 'order.created',
            'updated' => 'order.updated',
            'deleted' => 'order.deleted',
            default => null,
        };
    }

    // events
    public static function booted(): void
    {
        static::creating(function ($order) {
            $order->status = $order->status ?: 'pending';
        });

        static::updated(function (self $order) {
            if ($order->isDirty('status')) {
                $order->customer->notify(new OrderUpdated($order));
            }
        });

        static::deleting(function ($order) {
            $order->deleted_by = auth()->id();
        });
    }
}
