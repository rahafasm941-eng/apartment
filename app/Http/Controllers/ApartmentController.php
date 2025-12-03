<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Models\Apartment;
use Illuminate\Http\Request;

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
        $apartment = Apartment::create($request->all());
        return response()->json($apartment,201);
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
