<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class TransactionsResource extends JsonResource
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
            'paid_at' => $this->created_at->format('F j, Y h:i A'),
            'customer' => $this->whenLoaded('customer', fn($customer) => $customer->email),
            'order_id' => $this->order_id,
            'status' => $this->status,
        ];
    }
}
