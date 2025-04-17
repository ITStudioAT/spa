<?php

namespace Itstudioat\Spa\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginStep2Request extends FormRequest
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
            'data.step' => 'required|in:3',
            'data.email' => 'required|email|max:255',
            'data.password' => 'required|string|min:8|max:255',
            'data.token_2fa' => 'required|string|size:6',
        ];
    }
}
