<?php

namespace App\Http\Requests\Backend\Update;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermissionTo('update users');
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
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user)],
            'phone' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:active,inactive,banned'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpg', 'max:5120'],
            'roles' => ['required', 'array'],
            'roles.*.name' => ['string', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*.name' => ['string', 'exists:permissions,name'],
        ];
    }
}
