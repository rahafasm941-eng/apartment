<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingApartment extends Model
{
    //
    protected $casts = [
        'details_image' => 'array',
    ];

    protected $fillable = [
        'address',
        'city',
        'neighborhood',
        'description',
        'price_per_month',
        'area',
        'type',
        'number_of_rooms',
        'bathrooms',
        'is_available',
        'apartment_image',
        'latitude',
        'longitude',
        'apartment_id',
        'features',
        'user_id',
        'details_image'
        ];
    public function owner() {
    return $this->belongsTo(User::class, 'user_id');
}

}
