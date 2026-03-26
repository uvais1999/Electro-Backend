<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarTrim extends Model
{
    protected $fillable = [
        'car_model_id',
        'name',
        'status',
    ];

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }
}
