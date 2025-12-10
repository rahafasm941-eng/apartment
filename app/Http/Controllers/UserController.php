<?php

namespace App\Http\Controllers;

use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Sign Up
    public function signup(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'phone'      => 'required|string|digits:10|unique:pending_users,phone|unique:users,phone',
            'role'       => 'required|in:owner,renter',
            'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'id_image' => 'required|image|mimes:jpg,jpeg,png|max:4096',
            'birth_date' => ['required','date','before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
        ]);

        // حفظ الصور
        $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
        $idImagePath      = $request->file('id_image')->store('id_images', 'public');

        // حفظ المستخدم في pending_users
        $pendingUser = PendingUser::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'phone'      => '+963'.$request->phone,
            'role'       => $request->role,
            'profile_image' => $profileImagePath,
            'id_image' => $idImagePath,
            'birth_date' => $request->birth_date,
        ]);

        // إرسال OTP على WhatsApp
        $otp = rand(100000, 999999);
        Cache::put('otp_'.$pendingUser->phone, $otp, now()->addMinutes(5));

    $this->sendUltraMsgOtp($pendingUser->phone, $otp);
        return response()->json([
            'message' => 'Pending user created. Enter OTP sent via WhatsApp.',
            'pending_user_id' => $pendingUser->id,
        ], 201);
    }

    // إرسال OTP helper
    private function sendWhatsAppOtp($phone, $otp)
    {
        $url = "https://graph.facebook.com/v17.0/".env('WHATSAPP_PHONE_NUMBER_ID')."/messages";

        Http::withToken(env('WHATSAPP_TOKEN'))
            ->post($url, [
                "messaging_product" => "whatsapp",
                "to" => $phone,
                "type" => "text",
                "text" => ["body" => "Your verification code is: $otp"]
            ]);
    }

    // Verify OTP بعد Sign Up
    public function verifySignUpOtp(Request $request)
    {
        
        $request->validate([
            'pending_user_id' => 'required|exists:pending_users,id',
            'otp' => 'required|digits:6',
        ]);

        $pendingUser = PendingUser::findOrFail($request->pending_user_id);

        $cachedOtp = Cache::get('otp_'.$pendingUser->phone);
        if (!$cachedOtp) 
            {$pendingUser->delete();
        return response()->json(['message' => 'OTP expired'], 400);
            }
        if ($cachedOtp != $request->otp) return response()->json(['message' => 'Invalid OTP'], 400);

        Cache::forget('otp_'.$pendingUser->phone);
        $user=User::create([
            'first_name' => $pendingUser->first_name,
            'last_name'  => $pendingUser->last_name,
            'phone'      => $pendingUser->phone,
            'role'       => $pendingUser->role,
            'profile_image' => $pendingUser->profile_image,
            'id_image' => $pendingUser->id_image,
            'birth_date' => $pendingUser->birth_date,
            'is_approved' => false, // بانتظار موافقة الأدمن
        ]);
        $pendingUser->delete();

        return response()->json([
            'message' => 'OTP verified successfully. Waiting for admin approval.',
            'user' => $user
        ]);
    }

    // Login - إرسال OTP
    public function login(Request $request)
    {
        $request->validate(['phone' => 'required|digits:10']);

        $phone = '+963'.$request->phone;
        $user = User::where('phone', $phone)->first();

        if (!$user) return response()->json(['message'=>'User not found'], 404);
        if (!$user->is_approved) return response()->json(['message'=>'Account not approved by admin'], 403);

        $otp = rand(100000, 999999);
        Cache::put('otp_'.$user->phone, $otp, now()->addMinutes(5));

        $this->sendUltraMsgOtp($user->phone, $otp);

        return response()->json(['message'=>'OTP sent via WhatsApp']);
    }

    // Verify OTP Login
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp' => 'required|digits:6',
        ]);

        $phone = '+963'.$request->phone;
        $user = User::where('phone', $phone)->first();

        if (!$user) return response()->json(['message'=>'User not found'], 404);
        if (!$user->is_approved) return response()->json(['message'=>'Account not approved by admin'], 403);

        $cachedOtp = Cache::get('otp_'.$user->phone);
        if (!$cachedOtp) return response()->json(['message'=>'OTP expired'], 400);
        if ($cachedOtp != $request->otp) return response()->json(['message'=>'Invalid OTP'], 400);

        Cache::forget('otp_'.$user->phone);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out']);
    }
    private function sendUltraMsgOtp($phone, $otp)
{
    $params = [
        'token' => env('ULTRAMSG_TOKEN'),
        'to'    => $phone,   // رقم الهاتف يجب أن يكون بصيغة +9639xxxx
        'body'  => "Your verification code is: $otp",
    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.ultramsg.com/instance155393/messages/chat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_HTTPHEADER => ["content-type: application/x-www-form-urlencoded"],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        Log::error("UltraMSG Error: " . $err);
        return false;
    }

    return $response;
}
}
