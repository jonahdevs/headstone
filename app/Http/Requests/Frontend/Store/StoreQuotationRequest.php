<?php

namespace App\Http\Requests\Frontend\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'memorial' => ['required', 'integer', 'exists:memorials,id'],
            'material' => ['required', 'integer', 'exists:materials,id'],
            'dimensions' => ['required', 'string'],
            'inscription' => ['nullable', 'string'],
            'budget' => ['required', 'string'],
            'deadline' => ['required', 'date'],
            'note' => ['nullable', 'string'],
            'reference_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
            'terms' => ['required', 'accepted']
        ];
    }
}
