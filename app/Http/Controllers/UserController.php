<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function signup(UserRequest $request)
    {
        $user = User::create([
            'FirstName' => $request->FirstName,
            'LastName' => $request->LastName,
            'dateOfBirth' => $request->dateOfBirth,
            'email' => $request->email,
            'phoneNumber' => $request->phoneNumber,
            'password' => Hash::make($request->password),
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'phoneNumber' => 'required|string|digits:10',
            'password' => 'required|string|min:8|',
        ]);
        if (!Auth::attempt($request->only(['email' ,'phoneNumber', 'password']))){
            return response()->json(['message' => 'Invalid email or password'], 401);
        }
        $user=User::where('phoneNumber', $request->phoneNumber)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,]);
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
    public function store(Request $request)
    {
     $request->validate([
        'first_name'=>'nullable|string|max:255',
        'last_name'=>'nullable|string|max:255',
        'phone'=>'required|string|unique:users,phone',
        'password'=>'nullable|string|min:6',
        'role'='required|in:owner,renter',
        'birth_date'=>'nullable/date',


       
    
     ]) 
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
