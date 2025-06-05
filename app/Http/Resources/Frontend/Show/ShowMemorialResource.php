<?php

namespace App\Http\Resources\Frontend\Show;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ShowMemorialResource extends JsonResource
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
            'description' => $this->description,
            'sku' => $this->sku,
            'tags' => $this->whenLoaded(
                'tags',
                fn() =>
                $this->tags->map(fn($tag) => $tag->only('id', 'name'))
            ),
            'price' => Number::currency($this->price, 'kes'),
            'sale_price' => $this->sale_price ? Number::currency($this->sale_price, 'kes') : null,
            'images' => $this->whenLoaded(
                'images',
                fn() =>
                $this->images->map(fn($image) => $image->only('id', 'path')),
            ),
        ];
    }
}
