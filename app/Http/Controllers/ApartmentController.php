<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Models\Apartment;
use App\Models\PendingApartment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApartmentController extends Controller
{
    public function index()
{
    $apartments = Apartment::select('type','apartment_image', 'id', 'city','rating', 'address')->get();
    return response()->json($apartments, 200);
}

 public function store(CreateApartmentRequest $request)
{
    $user = Auth::user();

    if ($user->role !== 'owner') {
        return response()->json(['message' => 'Only owners can create apartments'], 403);
    }

    $validatedData = $request->validated();

    //  remove files from validated array
    unset($validatedData['apartment_image'], $validatedData['details_image']);

    // Store main apartment image
    if ($request->hasFile('apartment_image')) {
        $validatedData['apartment_image'] =
            $request->file('apartment_image')->store('apartment_images', 'public');
    }

    // Store details images
    if ($request->hasFile('details_image')) {
        $images = [];
        foreach ($request->file('details_image') as $image) {
            $images[] = $image->store('details_images', 'public');
        }
        $validatedData['details_image'] = $images;
    }

    $validatedData['user_id'] = $user->id;

    $pendingApartment = PendingApartment::create($validatedData);

    return response()->json($pendingApartment, 201);
}



    public function update(Request $request)
{
    $user = Auth::user();

    if ($user->role !== 'owner') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'id' => 'required|exists:apartments,id',
        'address' => 'sometimes|required|string|max:255',
        'city' => 'sometimes|required|string|max:100',
        'neighborhood' => 'sometimes|required|string|max:100',
        'latitude' => 'sometimes|required|numeric|between:-90,90',
        'longitude' => 'sometimes|required|numeric|between:-180,180',
        'bathrooms' => 'sometimes|integer|min:1',
        'number_of_rooms' => 'sometimes|integer|min:1',
        'price_per_month' => 'sometimes|numeric|min:0',
        'type' => 'sometimes|string',
        'is_available' => 'sometimes|boolean',
        'apartment_image' => 'sometimes|image|mimes:png,jpg,jpeg|max:2048',
        'details_image' => 'sometimes|array',
        'details_image.*' => 'image|mimes:png,jpg,jpeg|max:2048',
        'description' => 'nullable|string',
        'area' => 'sometimes|integer|min:1',
        'features' => 'nullable|array',
    ]);

    $apartment = Apartment::findOrFail($validated['id']);

    if ($apartment->user_id !== $user->id) {
        return response()->json(['message' => 'You do not own this apartment'], 403);
    }

    unset(
        $validated['id'],
        $validated['apartment_image'],
        $validated['details_image']
    );

    /** Store apartment image */
    if ($request->hasFile('apartment_image')) {
        $validated['apartment_image'] =
            $request->file('apartment_image')->store('apartment_images', 'public');
    }

    /** Store details images */
    if ($request->hasFile('details_image')) {
        $images = [];
        foreach ($request->file('details_image') as $image) {
            $images[] = $image->store('details_images', 'public');
        }

        $validated['details_image'] = $images;

       
    }

    /** Create pending update */
    $pendingApartment = PendingApartment::create([
        ...$validated,
        'apartment_id' => $apartment->id,
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    return response()->json([
        'message' => 'Update request sent. Waiting for admin approval.',
        'pending_apartment' => $pendingApartment
    ], 200);
}



    public function destroy(Request $request)
{
    $request->validate([
        'apartment_id' => 'required|integer|exists:apartments,id',
    ]);

    $user = Auth::user();
    $apartment = Apartment::findOrFail($request->apartment_id);

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
        'rating'=>$apartment->rating,

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
    public function OwnerApartments()
    {
        $user = Auth::user();

        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Only owners can view their apartments'], 403);
        }

        $apartments = Apartment::where('user_id', $user->id)->get();

        return response()->json($apartments, 200);
    }
    public function getOwnerBookedApartment(){
        $user = Auth::user();

        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Only owners can view their booked apartments'], 403);
        }

        $apartments = Apartment::where('user_id', $user->id)
                        ->whereHas('bookings')
                        ->with('bookings')
                        ->where('bookings.status', 'approved')
                        ->get();

        return response()->json($apartments, 200);
    }
    public function countOwnerApartments(){
        $user = Auth::user();

        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Only owners can view their apartment count'], 403);
        }

        $count = Apartment::where('user_id', $user->id)->count();

        return response()->json(['apartment_count' => $count], 200);
    }
    public function countBookedApartments(){
        $user = Auth::user();

        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Only owners can view their booked apartment count'], 403);
        }

        $count = Apartment::where('user_id', $user->id)
                        ->whereHas('bookings', function ($query) {
                            $query->where('status', 'approved')
                                  ->where('end_date', '>=', now());
                        })
                        ->count();

        return response()->json(['booked_apartment_count' => $count], 200);
    }
    public function countPendingApartments(){
        $user = Auth::user();

        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Only owners can view their pending apartment count'], 403);
        }

        $count = Apartment::where('user_id', $user->id)
                        ->whereHas('bookings', function ($query) {
                            $query->where('status', 'pending');
                        })
                        ->count();

        return response()->json(['pending_apartment_count' => $count], 200);
    }
   public function countAvailableApartments()
{
    $user = Auth::user();

    if ($user->role !== 'owner') {
        return response()->json([
            'message' => 'Only owners can view their available apartment count'
        ], 403);
    }

    $count = Apartment::where('user_id', $user->id)
        ->whereDoesntHave('bookings', function ($query) {
            $query->whereIn('status', ['pending', 'updated', 'approved'])
                  ->where('end_date', '>=', now());
        })
        ->count();

    return response()->json([
        'available_apartment_count' => $count
    ], 200);
}
 public function AvailableApartments()
{
    $user = Auth::user();

    if ($user->role !== 'owner') {
        return response()->json([
            'message' => 'Only owners can view their available apartment count'
        ], 403);
    }

    $count = Apartment::where('user_id', $user->id)
        ->whereDoesntHave('bookings', function ($query) {
            $query->whereIn('status',  ['pending', 'updated', 'approved'])
                  ->where('end_date', '>=', now());
        })
        ->count();

    return response()->json([
        'available_apartment_count' => $count
    ], 200);
}

}