<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            
            'nameOfOwner' => 'sometimes|string|max:100',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'numberOfRooms' => 'sometimes|integer|min:1',
            'rentPrice' => 'sometimes|numeric|min:0',
            'isAvailable' => 'sometimes|boolean',
            'imageUrl' => 'nullable|url',
            'description' => 'nullable|string',
            'area' => 'sometimes|integer|min:1',
        ];
    }
}
