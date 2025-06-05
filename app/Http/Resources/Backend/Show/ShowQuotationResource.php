<?php

namespace App\Http\Resources\Backend\Show;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ShowQuotationResource extends JsonResource
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
            'status' => $this->status,
            'image' => $this->image,
            'budget' => $this->budget,
            'instructions' => $this->instructions,
            'deadline' => Carbon::parse($this->deadline)->format('F j, Y h:i A'),
            'date' => $this->created_at->format('F j, Y h:i A'),
            'response_data' => collect($this->response_data)->map(function ($response) {
                return (object) [
                    'current_user' => $response['response_by']['email'] === auth()->user()->email,
                    'message' => $response['message'] ?? '',
                    'quoted_price' => $response['quoted_price'] ? Number::currency($response['quoted_price'], 'kes') : null,
                    'valid_until' => isset($response['valid_until']) ? Carbon::parse($response['valid_until'])->format('F j, Y') : null,
                    'response_by' => $response['response_by']['email'] === auth()->user()->email ? 'You' : $response['response_by']['name'] ?? null,
                    'response_at' => isset($response['response_at']) ? Carbon::parse($response['response_at'])->diffForHumans() : null,
                ];
            }),
            'source' => $this->source,
            'memorial' => $this->whenLoaded('memorial', fn($memorial) => $memorial->title),
            'material' => $this->whenLoaded('material', fn($material) => $material->name),
            'customer' => $this->whenLoaded('customer', fn($customer) => (object) [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
            ]),
        ];
    }
}
