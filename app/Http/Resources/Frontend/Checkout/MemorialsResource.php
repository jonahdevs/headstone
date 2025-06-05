<?php

namespace App\Http\Resources\Frontend\Checkout;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class MemorialsResource extends JsonResource
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
            'title' => $this->title,
            'estimated_delivery' => $this->estimated_delivery_time,
            'quantity' => $this->pivot->quantity,
            'total' => Number::currency($this->pivot->total, 'kes'),
            'materials' => $this->materials ? $this->materials->pluck('name')->toArray() : [
            ]
        ];
    }
}
