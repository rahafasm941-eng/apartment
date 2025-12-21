<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;

class ProfileController extends Controller
{
    
    public function show($id)
    {
        $profile = Profile::where('user_id', $id)->firstOrFail();
        $owner = User::find($profile->user_id);
        $profile->avatar=$owner->profile_image;
        return response()->json([
            'profile' => $profile,
            'avatar_path' => $profile->avatar, // Just the path
            'user_name' => $profile->user->first_name . ' ' . $profile->user->last_name,
            'userPhone' => $owner->phone,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Profile $profile)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request, Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
