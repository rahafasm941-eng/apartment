<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apartment;
use storage\app\public\uploads\apartment_images;
use storage\app\public\uploads\details_images;

class ApartmentSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء 20 شقة تجريبية
        Apartment::create([
            'address' => 'luxurious apartment in damascus',
            'type'=>'luxurious',
            'city' => 'Damascus',
            'neighborhood' => 'baramka',
            'description' => 'A beautiful apartment in the heart of the city.',
            'price_per_month' => 500,
            'area' => 120,
            'number_of_rooms' => 3,
            'bathrooms' => 2,
            'is_available' => true,
            'apartment_image' => 'apartment (1).jpg',
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'features' => json_encode(['Balcony', 'Air Conditioning', 'Furnished','wifi']),
            'user_id' => 1,
            'details_image' => json_encode(['detail(1).jpg', 'detail(2).jpg','detail(3).jpg'])
        ]);
        Apartment::create([
            'address' => 'economic apartment',
            'type'=>'economic',
            'city' => 'Homs',
            'neighborhood' => 'the clock of homs',
            'description' => 'A beautiful apartment with an affordable price.',
            'price_per_month' => 200,
            'area' => 80,
            'number_of_rooms' => 2,
            'bathrooms' => 1,
            'is_available' => true,
            'apartment_image' => 'apartment (2).jpg',
            'latitude' => 20.2454,
            'longitude' => 41.2357,
            'features' => json_encode(['pet frindly', 'Air Conditioning','wifi']),
            'user_id' => 2,
            'details_image' => json_encode(['detail (4).jpg', 'detail (5).jpg','detail (6).jpg'])
        ]);
            Apartment::create([
                
            'address' => 'luxurious apartment in damascus',
            'type'=>'luxurious',
            'city' => 'Damascus',
            'neighborhood' => 'al malkee',
            'description' => 'A luxuriuos apartment in a high class neighborhood.',
            'price_per_month' => 600,
            'area' => 200,
            'number_of_rooms' => 5,
            'bathrooms' => 2,
            'is_available' => true,
            'apartment_image' => 'apartment (3).jpg',
            'latitude' => 23.2454,
            'longitude' => 43.2357,
            'features' => json_encode(['pet frindly', 'Air Conditioning', 'parking','wifi']),
            'user_id' => 3,
            'details_image' => json_encode(['detail (7).jpg', 'detail (8).jpg','detail (9).jpg'])


            ]);
            Apartment::create([
                
            'address' => 'luxurious apartment in damascus',
            'type'=>'luxurious',
            'city' => 'Damascus',
            'neighborhood' => 'abo remana',
            'description' => 'A luxuriuos apartment in a high class neighborhood.',
            'price_per_month' => 550,
            'area' => 180,
            'number_of_rooms' => 4,
            'bathrooms' => 2,
            'is_available' => true,
            'apartment_image' => 'apartment (4).jpg',
            'latitude' => 26.2454,
            'longitude' => 46.2357,
            'features' => json_encode([ 'Air Conditioning', 'heating','wifi']),
            'user_id' => 4,
            'details_image' => json_encode(['detail (10).jpg', 'detail (11).jpg','detail (12).jpg'])


            ]);
            Apartment::create([
            'address' => 'economic apartment',          
              'type'=>'economic',

            'city' => 'Aleppo',
            'neighborhood' => 'the citadel of aleppo',
            'description' => 'A beautiful apartment with an affordable price.',
            'price_per_month' => 220,
            'area' => 90,
            'number_of_rooms' => 2,
            'bathrooms' => 1,
            'is_available' => true,
            'apartment_image' => 'apartment (5).jpg',
            'latitude' => 30.2454,
            'longitude' => 49.2357,
            'features' => json_encode([ 'Air Conditioning','wifi']),
            'user_id' => 5,
            'details_image' => json_encode(['detail (13).jpg', 'detail (14).jpg','detail (15).jpg'])
        ]);
        Apartment::create([
            'address' => 'apartment on the coast',
            'type'=>'coastal',
            'city' => 'Latakia',
            'neighborhood' => 'the coast',
            'description' => 'A beautiful apartment on the coast.',
            'price_per_month' => 550,
            'area' => 130,
            'number_of_rooms' => 4,
            'bathrooms' => 2,
            'is_available' => true,
            'apartment_image' => 'apartment (6).jpg',
            'latitude' => 39.2454,
            'longitude' => 44.2357,
            'features' => json_encode([ 'Air Conditioning','wifi','parking','heating']),
            'user_id' => 6,
            'details_image' => json_encode(['detail (16).jpg', 'detail (17).jpg','detail (18).jpg'])
        ]);
        Apartment::create([
            'address' => 'apartment on the coast',
                        'type'=>'coastal',

            'city' => 'Tartus',
            'neighborhood' => 'the coast',
            'description' => 'A beautiful apartment on the coast.',
            'price_per_month' => 530,
            'area' => 120,
            'number_of_rooms' => 4,
            'bathrooms' => 1,
            'is_available' => true,
            'apartment_image' => 'apartment (7).jpg',
            'latitude' => 32.2454,
            'longitude' => 54.2357,
            'features' => json_encode([ 'Air Conditioning','heating']),
            'user_id' => 7,
            'details_image' => json_encode(['detail (19).jpg', 'detail (20).jpg','detail (21).jpg'])
        ]);
        Apartment::create([
            'address' => 'apartment on the coast',
                        'type'=>'coastal',

            'city' => 'Tartus',
            'neighborhood' => 'the coast',
            'description' => 'A beautiful apartment on the coast.',
            'price_per_month' => 600,
            'area' => 200,
            'number_of_rooms' => 5,
            'bathrooms' => 2,
            'is_available' => true,
            'apartment_image' => 'apartment (8).jpg',
            'latitude' => 35.2454,
            'longitude' => 44.2357,
            'features' => json_encode(['parking','pet friendly','wifi', 'Air Conditioning','heating']),
            'user_id' => 8,
            'details_image' => json_encode(['detail (22).jpg', 'detail (23).jpg','detail (24).jpg'])
        ]);
         Apartment::create([
            'address' => 'economic apartment',
                        'type'=>'economic',

            'city' => 'Hama',
            'neighborhood' => 'the watermill',
            'description' => 'A beautiful apartment with an affordable price beside the watermill.',
            'price_per_month' => 180,
            'area' => 90,
            'number_of_rooms' => 3,
            'bathrooms' => 1,
            'is_available' => true,
            'apartment_image' => 'apartment (9).jpg',
            'latitude' => 30.2454,
            'longitude' => 20.2357,
            'features' => json_encode([ 'Air Conditioning','wifi']),
            'user_id' => 9,
            'details_image' => json_encode(['detail (25).jpg', 'detail (26).jpg','detail (27).jpg'])
        ]);
        Apartment::create([
            'address' => 'economic apartment',
                        'type'=>'economic',

            'city' => 'AL-Raqqa',
            'neighborhood' => 'the river',
            'description' => 'A beautiful apartment with an affordable price beside the river.',
            'price_per_month' => 190,
            'area' => 85,
            'number_of_rooms' => 3,
            'bathrooms' => 1,
            'is_available' => true,
            'apartment_image' => 'apartment (10).jpg',
            'latitude' => 34.2454,
            'longitude' => 22.2357,
            'features' => json_encode([ 'Air Conditioning','wifi']),
            'user_id' => 10,
            'details_image' => json_encode(['detail (28).jpg', 'detail (29).jpg','detail (30).jpg'])
        ]);
        Apartment::create([
            'address' => 'apartment in high class neighborhood',
                        'type'=>'high class',

            'city' => 'AL-Hasaka',
            'neighborhood' => 'the elite',
            'description' => 'A luxurious apartment in a high class street.',
            'price_per_month' => 700,
            'area' => 250,
            'number_of_rooms' => 5,
            'bathrooms' => 2,
            'is_available' => true,
            'apartment_image' => 'apartment (11).jpg',
            'latitude' => 40.2454,
            'longitude' => 12.2357,
            'features' => json_encode([ 'heating','pet friendly','parking','Air Conditioning','wifi']),
            'user_id' => 11,
            'details_image' => json_encode(['detail (31).jpg', 'detail (32).jpg','detail (33).jpg'])
        ]);
        Apartment::create([
            'address' => 'apartment in high class neighborhood',
                        'type'=>'high class',

            'city' => 'Dir Al-Zor',
            'neighborhood' => 'the river',
            'description' => 'A luxurious apartment in a high class street beside the river.',
            'price_per_month' => 730,
            'area' => 220,
            'number_of_rooms' => 4,
            'bathrooms' => 2,
            'is_available' => true,
            'apartment_image' => 'apartment (12).jpg',
            'latitude' => 46.2454,
            'longitude' => 14.2357,
            'features' => json_encode([ 'heating','pet friendly','parking','Air Conditioning','wifi']),
            'user_id' => 12,
            'details_image' => json_encode(['detail (34).jpg', 'detail (35).jpg','detail (36).jpg'])
        ]);
        Apartment::create([
            'address' => 'apartment in high class neighborhood',
                        'type'=>'high class',

            'city' => 'Daraa',
            'neighborhood' => 'Roman Theatre at Bosra',
            'description' => 'A luxurious apartment in a high class street beside the theater.',
            'price_per_month' => 500,
            'area' => 190,
            'number_of_rooms' => 4,
            'bathrooms' => 2,
            'is_available' => true,
            'apartment_image' => 'apartment (13).jpg',
            'latitude' => 53.2454,
            'longitude' => 22.2357,
            'features' => json_encode([ 'heating','pet friendly','parking','Air Conditioning','wifi']),
            'user_id' => 13,
            'details_image' => json_encode(['detail (37).jpg', 'detail (38).jpg','detail (39).jpg'])
        ]);
    }
}
