<?php

namespace App\Http\Resources\Frontend\Checkout;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class LatestOrderResource extends JsonResource
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
            'total' => Number::currency($this->total, 'kes'),
            'payment_method' => $this->payment->payment_method,
            'memorials' => MemorialsResource::collection($this->whenLoaded('memorials')),
        ];
    }
}
