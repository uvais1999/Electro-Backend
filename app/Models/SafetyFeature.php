<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafetyFeature extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];
}
