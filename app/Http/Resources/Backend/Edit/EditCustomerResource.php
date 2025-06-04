<?php

namespace App\Http\Resources\Backend\Edit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditCustomerResource extends JsonResource
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
            'address' => $this->address,
            'country' => $this->country,
            'city' => $this->city,
            'avatar' => $this->avatar,
            'status' => $this->status,
        ];
    }
}
