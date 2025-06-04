<?php

namespace App\Http\Resources\Backend\Edit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditUserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'status' => $this->status,
            'roles' => $this->whenLoaded('roles', fn() => $this->roles->map(fn($role) => $role->only('id', 'name'))),
            'permissions' => $this->whenLoaded('permissions', fn() => $this->permissions->map(fn($permission) => $permission->only('id', 'name'))),
        ];
    }
}
