<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarListing\NewCarListingController;
use App\Http\Controllers\Api\CarListing\UsedCarListingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Car Listing Routes
Route::get('/used-car-listings', [UsedCarListingController::class, 'index']);
Route::get('/used-car-listings/filter-data', [UsedCarListingController::class, 'filterData']);

Route::get('/new-car-listings', [NewCarListingController::class, 'index']);
Route::get('/new-car-listings/filter-data', [NewCarListingController::class, 'filterData']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });

    Route::post('/user/update', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Example: Routes for buyers
    Route::middleware('role:buyer')->group(function () {
        // Route::get('/buyer/dashboard', [BuyerController::class, 'index']);
    });

    // Seller Routes (Private Seller & Dealer)
    Route::middleware('role:private_seller|dealer')->group(function () {
        Route::get('/used-car-listings/create-data', [UsedCarListingController::class, 'createData']);
        Route::post('/used-car-listings', [UsedCarListingController::class, 'store']);

        Route::get('/new-car-listings/create-data', [NewCarListingController::class, 'createData']);
        Route::post('/new-car-listings', [NewCarListingController::class, 'store']);
    });

    // Example: Routes ONLY for dealers
    Route::middleware('role:dealer')->group(function () {
        // Route::get('/dealer/reports', [DealerController::class, 'reports']);
    });
});

// Wildcard routes must go after static segments
Route::get('/used-car-listings/{used_car_listing}', [UsedCarListingController::class, 'show']);
Route::get('/new-car-listings/{new_car_listing}', [NewCarListingController::class, 'show']);
