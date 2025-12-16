<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء 10 مستخدمين مالكين (owner)
        User::factory()->count(13)->state([
            'role' => 'owner',
        ])->create();

        // إنشاء 20 مستأجر (renter)
        User::factory()->count(20)->state([
            'role' => 'renter',
        ])->create();
        User::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'phone' => '+963953707821',
            'role' => 'admin',
            'profile_image' => 'profile_images/default.png',
            'birth_date' => '1990-01-01',
            'is_approved' => true,
        ]);
        User::create([
            'first_name' => 'admin2',
            'last_name' => 'admin2',
            'phone' => '+963952659451',
            'role' => 'admin',
            'profile_image' => 'profile_images/default.png',
            'birth_date' => '1990-01-01',
            'is_approved' => true,
        ]);
    }
}
