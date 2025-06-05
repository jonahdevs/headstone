<?php

namespace App\Http\Resources\Backend\Show;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'status' => $this->status,
            'review' => $this->review,
            'customer' => $this->whenLoaded('customer', fn($customer) => $customer->only('id', 'name', 'email', 'phone')),
            'memorial' => $this->whenLoaded('memorial', fn($memorial) => $memorial->title)
        ];
    }
}
