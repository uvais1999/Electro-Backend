<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model
{
    protected $table = 'user_login_histories';

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'is_successful'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
