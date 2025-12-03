<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateApartmentRequest extends FormRequest
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
            'nameOfOwner' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'numberOfRooms' => 'required|integer|min:1',
            'rentPrice' => 'required|numeric|min:0',
            'isAvailable' => 'required|boolean',
            'imageUrl' => 'nullable|url',
            'description' => 'nullable|string',
            'area' => 'required|integer|min:1',
        ];
    }
}
