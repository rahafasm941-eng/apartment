<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;
    protected $fillable = [
        'address',
        'city',
        'neighborhood',
        'description',
        'price_per_month',
        'area',
        'number_of_rooms',
        'bathrooms',
        'is_available',
        'image_url',
        'latitude',
        'longitude',
        'features',
        'user_id',
        ];
    public function owner() {
    return $this->belongsTo(User::class, 'user_id');
}

public function bookings() {
    return $this->hasMany(Booking::class);
}

public function reviews() {
    return $this->hasMany(Review::class);
}

}
