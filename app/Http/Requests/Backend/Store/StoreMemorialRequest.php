<?php

namespace App\Http\Requests\Backend\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemorialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermissionTo('create memorials');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255',],
            'slug' => ['nullable', 'string'],
            'sku' => ['nullable', 'string'],
            'dimensions' => ['nullable', 'string'],
            'weight' => ['nullable'],
            'estimated_delivery_time' => ['nullable', 'string'],
            'description' => ['required', 'string', 'min:50'],
            'category' => ['nullable', 'string', 'exists:categories,name'],
            'materials' => ['required', 'array'],
            'materials.*.id' => ['required', 'integer', 'exists:materials,id'],
            'materials.*.name' => ['string', 'exists:materials,name'],
            'tags' => ['required', 'array'],
            'tags.*.id' => ['required', 'integer', 'exists:tags,id'],
            'tags.*.name' => ['string', 'exists:tags,name'],
            'price' => ['required'],
            'sale_price' => ['nullable'],
            'stock' => ['required', 'integer'],
            'status' => ['required', 'in:draft,published'],
            'main_image' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ];
    }
}
