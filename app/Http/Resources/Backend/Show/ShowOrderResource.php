<?php

namespace App\Http\Resources\Backend\Show;

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
            "id" => $this->id,
            'subtotal' => Number::currency($this->subtotal, 'kesh'),
            'discount' => !$this->discount ?: Number::currency($this->discount, 'kes'),
            'total' => Number::currency($this->total, 'kes'),
            'date' => $this->created_at->format('F j, Y h:i A'),
            'status' => $this->status,
            'memorials' => $this->whenLoaded(
                'memorials',
                fn() =>
                $this->memorials->map(
                    fn($memorial) => (object) 
                    [
                        'id' => $memorial->pivot->id,
                        'quantity' => $memorial->pivot->quantity,
                        'title' => $memorial->title,
                        'unit_price' => Number::currency($memorial->pivot->price, 'kes'),
                        'discount' => !$memorial->pivot->discount ?: Number::currency($memorial->pivot->discount, 'kes'),
                        'total' => Number::currency($memorial->pivot->total, 'kes'),
                    ]
                )
            ),
            'customer' => $this->whenLoaded(
                'customer',
                fn() =>
                (object) [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                    'address' => $this->customer->address,
                ]
            ),
            'transaction' => $this->whenLoaded(
                'payment',
                fn($transaction) =>
                (object) [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'method' => $transaction->payment_method,
                    'status' => $transaction->status,
                    'amount' => Number::currency($transaction->amount, 'kes'),
                    'date' => $transaction->created_at->format('F j, Y h:i A'),
                ]
            ),
        ];
    }
}
