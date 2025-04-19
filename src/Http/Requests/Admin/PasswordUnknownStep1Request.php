<?php

namespace Itstudioat\Spa\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUnknownStep1Request extends FormRequest
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
            'data.step' => 'required|in:PASSWORD_UNKNOWN_ENTER_EMAIL',
            'data.email' => 'required|email|max:255',
        ];
    }
}
