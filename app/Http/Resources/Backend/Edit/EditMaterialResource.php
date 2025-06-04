<?php

namespace App\Http\Resources\Backend\Edit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditMaterialResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'sku' => $this->sku,
            'description' => $this->description
        ];
    }
}
