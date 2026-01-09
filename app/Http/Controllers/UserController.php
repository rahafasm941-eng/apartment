<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PendingUser;
use App\Models\Profile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function signupPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|digits:9|unique:users,phone|unique:pending_users,phone',
        ]);

        $existingUser = User::where('phone', '+963' . $request->phone)->first();
        
        if ($existingUser) {
            return response()->json(['message' => 'Phone number already registered.'], 400);
        }

        $pendingUser = PendingUser::create([
            'phone' => $request->phone,
        ]);

        $otp = rand(100000, 999999);
        Cache::put('otp_' . $pendingUser->phone, $otp, now()->addMinutes(5));
        
        // Log للتأكد
        Log::info('OTP Generated: ' . $otp . ' for phone: ' . $pendingUser->phone);
        
        $this->sendUltraMsgOtp('+963' . $pendingUser->phone, $otp);

        return response()->json([
            'message' => 'OTP sent via WhatsApp. Please verify.',
            'pending_user_id' => $pendingUser->id,
        ], 201);
    }

    // باقي الدوال زي ما هي... (صحيحة 100%)
    
    private function sendUltraMsgOtp($phone, $otp)
    {
     

$params=array(
'token' => 'c2pfilty64z6ru8s',
'to' => $phone,
'body' => 'Your OTP code is: '.$otp
);
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.ultramsg.com/instance158363/messages/chat",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => http_build_query($params),
  CURLOPT_HTTPHEADER => array(
    "content-type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
    }







// 2. التحقق من OTP
public function verifySignUpOtp(Request $request)
{
    $request->validate([
        'pending_user_id' => 'required|exists:pending_users,id',
        'otp' => 'required|digits:6',
    ]);

    $pendingUser = PendingUser::findOrFail($request->pending_user_id);
    $phoneKey = $pendingUser->phone;
    $cacheKey = 'otp_' . $phoneKey;
    $userOtp = (int)$request->otp;
    
    Log::info("=== VERIFY OTP DEBUG ===");
    Log::info("Phone: {$phoneKey}, User OTP: {$userOtp}, Cached OTP: " . (Cache::get($cacheKey) ?? 'NULL'));

    $cachedOtp = Cache::get($cacheKey);
    if (!$cachedOtp || (int)$cachedOtp !== $userOtp) {
        Log::warning("OTP failed for {$phoneKey}");
        $pendingUser->delete();
        return response()->json(['message' => 'Invalid or expired OTP'], 400);
    }

    // حفظ session أو token مؤقت للـ pending_user للخطوة التالية
    $tempToken = Str::random(40);
    Cache::put('verified_phone_'.$tempToken, $pendingUser->phone, now()->addHours(1));

    Log::info("OTP verified successfully for {$phoneKey}");
    Cache::forget($cacheKey);
    $pendingUser->delete();

    return response()->json([
        'message' => 'Phone verified successfully!',
        'temp_token' => $tempToken, // للاستخدام في الخطوة التالية
    ]);
}

// 3. خطوة ثالثة: إدخال البيانات الشخصية (بعد التحقق من الهاتف)
public function completeProfile(Request $request)
{
    $request->validate([
        'temp_token' => 'required',
        'first_name' => 'required|string|max:50',
        'last_name' => 'required|string|max:50',
        'role' => 'required|in:owner,renter',
        'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'id_image' => 'required|image|mimes:jpg,jpeg,png|max:4096',
        'birth_date' => ['required','date','before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
    ]);

    // التحقق من الـ temp_token
    $verifiedPhone = Cache::get('verified_phone_'.$request->temp_token);
    if (!$verifiedPhone) {
        return response()->json(['message' => 'Phone verification expired or invalid'], 400);
    }

    // حفظ الصور
    $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
    $idImagePath = $request->file('id_image')->store('id_images', 'public');

    DB::beginTransaction();
    try {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => '+963'.$verifiedPhone,
            'role' => $request->role,
            'profile_image' => $profileImagePath,
            'id_image' => $idImagePath,
            'birth_date' => $request->birth_date,
            // 'is_approved' => false, // إذا كنت تستخدم هذا
        ]);
        $profile=Profile::create([
            'user_id'=>$user->id,
            'avatar'=>$user->$profileImagePath,
        ]);

        DB::commit();
        Cache::forget('verified_phone_'.$request->temp_token);

        return response()->json([
            'message' => 'Profile completed successfully! Waiting for admin approval.',
            'user' => $user
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Complete profile failed: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to create user'], 500);
    }
}

    // Login - إرسال OTP
    // public function login(Request $request)
    // {
    //     $request->validate(['phone' => 'required|digits:9']);

    //     $phone = '+963'.$request->phone;
    //     $user = User::where('phone', $phone)->first();

    //     if (!$user) return response()->json(['message'=>'User not found'], 404);
    //     if (!$user->is_approved) return response()->json(['message'=>'Account not approved by admin'], 403);

    //     $otp = rand(100000, 999999);
    //     Cache::put('otp_'.$user->phone, $otp, now()->addMinutes(5));

    //     $this->sendUltraMsgOtp($user->phone, $otp);

    //     return response()->json(['message'=>'OTP sent via WhatsApp']);
    // }

    // Verify OTP Login
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:9',
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
        if($user->role=='renter'){
            $user->fcm_token=$token;
        }
            $user->save();

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'fcm_token'=>$user->fcm_token,
            'user' => $user
        ]);
    }
    public function login(Request $request)
    {
        $request->validate(['phone' => 'required|digits:9']);

        $phone = '+963'.$request->phone;
        $user = User::where('phone', $phone)->first();

        if (!$user) return response()->json(['message'=>'User not found'], 404);
        if (!$user->is_approved) return response()->json(['message'=>'Account not approved by admin'], 403);

        $token = $user->createToken('auth_token')->plainTextToken;
 if($user->role=='renter'){
            $user->fcm_token=$token;
        }
            $user->save();

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
    public function getProfile(Request $request)
    {
        return response()->json($request->user());
    }
}
