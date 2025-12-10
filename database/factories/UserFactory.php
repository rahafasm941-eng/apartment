<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'phone'      => $this->faker->unique()->numerify('##########'),
            'role'       => $this->faker->randomElement(['owner', 'renter']),
            'profile_image' => 'uploads/profile_images/dummy_profile.png',
            'id_image' => 'uploads/id_images/dummy_id.png',
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'is_approved' => true, // لأن الأدمن يوافق عليهم
        ];
    }
}
