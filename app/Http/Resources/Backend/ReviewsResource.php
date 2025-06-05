<?php

namespace App\Http\Resources\Backend;

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
            "review" => $this->review,
            "rating" => $this->rating,
            "status" => $this->status,
            "created_at" => $this->created_at->format('F j, Y h:i A'),
            "customer" => $this->whenLoaded('customer', fn($customer) => $customer->email),
            "memorial" => $this->whenLoaded('memorial', fn($memorial) => $memorial->title),
        ];
    }
}
