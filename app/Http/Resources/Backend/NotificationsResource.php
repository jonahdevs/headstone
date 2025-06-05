<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationsResource extends JsonResource
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
            "time" => $this->created_at->diffForHumans(),
            'data' => $this->data,
            'type' => $this->type,
            'read_at' => $this->read_at,
        ];
    }
}
