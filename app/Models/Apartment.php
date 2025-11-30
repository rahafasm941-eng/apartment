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
}
