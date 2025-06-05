<?php

namespace App\Http\Resources\Backend\Edit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditMemorialResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'dimensions' => $this->dimensions,
            'weight' => $this->weight,
            'estimated_delivery_time' => $this->estimated_delivery_time,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', fn() => $this->category->name),
            'materials' => $this->whenLoaded(
                'materials',
                fn() =>
                $this->materials->map(fn($material) => $material->only('id', 'name'))
            ),
            'tags' => $this->whenLoaded(
                'tags',
                fn() =>
                $this->tags->map(fn($tag) => $tag->only('id', 'name'))
            ),
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'image' => $this->image,
            'status' => $this->status,
            'images' => $this->whenLoaded(
                'images',
                fn() =>
                $this->images->map(fn($image) => $image->only('id', 'path'))
            ),
        ];
    }
}
