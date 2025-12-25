<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Apartment;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'غير مسجل الدخول'], 401);
        }
        
        $user_id = $user->id;
        $request->merge(['user_id' => $user_id]);
        
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'start_date'   => 'required|date_format:d-m-Y',
            'end_date'     => 'required|date_format:d-m-Y|after_or_equal:start_date',
        ]);

        try {
            DB::beginTransaction();
            
            $apartment_id = $request->input('apartment_id');
            $apartment = Apartment::findOrFail($apartment_id);
            $owner = User::find($apartment->user_id);
            
            if (!$owner) {
                DB::rollBack();
                return response()->json(['message' => 'مالك الشقة غير موجود'], 404);
            }
            
            $owner_phone = $owner->phone ?? $owner->phone_number;
            $owner_name = trim($owner->first_name . ' ' . $owner->last_name);
            
            $start_new = Carbon::createFromFormat('d-m-Y', $request->input('start_date'));
            $end_new = Carbon::createFromFormat('d-m-Y', $request->input('end_date'));
            $renter = User::find($user_id);
            
            $days = $start_new->diffInDays($end_new);
            $days = ($days == 0) ? 1 : $days;
            $total_price = $days * $apartment->price;

            // التحقق من الحجوزات المتداخلة
            $conflict = Booking::where('apartment_id', $apartment_id)
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->where(function ($query) use ($start_new, $end_new) {
                    $query->where('start_date', '<', $end_new)
                          ->where('end_date', '>', $start_new);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                DB::rollBack();
                return response()->json(['message' => 'هذه الشقة محجوزة بالفعل في التواريخ المختارة.'], 409);
            }

            $booking = Booking::create([
                'apartment_id' => $apartment_id,
                'user_id' => $user_id,
                'start_date' => $start_new->format('Y-m-d'),
                'end_date' => $end_new->format('Y-m-d'),
                'total_price' => $total_price,
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء طلب الحجز بنجاح',
                'booking_summary' => [
                    'id' => $booking->id,
                    'renter_name' => trim($renter->first_name . ' ' . $renter->last_name),
                    'renter_phone' => $renter->phone_number ?? $renter->phone,
                    'owner_phone' => $owner_phone,
                    'owner_name' => $owner_name,
                    'apartment_title' => $apartment->title,
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                    'number_of_days' => $days,
                    'total_price' => $total_price,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at->format('d/m/Y H:i')
                ]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'حدث خطأ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'غير مسجل الدخول'], 401);
    }
    
    $user_id = $user->id;
    $id = $request->id;
    
    if (!$id) {
        return response()->json(['message' => 'معرف الحجز مطلوب'], 400);
    }
    
    try {
        $booking = Booking::where('user_id', $user_id)
                        ->where('id', $id)
                        ->firstOrFail();

        if ($booking->status === 'canceled') {
            return response()->json(['message' => 'هذا الحجز ملغي بالفعل.'], 400);
        }

        $booking->update(['status' => 'canceled']);

        return response()->json([
            'message' => 'تم إلغاء الحجز بنجاح',
            'booking_id' => $id,
            'status' => $booking->status
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['message' => 'الحجز غير موجود'], 404);
    } catch (Exception $e) {
        return response()->json(['message' => 'حدث خطأ'], 500);
    }
}
public function updateBooking(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'غير مسجل الدخول'], 401);
    }
    
    $user_id = $user->id;
    
    $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'start_date' => 'sometimes|required|date_format:d-m-Y',
        'end_date' => 'sometimes|required|date_format:d-m-Y|after_or_equal:start_date',
    ]);

    try {
        DB::beginTransaction();
        
        // ✅ الحل: ابحث أولاً بدون lock، ثم lock النتيجة
        $booking = Booking::where('id', $request->booking_id)
                         ->where('user_id', $user_id)
                         ->first();
                         
        if (!$booking) {
            DB::rollBack();
            return response()->json(['message' => 'الحجز غير موجود أو لا يخصك'], 404);
        }

        // ✅ الآن lock الـ booking الموجود
        $booking->lockForUpdate()->get(); // أو Booking::lockForUpdate()->find($booking->id);

        if ($booking->status != 'pending') {
            DB::rollBack();
            return response()->json(['message' => 'لا يمكن تعديل هذا الحجز'], 400);
        }

        // باقي الكود كما هو...
        $start_new = $booking->start_date;
        $end_new = $booking->end_date;
        
        if ($request->has('start_date')) {
            $start_new = Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d');
        }
        if ($request->has('end_date')) {
            $end_new = Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d');
        }

        // التحقق من التداخل
        $conflict = Booking::where('apartment_id', $booking->apartment_id)
            ->where('id', '!=', $booking->id)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where(function ($query) use ($start_new, $end_new) {
                $query->where('start_date', '<', $end_new)
                      ->where('end_date', '>', $start_new);
            })->exists();

        if ($conflict) {
            DB::rollBack();
            return response()->json(['message' => 'هذه الشقة محجوزة بالفعل في التواريخ المختارة.'], 409);
        }

        $days = Carbon::parse($start_new)->diffInDays(Carbon::parse($end_new)) + 1;
        $apartment = Apartment::findOrFail($booking->apartment_id);
        $total_price = $days * $apartment->price_per_month;

        $booking->update([
            'start_date' => $start_new,
            'end_date' => $end_new,
            'total_price' => $total_price,
            'status' => 'pending',
        ]);
    

        DB::commit();

        return response()->json([
            'message' => 'تم تحديث الحجز بنجاح',
            'booking' => $booking->fresh() // لجلب البيانات المحدثة
        ], 200);

    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Booking update failed: ' . $e->getMessage());
        return response()->json(['message' => 'حدث خطأ أثناء التحديث'], 500);
    }
}
public function approveBookingUpdate(Request $request)
    {
        $user = Auth::user();
        $owner_id = $user->id;
        
        if ($user->role != 'owner') {
            return response()->json(['message' => 'غير مصرح لك بالموافقة على هذا التحديث'], 403);
        }   
        
        $request->validate([
            'id' => 'required|exists:bookings,id'
        ]);
        
        $booking = Booking::find($request->id);
        
        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }
        
        if ($booking->status != 'pending') {
            return response()->json(['message' => 'لا يمكن الموافقة على هذا التحديث'], 400);
        }
        
        if ($booking->apartment->owner_id != $owner_id) {
            return response()->json(['message' => 'غير مصرح لك بالموافقة على هذا التحديث'], 403);
        }
        
        $booking->status = 'approved';
        $booking->save();
        
        return response()->json([
            'message' => 'تمت الموافقة على تحديث الحجز بنجاح',
            'booking_status' => $booking->status
        ], 200);
        
    }


    public function userBookings(Request $request)
    {
        $userId = Auth::id();
        $bookings = Booking::with('apartment')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $now = now();
        
        $summary = [
            'active' => $bookings->where('status', 'pending')->where('end_date', '>=', $now->format('Y-m-d')),
            'past' => $bookings->where('end_date', '<', $now->format('Y-m-d')),
            'canceled' => $bookings->where('status', 'canceled'),
        ];

        return response()->json([
            'all_bookings' => $bookings,
            'categorized' => $summary
        ], 200);
    }

    public function OwnerBookings(Request $request)
    {
        $user = Auth::user();
        if($user->role != 'owner'){
            return response()->json(['message' => 'غير مصرح لك بالوصول إلى هذه البيانات'], 403);
        }
        $owner_id = $user->id;
        $apartments = Apartment::where('user_id', $owner_id)->pluck('id');
        
        $bookings = Booking::with('apartment', 'user')
            ->whereIn('apartment_id', $apartments)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'owner_bookings' => $bookings
        ], 200);
    }

    public function approveBooking(Request $request)
    {
        $user = Auth::user();
        $owner_id = $user->id;
        
        if ($user->role != 'owner') {
            return response()->json(['message' => 'غير مصرح لك بالموافقة على هذا الحجز'], 403);
        }   
        
        $request->validate([
            'id' => 'required|exists:bookings,id'
        ]);
        
        $booking = Booking::find($request->id);
        
        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }
        
        if ($booking->status != 'pending') {
            return response()->json(['message' => 'لا يمكن الموافقة على هذا الحجز'], 400);
        }
        
        if ($booking->apartment->owner_id != $owner_id) {
            return response()->json(['message' => 'غير مصرح لك بالموافقة على هذا الحجز'], 403);
        }
        
        $booking->status = 'approved';
        $booking->save();
        
        return response()->json([
            'message' => 'تمت الموافقة على الحجز بنجاح',
            'booking_status' => $booking->status
        ], 200);
    }
}
