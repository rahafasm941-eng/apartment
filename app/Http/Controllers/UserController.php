<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function signup(Request $request)
 {
    $request->validate([
        'first_name' => 'required|string|max:50',
        'last_name'  => 'required|string|max:50',
        'phone'      => 'required|string|digits:10|unique:users,phone',
        'role'       => 'required|in:owner,renter',
        'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'id_image' => 'required|image|mimes:jpg,jpeg,png|max:4096',

        'birth_date' => [
            'required',
            'date',
            'before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
        ],
    ]);
    $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
    $idImagePath = $request->file('id_image')->store('id_images', 'public');

    $user = User::create([
        'first_name'  => $request->first_name,
        'last_name'   => $request->last_name,
        'phone'       => $request->phone,
        'role'        => $request->role,
        'birth_date'  => $request->birth_date,
        'is_approved' => false,
        'profile_image' => $profileImagePath,
        'id_image' => $idImagePath,
    ]);

    return response()->json([
        'message' => 'Account created. Waiting for admin approval.',
        'user'    => $user,
    ], 201);
}


       public function login(Request $request)
{
    $request->validate([
        'phone' => 'required|string|digits:10'
    ]);

    $user = User::where('phone', $request->phone)->first();

    if (!$user) {
        return response()->json(['message' => 'Invalid phone'], 401);
    }

    // تحقق من موافقة الأدمن
    if (!$user->is_approved) {
        return response()->json(['message' => 'Your account is not approved by admin yet'], 403);
    }

    // أنشئ توكن جديد
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ]);
}


    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
    public function index()
    {
       $users=User::all();
       return response()->json($users);
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
       $user=User::findorFail($id);
       return response()->json($user); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(Request $request, User $user)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
