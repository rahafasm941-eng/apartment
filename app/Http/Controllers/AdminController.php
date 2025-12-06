<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function pendingUsers()
    {
        return User::where('is_approved', false)
                   ->where('role', '!=', 'admin')
                   ->get();
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot approve admin account'], 403);
        }

        $user->is_approved = true;
        $user->save();

        return response()->json(['message' => 'User approved successfully']);
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot reject admin'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User rejected and removed']);
    }
}
