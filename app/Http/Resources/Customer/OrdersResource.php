<?php

namespace App\Http\Resources\Customer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class OrdersResource extends JsonResource
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
            'total' => Number::currency($this->total, 'kes'),
            'payment_method' => $this->payment->payment_method,
            'status' => $this->status,
            'payment_status' => $this->payment->status,
            'date' => Carbon::parse($this->created_at)->format('F j, Y'),
            'memorials' => $this->whenLoaded('memorials', function () {
                return $this->memorials->map(function ($memorial) {
                    return (object) [
                        'id' => $memorial->id,
                        'title' => $memorial->title,
                        'materials' => $memorial->materials ? $memorial->materials->pluck('name')->toArray() : [],
                    ];
                });
            }),
        ];
    }
}
