<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// _____ هدول تبعات الشقة مو جاهزين لسا ___________
Route::post('apartments', [ApartmentController::class, 'store'])
    ->middleware('auth:sanctum');
Route::put('apartments/{apartment}', [ApartmentController::class, 'edit'])
    ->middleware('auth:sanctum');
Route::delete('apartments/{apartment}', [ApartmentController::class, 'destroy'])
    ->middleware('auth:sanctum');
Route::get('apartments', [ApartmentController::class, 'index']);
Route::get('apartments/{apartment}', [ApartmentController::class, 'show']);
// ________________________________________________


// __________ هدول تبع ال auth لكن بدون otp _____________
Route::post('/signup', [UserController::class, 'signup']);
Route::post('/signup/verify-otp', [UserController::class, 'verifySignUpOtp']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/login/verify-otp', [UserController::class, 'verifyLoginOtp']);
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);
// _________________________________________________;
    Route::middleware(['auth:sanctum', 'AdminMiddleware'])->group(function () {
    Route::get('/admin/pending-users', [AdminController::class, 'pendingUsers']);
    Route::post('/admin/approve-user/{id}', [AdminController::class, 'approveUser']);
    Route::post('/admin/reject-user/{id}', [AdminController::class, 'rejectUser']);
});
//_______________________________________________________________

//_____________ هي تبع الفلترة__________________
Route::get('filteringApartments', [ApartmentController::class, 'filteringApartments']);
//_____________________________________________

// __________هي تبع عرض مواصفات الشقة _____________________
Route::get('ApartmentDetails', [ApartmentController::class, 'ApartmentDetails']);
//__________________________________________________


        