<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PostStoreRequest extends FormRequest
{
    /**
     * Only Admin and Agents are authorized
     */
    public function authorize(): bool
    {
        return auth()->user()->is_admin || (auth()->user()->is_agent && auth()->user()->status === "accepted");
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException('Must Be Admin Or Accepted Agent!');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:65535',
            'street' => 'nullable|string|max:255',
            'township' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state_or_division' => 'required|string|max:255',
            'price' => 'required|integer|max:999999999999999',
            'width' => 'required|string|max:255',
            'length' => 'required|string|max:255',
            'status' => ['required', Rule::in(['rent', 'sell'])],
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'required_with:images|string'
        ];
    }
}