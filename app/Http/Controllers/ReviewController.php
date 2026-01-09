<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'rating'     => 'required|integer|min:1|max:5',
        'comment'    => 'nullable|string|max:500',
        'apartment_id' => 'required|exists:apartments,id',
    ]);

    try {
        $booking = Booking::findOrFail($request->booking_id);
        $user_id = Auth::id();  
        if ($booking->user_id != $user_id) {
            return response()->json( ['message' => 'لايمكنك التقييم لانك لم تحجز الشقة مسبقا'], 403);
        }
        if($request->apartment_id != $booking->apartment_id){
            return response()->json(['message' => 'لايمكنك التقييم لانك لم تحجز الشقة مسبقا'], 403);
        }
        if($booking->status != 'approved' || $booking->end_date > now()){
            return response()->json(['message' => 'لايمكنك التقييم لان الحجز لم يكتمل بعد'], 400);
        }


        $alreadyReviewed = Review::where('booking_id', $request->booking_id)->exists();
        if ($alreadyReviewed) {
            return response()->json(['message' => 'لقد قمت بتقييم هذا الحجز مسبقاً'], 400);
        }

        
        $review = Review::create([
            'user_id'    => $user_id,
            'booking_id' => $request->booking_id,
            'apartment_id' => $booking->apartment_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);
        $apartment = $booking->apartment;
        $apartment->calculateRating();
        return response()->json([
            'message' => 'تم إضافة التقييم ',
            'review'  => $review
        ], 201);

    } catch (\Exception $e) {
        return response()->json(['message' => 'حدث خطا', 'error' => $e->getMessage()], 500);
    }
}
}
