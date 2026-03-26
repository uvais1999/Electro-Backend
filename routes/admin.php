<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DealerController;
use App\Http\Controllers\Admin\PrivateSellerController;
use App\Http\Controllers\Admin\BuyerController;
use App\Http\Controllers\Admin\CarListing\MakeController;
use App\Http\Controllers\Admin\CarListing\ModelController;
use App\Http\Controllers\Admin\CarListing\TrimController;
use App\Http\Controllers\Admin\CarListing\RegionalSpecController;
use App\Http\Controllers\Admin\CarListing\BodyTypeController;
use App\Http\Controllers\Admin\CarListing\EmirateController;
use App\Http\Controllers\Admin\CarListing\ExteriorColorController;
use App\Http\Controllers\Admin\CarListing\InteriorColorController;
use App\Http\Controllers\Admin\CarListing\ChargingTypeController;
use App\Http\Controllers\Admin\CarListing\WarrantyOptionController;
use App\Http\Controllers\Admin\CarListing\DoorTypeController;
use App\Http\Controllers\Admin\CarListing\EngineCylinderController;
use App\Http\Controllers\Admin\CarListing\TransmissionController;
use App\Http\Controllers\Admin\CarListing\SeatingCapacityController;
use App\Http\Controllers\Admin\CarListing\HorsepowerController;
use App\Http\Controllers\Admin\CarListing\SteeringSideController;
use App\Http\Controllers\Admin\CarListing\EngineCapacityController;
use App\Http\Controllers\Admin\CarListing\SafetyFeatureController;
use App\Http\Controllers\Admin\CarListing\TechFeatureController;
use App\Http\Controllers\Admin\CarListing\ComfortFeatureController;
use App\Http\Controllers\Admin\CarListing\ExteriorFeatureController;
use App\Http\Controllers\Admin\CarListingController;
use App\Http\Controllers\Admin\CarListing\UsedCarListingController;
use App\Http\Controllers\Admin\CarListing\NewCarListingController;
use App\Http\Controllers\Admin\UserController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

        // dealers
        Route::resource('dealers', DealerController::class);
        Route::post('/dealers/{user}/approve', [DealerController::class, 'approve'])->name('dealers.approve');
        Route::post('/dealers/{user}/toggle-status', [DealerController::class, 'toggleStatus'])->name('dealers.toggle-status');

        // private sellers
        Route::resource('private-sellers', PrivateSellerController::class);
        Route::post('/private-sellers/{user}/approve', [PrivateSellerController::class, 'approve'])->name('private-sellers.approve');
        Route::post('/private-sellers/{user}/toggle-status', [PrivateSellerController::class, 'toggleStatus'])->name('private-sellers.toggle-status');

        // buyers
        Route::resource('buyers', BuyerController::class);
        Route::post('/buyers/{user}/approve', [BuyerController::class, 'approve'])->name('buyers.approve');
        Route::post('/buyers/{user}/toggle-status', [BuyerController::class, 'toggleStatus'])->name('buyers.toggle-status');

        // car listings
        Route::resource('used-car-listings', UsedCarListingController::class)->names('used-car-listings');
        Route::post('/used-car-listings/{carListing}/verify', [UsedCarListingController::class, 'verify'])->name('used-car-listings.verify');

        Route::resource('new-car-listings', NewCarListingController::class)->names('new-car-listings');
        Route::post('/new-car-listings/{carListing}/verify', [NewCarListingController::class, 'verify'])->name('new-car-listings.verify');

        Route::prefix('car-listings')->name('car-listings.')->group(function () {
            Route::resource('make', MakeController::class);
            Route::post('/make/{make}/toggle-status', [MakeController::class, 'toggleStatus'])->name('make.toggle-status');

            Route::resource('model', ModelController::class);
            Route::post('/model/{model}/toggle-status', [ModelController::class, 'toggleStatus'])->name('model.toggle-status');

            Route::resource('trim', TrimController::class);
            Route::post('/trim/{trim}/toggle-status', [TrimController::class, 'toggleStatus'])->name('trim.toggle-status');
            Route::get('/trim/get-models/{make}', [TrimController::class, 'getModels'])->name('trim.get-models');

            Route::resource('regional-spec', RegionalSpecController::class);
            Route::post('/regional-spec/{regional_spec}/toggle-status', [RegionalSpecController::class, 'toggleStatus'])->name('regional-spec.toggle-status');

            Route::resource('body-type', BodyTypeController::class);
            Route::post('/body-type/{body_type}/toggle-status', [BodyTypeController::class, 'toggleStatus'])->name('body-type.toggle-status');

            Route::resource('emirate', EmirateController::class);
            Route::post('/emirate/{emirate}/toggle-status', [EmirateController::class, 'toggleStatus'])->name('emirate.toggle-status');

            Route::resource('exterior-color', ExteriorColorController::class);
            Route::post('/exterior-color/{exterior_color}/toggle-status', [ExteriorColorController::class, 'toggleStatus'])->name('exterior-color.toggle-status');

            Route::resource('interior-color', InteriorColorController::class);
            Route::post('/interior-color/{interior_color}/toggle-status', [InteriorColorController::class, 'toggleStatus'])->name('interior-color.toggle-status');

            Route::resource('charging-type', ChargingTypeController::class);
            Route::post('/charging-type/{charging_type}/toggle-status', [ChargingTypeController::class, 'toggleStatus'])->name('charging-type.toggle-status');

            Route::resource('warranty-option', WarrantyOptionController::class);
            Route::post('/warranty-option/{warranty_option}/toggle-status', [WarrantyOptionController::class, 'toggleStatus'])->name('warranty-option.toggle-status');

            Route::resource('door-type', DoorTypeController::class);
            Route::post('/door-type/{door_type}/toggle-status', [DoorTypeController::class, 'toggleStatus'])->name('door-type.toggle-status');

            Route::resource('engine-cylinder', EngineCylinderController::class);
            Route::post('/engine-cylinder/{engine_cylinder}/toggle-status', [EngineCylinderController::class, 'toggleStatus'])->name('engine-cylinder.toggle-status');

            Route::resource('transmission', TransmissionController::class);
            Route::post('/transmission/{transmission}/toggle-status', [TransmissionController::class, 'toggleStatus'])->name('transmission.toggle-status');

            Route::resource('seating-capacity', SeatingCapacityController::class);
            Route::post('/seating-capacity/{seating_capacity}/toggle-status', [SeatingCapacityController::class, 'toggleStatus'])->name('seating-capacity.toggle-status');

            Route::resource('horsepower', HorsepowerController::class);
            Route::post('/horsepower/{horsepower}/toggle-status', [HorsepowerController::class, 'toggleStatus'])->name('horsepower.toggle-status');

            Route::resource('steering-side', SteeringSideController::class);
            Route::post('/steering-side/{steering_side}/toggle-status', [SteeringSideController::class, 'toggleStatus'])->name('steering-side.toggle-status');

            Route::resource('engine-capacity', EngineCapacityController::class);
            Route::post('/engine-capacity/{engine_capacity}/toggle-status', [EngineCapacityController::class, 'toggleStatus'])->name('engine-capacity.toggle-status');

            Route::resource('safety-feature', SafetyFeatureController::class);
            Route::post('/safety-feature/{safety_feature}/toggle-status', [SafetyFeatureController::class, 'toggleStatus'])->name('safety-feature.toggle-status');

            Route::resource('tech-feature', TechFeatureController::class);
            Route::post('/tech-feature/{tech_feature}/toggle-status', [TechFeatureController::class, 'toggleStatus'])->name('tech-feature.toggle-status');

            Route::resource('comfort-feature', ComfortFeatureController::class);
            Route::post('/comfort-feature/{comfort_feature}/toggle-status', [ComfortFeatureController::class, 'toggleStatus'])->name('comfort-feature.toggle-status');

            Route::resource('exterior-feature', ExteriorFeatureController::class);
            Route::post('/exterior-feature/{exterior_feature}/toggle-status', [ExteriorFeatureController::class, 'toggleStatus'])->name('exterior-feature.toggle-status');
        });



        // logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
