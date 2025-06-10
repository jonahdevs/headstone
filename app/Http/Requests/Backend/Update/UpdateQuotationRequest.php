<?php

namespace App\Http\Requests\Backend\Update;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermissionTo('update quotations');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quoted_price' => ['required'],
            'valid_until' => ['required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
            'message' => ['required', 'string'],
        ];
    }
}
