<?php

namespace App\Http\Resources\Backend\Edit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditRoleResource extends JsonResource
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
            'permissions' => $this->whenLoaded('permissions', fn() => $this->permissions->map(fn($permission) => $permission->only('id', 'name')))
        ];
    }
}
