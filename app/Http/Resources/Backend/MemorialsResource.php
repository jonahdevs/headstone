<?php

namespace App\Http\Resources\Backend;

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
            'description' => $this->description,
            'price' => Number::currency($this->price, 'kes'),
            'sale_price' => $this->sale_price ? Number::currency($this->sale_price, 'kes') : null,
            'status' => $this->status,
            'image' => $this->image,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
