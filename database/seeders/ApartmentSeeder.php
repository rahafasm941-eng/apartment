<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apartment;

class ApartmentSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء 20 شقة تجريبية
        Apartment::factory()->count(20)->create();
    }
}
