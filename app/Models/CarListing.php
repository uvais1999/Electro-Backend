<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarListing extends Model
{
    protected $fillable = [
        'user_id',
        'emirate_id',
        'car_make_id',
        'car_model_id',
        'car_trim_id',
        'regional_spec_id',
        'year',
        'kilometers',
        'body_type_id',
        'is_insured',
        'price',
        'phone_number',
        'title',
        'description',
        'exterior_color_id',
        'interior_color_id',
        'warranty_option_id',
        'charging_type_id',
        'door_type_id',
        'engine_cylinder_id',
        'transmission_id',
        'seating_capacity_id',
        'horsepower_id',
        'steering_side_id',
        'engine_capacity_id',
        'location',
        'building_street',
        'latitude',
        'longitude',
        'status',
        'condition_type',
        'is_verified',
    ];

    protected $appends = [
        'seller_type',
    ];

    /**
     * Accessor for seller type (Private Seller/Dealer)
     */
    public function getSellerTypeAttribute()
    {
        if (!$this->user) return null;
        
        $role = $this->user->roles->first()?->name;
        
        if ($role === User::ROLE_PRIVATE_SELLER) {
            return 'Private Seller';
        }
        
        if ($role === User::ROLE_DEALER) {
            return 'Dealer';
        }
        
        return $role;
    }

    /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->hasMany(CarListingImage::class);
    }

    public function safetyFeatures()
    {
        return $this->belongsToMany(SafetyFeature::class, 'car_listing_safety_feature');
    }

    public function techFeatures()
    {
        return $this->belongsToMany(TechFeature::class, 'car_listing_tech_feature');
    }

    public function comfortFeatures()
    {
        return $this->belongsToMany(ComfortFeature::class, 'car_listing_comfort_feature');
    }

    public function exteriorFeatures()
    {
        return $this->belongsToMany(ExteriorFeature::class, 'car_listing_exterior_feature');
    }

    // Spec Relationships
    public function emirate()
    {
        return $this->belongsTo(Emirate::class);
    }
    public function make()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }
    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }
    public function trim()
    {
        return $this->belongsTo(CarTrim::class, 'car_trim_id');
    }
    public function regionalSpec()
    {
        return $this->belongsTo(RegionalSpec::class);
    }
    public function bodyType()
    {
        return $this->belongsTo(BodyType::class);
    }
    public function exteriorColor()
    {
        return $this->belongsTo(ExteriorColor::class);
    }
    public function interiorColor()
    {
        return $this->belongsTo(InteriorColor::class);
    }
    public function warrantyOption()
    {
        return $this->belongsTo(WarrantyOption::class);
    }
    public function chargingType()
    {
        return $this->belongsTo(ChargingType::class);
    }
    public function doorType()
    {
        return $this->belongsTo(DoorType::class);
    }
    public function engineCylinder()
    {
        return $this->belongsTo(EngineCylinder::class);
    }
    public function transmission()
    {
        return $this->belongsTo(Transmission::class);
    }
    public function seatingCapacity()
    {
        return $this->belongsTo(SeatingCapacity::class);
    }
    public function horsepower()
    {
        return $this->belongsTo(Horsepower::class);
    }
    public function steeringSide()
    {
        return $this->belongsTo(SteeringSide::class);
    }
    public function engineCapacity()
    {
        return $this->belongsTo(EngineCapacity::class);
    }
}
