<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

use App\Models\UserLoginHistory;
use App\Models\ActivityLog;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    const ROLE_BUYER = 'buyer';
    const ROLE_PRIVATE_SELLER = 'private_seller';
    const ROLE_DEALER = 'dealer';

    const STATUS_INACTIVE = false;
    const STATUS_ACTIVE = true;

    const VERIFICATION_PENDING = 'pending';
    const VERIFICATION_APPROVED = 'approved';
    const VERIFICATION_REJECTED = 'rejected';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'status',
        'verification',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_image_url',
        'role_name',
    ];

    /**
     * Get the user's first role name.
     */
    public function getRoleNameAttribute()
    {
        return $this->roles->first()?->name;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    /**
     * Get the URL for the profile image.
     */
    protected function getProfileImageUrlAttribute()
    {
        return $this->profile_image ? Storage::disk('s3')->temporaryUrl($this->profile_image, now()->addMinutes(10)) : null;
    }

    /**
     * Check if user is a buyer.
     */
    public function isBuyer()
    {
        return $this->hasRole(self::ROLE_BUYER);
    }

    /**
     * Check if user is a private seller.
     */
    public function isPrivateSeller()
    {
        return $this->hasRole(self::ROLE_PRIVATE_SELLER);
    }

    /**
     * Check if user is a dealer.
     */
    public function isDealer()
    {
        return $this->hasRole(self::ROLE_DEALER);
    }

    public function loginHistories()
    {
        return $this->hasMany(UserLoginHistory::class, 'user_id');
    }

    public function latestLogin()
    {
        return $this->hasOne(UserLoginHistory::class, 'user_id')->latestOfMany();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function latestActivity()
    {
        return $this->hasOne(ActivityLog::class)->latestOfMany();
    }
}
