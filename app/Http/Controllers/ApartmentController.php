<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApartmentController extends Controller
{
    public function index()
    {
        $apartments = Apartment::all();
        return response()->json($apartments,200);
    }
    public function show(Apartment $apartment)
    {
        return response()->json($apartment,200);
    }
    public function store(CreateApartmentRequest $request)
    {

        $user_id=Auth::user()->id;
        $user=Auth::user();
        $validatedData = $request->validated(); 
               $validatedData['apartment_image'] = $validatedData['apartment_image'] ?? 'Unknown';

        $validatedData['user_id'] = $user_id;
        if($user->role=='owner'){
        $apartment=apartment::create($validatedData); 
        return response()->json($apartment, 201);
        }
        else{
            return response()->json(['message' => 'Only owners can create apartments'], 403);
        }
        
       
    }
    public function edit(UpdateApartmentRequest $request, Apartment $apartment)
{
    $user = Auth::user();

    // السماح للـ owner فقط بتعديل شقته
    if ($user->role !== 'owner' || $apartment->user_id !== $user->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $apartment->update($request->validated());

    return response()->json($apartment, 200);
}

    public function destroy(Apartment $apartment)
{
    $user = Auth::user();

    // السماح للـ owner فقط بحذف شقته
    if ($user->role !== 'owner' || $apartment->user_id !== $user->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $apartment->delete();

    return response()->json([
        'message' => 'Apartment deleted successfully'
    ], 200);
}

    public function filteringApartments(Request $request)
    {
        $query = Apartment::query();

        if ($request->has('min_price')) {
            $query->where('price_per_month', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price_per_month', '<=', $request->input('max_price'));
        }

        if ($request->has('min_number_of_rooms')) {
            $query->where('number_of_rooms', '>=', $request->input('min_number_of_rooms'));
        }


        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }
        if($request->has('neighborhood')){
            $query->where('neighborhood','like','%'.$request->input('neighborhood').'%');
        }
        if($request->has('min_area')){
            $query->where('area', '>=', $request->input('min_area'));

        }
        if ($request->has('features')) { 
        // إذا وصل string مثل "WiFi,Parking"
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

        $apartment = Apartment::find($request->input('apartment_id'));

        return response()->json($apartment, 200);
    }
}
    
