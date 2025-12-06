<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            
            'FirstName' => 'required|string|max:50',
            'LastName' => 'required|string|max:50',
            'dateOfBirth' => 'required|date|before_or_equal:' . now()->subYears(18)->toDateString(),
            'email' => 'required|string|email|max:255|unique:users',
            'phoneNumber' => 'required|string|digits:10|unique:users',
        ];
    }
    public function messages()
{
    return [
        'dateOfBirth.before_or_equal' => 'You must be at least 18 years old.',
    ];
}
}