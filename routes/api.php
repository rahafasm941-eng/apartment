<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ______________هدول تبعات الشقةا _______________
Route::middleware('auth:sanctum')->group(function () {
Route::post('apartments', [ApartmentController::class, 'store']);
Route::post('apartmentsUpdate', [ApartmentController::class, 'update']);
Route::delete('apartments', [ApartmentController::class, 'destroy']);
Route::get('OwnerApartments', [ApartmentController::class, 'OwnerApartments']);
Route::get('countOwnerApartments', [ApartmentController::class, 'countOwnerApartments']);
Route::get('countBookedApartments', [ApartmentController::class, 'countBookedApartments']);
Route::get('countPendingApartments', [ApartmentController::class, 'countPendingApartments']);
Route::get('countAvailableApartments', [ApartmentController::class, 'countAvailableApartments']);
});
// ________________________________________________

// __________ هدول تبع الحجز _____________
Route::middleware('auth:sanctum')->group(function () {
Route::post('/bookings', [BookingController::class, 'store']);
Route::post('/bookings/cancel', [BookingController::class, 'cancel']);
Route::get('owner/bookings',[BookingController::class,'OwnerBookings']);
Route::post('approve-booking',[BookingController::class,'approveBooking']);
Route::get('user/booking',[BookingController::class,'userBookings']);
Route::post('update-booking',[BookingController::class,'updateBooking']);
Route::post('bookingApproveUpdate',[BookingController::class,'approveBookingUpdate']);
Route::get('pending-booking',[BookingController::class,'pendingBookings']);
Route::post('reject-booking',[BookingController::class,'rejectBooking']);
Route::post('owner/reject-booking-update',[BookingController::class,'rejectBookingUpdate']);
Route::get('OwnerUpdatedBooking',[BookingController::class,'OwnerUpdatedBooking']);
});
// ________________________________________________


// __________ هدول تبع ال auth  _____________
Route::post('/signup/phone', [UserController::class, 'signupPhone']);           // خطوة 1
Route::post('/verify-phone-otp',[ UserController::class,'verifySignUpOtp']);   // خطوة 2  
Route::post('/complete-profile',[UserController::class, 'completeProfile']);    // خطوة 3
Route::post('/login', [UserController::class, 'login']);
Route::post('/login/verify-otp', [UserController::class, 'verifyLoginOtp']);
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);
// ________________________________________________


// ________________________هدول للأدمن_________________________;
Route::middleware(['auth:sanctum', 'AdminMiddleware'])->group(function () {
Route::get('/admin/pending-users', [AdminController::class, 'pendingUsers']);
Route::get('/admin/approved-users', [AdminController::class, 'approvedUsers']);
Route::post('/admin/approve-user', [AdminController::class, 'approveUser']);
Route::post('/admin/reject-user', [AdminController::class, 'rejectUser']);
Route::get('/admin/all-users', [AdminController::class, 'allUsers']);
Route::get('/admin/all-apartments', [AdminController::class, 'allApartments']);
Route::delete('/admin/delete-apartment', [AdminController::class, 'deleteApartment']);
Route::get('countPendingUsers',[AdminController::class,'countPendingUsers']);
Route::get('countBookedAdminApartments',[AdminController::class,'countBookedApartments']);
Route::get('countBookingUsers',[AdminController::class,'countBookingUsers']);
Route::get('countAllApartments',[AdminController::class,'countAllApartments']);
Route::post('approveApartment',[AdminController::class,'approveApartment']);
Route::get('pendingApartments',[AdminController::class,'pendingApartments']);

});
//_______________________________________________________________

//_____________ هي تبع الفلترة__________________
Route::get('filteringApartments', [ApartmentController::class, 'filteringApartments']);
//_____________________________________________

// __________هي تبع عرض مواصفات الشقة _____________________
Route::get('ApartmentDetails', [ApartmentController::class, 'ApartmentDetails']);
//_____________________________________________

//_________________________هي تبع البحث حسب المدينة_____________________________
Route::get('SearchByCity',[ApartmentController::class,'SearchByCity']);
//_____________________________________________

//_________________________________هي انو يجيب كل الشقق بصفحة الهوم_________________________________
Route::get('allApartments',[ApartmentController::class,'index']);
//__________________________________________________

//___________هي تبع عرض البروفايل ________________
Route::get('user/profile',[UserController::class,'getProfile']);
//_________________________________________

// ___________هي تبع التقييمات ___________________
Route::middleware('auth:sanctum')->group(function () {
    Route::post('reviews', [ReviewController::class, 'store']);
});
// _____________________________________________

        