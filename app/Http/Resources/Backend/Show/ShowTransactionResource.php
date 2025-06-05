<?php

namespace App\Http\Resources\Backend\Show;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ShowTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'transaction_id' => $this->transaction_id,
            'amount' => Number::currency($this->amount, 'kes'),
            'paid_at' => Carbon::parse($this->paid_at)->format('F j, Y h:i A'),
            'payment_method' => $this->payment_method,
            'currency' => $this->currency,
            'status' => $this->status,
            'customer' => $this->whenLoaded('customer', fn($customer) => (object) [
                'id' => $customer->id,
                'email' => $customer->email,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'address' => $customer->address,
            ]),
            'order' => $this->whenLoaded('order', fn($order) => (object) [
                'id' => $order->id,
                'date' => $order->created_at->format('F j, Y h:i A'),
                'subtotal' => Number::currency($order->subtotal, 'kes'),
                'discount' => !$order->discount ?: Number::currency($order->discount, 'kes'),
                'total' => Number::currency($order->total, 'kes'),
                'memorials' => $order->memorials->map(
                    fn($memorial) =>
                    (object) [
                        'id' => $memorial->id,
                        'title' => $memorial->title,
                        'quantity' => $memorial->pivot->quantity,
                        'price' => Number::currency($memorial->pivot->price, 'kes'),
                        'discount' => !$memorial->pivot->discount ? null : Number::currency($memorial->pivot->discount, 'kes'),
                        'total' => Number::currency($memorial->pivot->total, 'kes'),
                    ]
                )
            ]),
        ];
    }
}
