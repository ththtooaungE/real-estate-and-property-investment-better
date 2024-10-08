<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255','lowercase', 'not_regex:/\s/i', 'unique:users,username'], // must-not-include-space
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'max:255'],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'is_agent' => ['nullable', 'boolean'],
            'phone' => ['required_with:is_agent' , 'string', 'numeric', 'unique:users,phone'],
            'address' => ['required_with:is_agent', 'string', 'max:255']
        ];
    }
}
