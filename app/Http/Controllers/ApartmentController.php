<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApartmentController extends Controller
{
    public function index()
{
    $apartments = Apartment::select('type','apartment_image', 'id', 'city', 'address')->get();
    return response()->json($apartments, 200);
}

    public function show(Apartment $apartment)
    {
        return response()->json($apartment, 200);
    }

    public function store(CreateApartmentRequest $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $validatedData = $request->validated();
        $validatedData['apartment_image'] = $validatedData['apartment_image'] ?? 'Unknown';
        $validatedData['user_id'] = $user_id;

        if ($user->role == 'owner') {
            $apartment = Apartment::create($validatedData);
            return response()->json($apartment, 201);
        }

        return response()->json(['message' => 'Only owners can create apartments'], 403);
    }

    public function edit(UpdateApartmentRequest $request, Apartment $apartment)
    {
        $user = Auth::user();

        if ($user->role !== 'owner' || $apartment->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $apartment->update($request->validated());

        return response()->json($apartment, 200);
    }

    public function destroy(Apartment $apartment)
    {
        $user = Auth::user();

        if ($user->role !== 'owner' || $apartment->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $apartment->delete();

        return response()->json(['message' => 'Apartment deleted successfully'], 200);
    }

    public function filteringApartments(Request $request)
    {
        $query = Apartment::query();

        if ($request->has('min_price')) {
            $query->where('price_per_month', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price_per_month', '<=', $request->max_price);
        }
        if ($request->has('min_number_of_rooms')) {
            $query->where('number_of_rooms', '>=', $request->min_number_of_rooms);
        }
        
        if ($request->has('neighborhood')) {
            $query->where('neighborhood', 'like', '%' . $request->neighborhood . '%');
        }
        if ($request->has('min_area')) {
            $query->where('area', '>=', $request->min_area);
        }

        if ($request->has('features')) {
            $requestedFeatures = explode(',', $request->features);

            foreach ($requestedFeatures as $feature) {
                $query->whereJsonContains('features', trim($feature));
            }
        }

        $apartments = $query->get();
        return response()->json($apartments, 200);
    }

    public function ApartmentDetails(Request $request)
{
    $request->validate([
        'apartment_id' => 'required|integer|exists:apartments,id',
    ]);
    $apartment = Apartment::find($request->apartment_id);
        $owner = User::find($apartment->user_id);

    return response()->json([
        'id' => $apartment->id,
        'address' => $apartment->address,
        'city' => $apartment->city,
        'neighborhood' => $apartment->neighborhood,
        'price_per_month' => $apartment->price_per_month,
        'number_of_rooms' => $apartment->number_of_rooms,
        'bathrooms' => $apartment->bathrooms,
        'area' => $apartment->area,
        'type' => $apartment->type,
        'features' => $apartment->features,
        'apartment_image' => $apartment->apartment_image,
        'details_image' => $apartment->details_image,
        'description' => $apartment->description,
        'is_available' => $apartment->is_available,
        'latitude' => $apartment->latitude,
        'longtude'=>$apartment->longitude,
        'phoneOfOwner'=>$owner->phone,
        'first name'=>$owner->first_name,
        'last name'=>$owner->last_name,

        
    ], 200);
}

    public function SearchByCity(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:100',
        ]);

        $apartments = Apartment::where('city', 'like', '%' . $request->city . '%')->get();

        return response()->json($apartments, 200);
    }
}