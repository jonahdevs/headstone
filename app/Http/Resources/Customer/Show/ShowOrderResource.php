<?php

namespace App\Http\Resources\Customer\Show;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ShowOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subtotal' => Number::currency($this->subtotal, 'kes'),
            'discount' => !$this->discount ?: Number::currency($this->discount, 'kes'),
            'total' => Number::currency($this->total, 'kes'),
            'status' => $this->status,
            'date' => $this->created_at->format('F j, Y h:i A'),
            'items' => $this->memorials_count,
            'memorials' => $this->whenLoaded('memorials', function () {
                return $this->memorials->map(function ($memorial) {
                    // $estimatedDelivery = isset($this->created_at) && isset($memorial->estimated_delivery_time)
                    //     ? Carbon::parse($memorial->estimated_delivery_time)->diffForHumans($this->created_at, [
                    //         'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
                    //         'parts' => 2, // e.g., "2 days 3 hours"
                    //         'short' => false,
                    //     ])
                    //     : null;
    
                    return (object) [
                        'id' => $memorial->id,
                        'title' => $memorial->title,
                        'total' => Number::currency($memorial->pivot->total, 'kes'),
                        'quantity' => $memorial->pivot->quantity,
                        'image' => $memorial->image,
                        'sku' => $memorial->sku,
                        'dimensions' => $memorial->dimensions,
                        'estimated_delivery' => $memorial->estimated_delivery_time,
                    ];
                });
            }),
            'customer' => $this->whenLoaded(
                'customer',
                fn($customer) =>
                (object) [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'address' => $customer->address,
                ]
            ),
            'payment' => $this->whenLoaded('payment', fn($payment) => (object) [
                'id' => $payment->id,
                'amount' => Number::currency($payment->amount, 'kes'),
                'transaction_id' => $payment->transaction_id,
                'method' => $payment->payment_method,
                'status' => $payment->status
            ]),
        ];
    }
}
