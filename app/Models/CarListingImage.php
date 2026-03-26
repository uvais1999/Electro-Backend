<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CarListingImage extends Model
{
    protected $fillable = [
        'car_listing_id',
        'image',
        'is_main',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::disk('s3')->temporaryUrl($this->image, now()->addMinutes(10)) : null;
    }

    public function carListing()
    {
        return $this->belongsTo(CarListing::class);
    }
}
