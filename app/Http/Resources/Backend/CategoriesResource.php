<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
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
            'items' => $this->memorials_count,
            'created_by' => $this->createdBy->email,
            'status' => $this->status,
            'date' => $this->created_at->toFormattedDateString(),
        ];
    }
}
