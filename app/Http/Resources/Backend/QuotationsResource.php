<?php

namespace App\Http\Resources\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class QuotationsResource extends JsonResource
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
            'budget' => Number::currency($this->budget, 'kes'),
            'quoted_price' => $this->quoted_price ? Number::currency($this->quoted_price, 'kes') : null,
            'status' => $this->status,
            'deadline' => Carbon::parse($this->deadline)->toFormattedDateString(),
            'created_at' => $this->created_at->toFormattedDateString(),
            'customer' => $this->whenLoaded('customer', fn($customer) => $customer->email) ?? $this->name,
            'memorial' => $this->whenLoaded('memorial', fn($memorial) => $memorial->title),
            'material' => $this->whenLoaded('material', fn($material) => $material->name),
        ];
    }
}
