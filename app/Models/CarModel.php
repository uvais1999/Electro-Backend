<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $fillable = [
        'car_make_id',
        'name',
        'status',
    ];

    public function make()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }

    public function trims()
    {
        return $this->hasMany(CarTrim::class, 'car_model_id');
    }
}
