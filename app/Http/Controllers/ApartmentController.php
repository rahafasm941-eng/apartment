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
        $validatedData = $request->validated();
        $validatedData['user_id'] = $user_id;
        $apartment=apartment::create($validatedData);
        return response()->json($apartment, 201);
    }
    public function edit (UpdateApartmentRequest $request, Apartment $apartment)
    {
        $apartment->update($request->all());
        return response()->json($apartment,200);
    }
    public function destroy(Apartment $apartment)
    {
        $apartment->delete();
        return response()->json(null,204);
    }
}
