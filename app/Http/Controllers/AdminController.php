<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\PendingApartment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function pendingUsers()
    {
        return User::where('is_approved', false)
                   ->where('role', '!=', 'admin')
                   ->get();
    } 
    public function approvedUsers()
    {
        return User::where('is_approved', true)
                   ->where('role', '!=', 'admin')
                   ->get();
    }
    public function approveUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot approve admin account'], 403);
        }

        $user->is_approved = true;
        $user->save();

        return response()->json(['message' => 'User approved successfully']);
    }

    public function rejectUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot reject admin'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User rejected and removed']);
    }
    public function allUsers()
    {
        return User::where('role', '!=', 'admin')->get();
    }
    public function allApartments(){
        return Apartment::all();
    }
    public function deleteApartment(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
        ]);

        $apartment = Apartment::findOrFail($request->apartment_id);
        $apartment->delete();

        return response()->json(['message' => 'Apartment deleted successfully']);
    }
    public function countPendingUsers()
    {
        $count = User::where('is_approved', false)
                     ->where('role', '!=', 'admin')
                     ->count();

        return response()->json(['pending_users_count' => $count]);
    }
    public function countBookedApartments()
    {
        $count = Apartment::whereHas('bookings', function ($query) {
            $query->where('status', 'approved')
                 ->where('end_date', '>=', now());
        })->count();

        return response()->json(['booked_apartments_count' => $count]);
    }
    public function countBookingUsers()
    {
        $count = User::whereHas('bookings', function ($query) {
            $query->where('status', 'approved')
            ->where('end_date', '>=', now());
        })->count();

        return response()->json(['booking_users_count' => $count]);
    }
    public function countAllApartments()
    {
        $count = Apartment::count();

        return response()->json(['total_apartments_count' => $count]);
    }
    public function approveApartment(Request $request)
{
    try{
    $request->validate([
        'pending_apartment_id' => 'required|exists:pending_apartments,id'
    ]);

    $pending = PendingApartment::findOrFail($request->pending_apartment_id);

    // خذ البيانات بدون user_id
    $data = collect($pending->getAttributes())->except([
    'id',
    'apartment_id',
    'status',
    'created_at',
    'updated_at'
])->toArray();

$data['user_id'] = $pending->user_id; // هنا نضمن أنه ينتقل


    if ($pending->apartment_id) {
        // تحديث شقة موجودة
        $apartment = Apartment::findOrFail($pending->apartment_id);
        $apartment->update($data);
        $oldapartment=Apartment::where('id',$pending->apartment_id)->first();
        $oldapartment->delete();
        $apartment = Apartment::create($data);
        

    } else {
        // الإنشاء مع فرض user_id صراحة
        $apartment = new Apartment($data);
        $apartment->user_id = $pending->user_id;
        $apartment->save();
    }

    $pending->delete();

    return response()->json([
        'message' => 'Apartment approved successfully',
        'apartment' => $apartment
    ]);}
    catch(\Exception $e){
        return response()->json(['error'=>$e->getMessage()],500);
    }
}


    public function pendingApartments(){
        $pending_apartments=PendingApartment::get();
        return response()->json(['pending apartments'=>$pending_apartments],200);
    }
}