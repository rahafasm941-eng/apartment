<?php

namespace App\Http\Controllers;

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
//1|cduOqBTqXzOwytD6wwmPyThA2hhssrf0rg3z74kdd4cadfa2
    public function approveUser(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);
        $user = User::where('first_name', $validatedData['first_name'])
                    ->where('last_name', $validatedData['last_name'])
                    ->firstOrFail();
                    
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot approve admin account'], 403);
        }

        $user->is_approved = true;
        $user->save();

        return response()->json(['message' => 'User approved successfully']);
    }

    public function rejectUser(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);
        $first_name = $validatedData['first_name'];
        $last_name = $validatedData['last_name'];
    {
        $user = User::where('first_name', $first_name)
                    ->where('last_name', $last_name)
                    ->firstOrFail();

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot reject admin'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User rejected and removed']);
    }
}
}