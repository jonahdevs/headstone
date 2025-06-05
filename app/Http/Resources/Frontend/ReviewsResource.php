<?php

namespace App\Http\Resources\Frontend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewsResource extends JsonResource
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
            'review' => $this->review,
            'rating' => $this->rating,
            'customer' => $this->whenLoaded('customer', fn($customer) => $customer->only('id', 'avatar', 'name')),
        ];
    }
}
