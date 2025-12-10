<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $firstNames = ['أحمد', 'محمد', 'علي', 'يوسف', 'سارة', 'ليلى', 'مريم', 'هند', 'فاطمة', 'نور'];
        $lastNames  = ['النجار', 'الخطيب', 'الحسني', 'العلي', 'الحموي', 'السالم', 'المصري', 'اليوسف'];

        // اختيار عشوائي
        $first_name = $this->faker->randomElement($firstNames);
        $last_name  = $this->faker->randomElement($lastNames);

        return [
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'phone'      => '+963 9' . $this->faker->unique()->numerify('########'),
            'role'       => $this->faker->randomElement(['owner', 'renter']),
            'profile_image' => 'uploads/profile_images/dummy_profile.png',
            'id_image' => 'uploads/id_images/dummy_id.png',
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'is_approved' => true, // لأن الأدمن يوافق عليهم
        ];
    }
}
