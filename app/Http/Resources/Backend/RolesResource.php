<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RolesResource extends JsonResource
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
            'users_count' => $this->users_count,
            'permissions_count' => $this->permissions_count,
            'users' => $this->whenLoaded('users', fn() => $this->users->map(fn($user) => $user->only('id', 'name', 'avatar')))
        ];
    }
}
