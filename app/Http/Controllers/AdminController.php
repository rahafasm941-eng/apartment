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
}