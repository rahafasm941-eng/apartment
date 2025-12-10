<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        $syrianCities = [
            'Damascus', 'Rif Dimashq', 'Aleppo', 'Homs', 'Hama',
            'Latakia', 'Tartus', 'Idlib', 'Raqqa', 'Deir ez-Zor',
            'Al-Hasakah', 'As-Suwayda', 'Daraa', 'Quneitra'
        ];

        return [
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->randomElement($syrianCities),
            'neighborhood' => $this->faker->word(),

            'latitude' => $this->faker->latitude(-90, 90),
            'longitude' => $this->faker->longitude(-180, 180),

            'bathrooms' => $this->faker->numberBetween(1, 4),
            'number_of_rooms' => $this->faker->numberBetween(1, 6),

            'price_per_month' => $this->faker->randomFloat(2, 50, 800),

            'is_available' => $this->faker->boolean(),

            'image_url' => $this->faker->imageUrl(640, 480, 'apartments', true),

            'description' => $this->faker->sentence(12),

            'area' => $this->faker->numberBetween(40, 400),
            'features' => json_encode($this->faker->randomElements(
                ['WiFi', 'Air Conditioning', 'Heating', 'Kitchen', 'Parking', 'Pool', 'Gym', 'Pet Friendly'],
                $this->faker->numberBetween(1, 5)
            )),
             'user_id' => User::where('role', 'owner')->inRandomOrder()->value('id'),


        ];
    }
}
