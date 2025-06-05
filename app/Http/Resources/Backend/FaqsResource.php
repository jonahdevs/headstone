<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqsResource extends JsonResource
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
            'question' => $this->question,
            'response' => $this->response,
            'status' => $this->status,
            'date' => $this->created_at->toFormattedDateString(),
            'created_by' => $this->whenLoaded('createdBy', fn($user) => $user->email),
        ];
    }
}
