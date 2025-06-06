<?php

namespace App\Models;

use App\Jobs\ProcessNewQuotation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    /** @use HasFactory<\Database\Factories\QuotationFactory> */
    use HasFactory, BroadcastsEvents;

    protected $fillable = [
        'customer_id',
        'memorial_id',
        'material_id',
        'deleted_by',

        // user details
        'name',
        'email',
        'phone',
        'address',

        'budget',
        'dimensions',
        'reference_image',
        'inscription',
        'note',
        'deadline',
        'status',
        'source',

        'response_data',
    ];

    protected $casts = [
        'response_data' => 'array',
    ];


    // relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id')->withDefault([
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }

    public function memorial(): BelongsTo
    {
        return $this->belongsTo(Memorial::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
    public function responseBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'response_by');
    }
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // scopes
    #[Scope]
    protected function pending($query)
    {
        return $query->where('status', 'pending');
    }


    public function broadcastOn()
    {
        return [
            new Channel('admins'),
        ];
    }

    public function broadcastAs(string $event): string|null
    {
        return match ($event) {
            'created' => 'quotation.created',
            'updated' => 'quotation.updated',
            'deleted' => 'quotation.deleted',
            default => null,
        };
    }

    // events
    public static function booted(): void
    {
        static::creating(function (self $quotation) {
            $quotation->status = $quotation->status ?: 'pending';
        });

        static::created(function (self $quotation) {
            ProcessNewQuotation::dispatch($quotation);
        });

        static::updating(function (self $quotation) {
            $quotation->status = !$quotation->isDirty('response_data') ?: 'responded';
        });

        static::deleting(function (self $quotation) {
            $quotation->deleted_by = auth()->id();
        });
    }
}
