<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $ignored_id = auth()->user()->id;
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:255', 'lowercase', 'not_regex:/\s/i',
                Rule::unique('users', 'username')->ignore($ignored_id)
            ], // must-not-include-space
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($ignored_id)
            ],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'phone' => [
                'required_with:is_agent', 'string', 'numeric',
                Rule::unique('users', 'phone')->ignore($ignored_id)
            ],
            'address' => ['required_with:is_agent', 'string', 'max:255']
        ];
    }
}
