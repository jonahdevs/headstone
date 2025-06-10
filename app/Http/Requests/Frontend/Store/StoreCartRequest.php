<?php

namespace App\Http\Requests\Frontend\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
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
            'memorial_id' => ['required', 'integer', 'exists:memorials,id'],
            'font' => ['required', 'string', 'in:nunito,amatic,pacifico,dancing,satisfy'],
            'first_name' => ['required', 'string', 'max:15'],
            'last_name' => ['required', 'string', 'max:15'],
            'DOB' => ['required'],
            'DOD' => ['required'],
            'epitaph' => ['required', 'string', 'max:32'],
            'instructions' => ['nullable', 'string'],
            'image' => ['nullable', 'image'],
            'terms' => ['boolean', 'accepted'],
        ];
    }
}
