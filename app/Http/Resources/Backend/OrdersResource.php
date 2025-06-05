<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class OrdersResource extends JsonResource
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
            'status' => $this->status,
            'placed_on' => $this->created_at->diffForHumans(),
            'total' => Number::currency($this->total, 'kes'),
            'items' => $this->memorials_count,
            'payment_status' => $this->whenLoaded('payment', fn() => $this->payment->status ?? 'unpaid'),
            'customer' => $this->whenLoaded('customer', fn() => $this->customer->email),
        ];
    }
}
