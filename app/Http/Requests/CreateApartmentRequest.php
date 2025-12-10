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
            
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'neighborhood' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'bathrooms' => 'required|integer|min:1',
            'number_of_rooms' => 'required|integer|min:1',
            'price_per_month' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
            'apartment_image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'description' => 'nullable|string',
            'area' => 'required|integer|min:1',
        ];
    }
}
