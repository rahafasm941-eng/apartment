<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء 10 مستخدمين مالكين (owner)
        User::factory()->count(10)->state([
            'role' => 'owner',
        ])->create();

        // إنشاء 20 مستأجر (renter)
        User::factory()->count(20)->state([
            'role' => 'renter',
        ])->create();
    }
}
