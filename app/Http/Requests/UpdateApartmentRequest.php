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
            
            'address' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'neighborhood' => 'sometimes|required|string|max:100',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'bathrooms' => 'sometimes|required|integer|min:1',
            'number_of_rooms' => 'sometimes|required|integer|min:1',
            'price_per_month' => 'sometimes|required|numeric|min:0',
            'is_available' => 'sometimes|required|boolean',
            'image_url' => 'sometimes|required|image|mimes:png,jpg,jpeg|max:2048',
            'description' => 'nullable|string',
            'area' => 'sometimes|required|integer|min:1',
        ];
    }
}
