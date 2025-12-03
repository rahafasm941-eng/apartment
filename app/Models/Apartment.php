<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $fillable = [
        'nameOfOwner',
        'address',
        'city',
        'numberOfRooms',
        'rentPrice',
        'isAvailable',
        'imageUrl',
        'description',
        'area',
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
