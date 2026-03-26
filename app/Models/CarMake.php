<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CarMake extends Model
{
    protected $fillable = [
        'name',
        'image',
        'status',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::disk('s3')->temporaryUrl($this->image, now()->addMinutes(10)) : null;
    }

    public function models()
    {
        return $this->hasMany(CarModel::class, 'car_make_id');
    }
}
