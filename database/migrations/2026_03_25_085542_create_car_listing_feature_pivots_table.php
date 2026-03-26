<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safety Features Pivot
        Schema::create('car_listing_safety_feature', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('car_listing_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('safety_feature_id')->constrained()->onDelete('cascade');
        });

        // Tech Features Pivot
        Schema::create('car_listing_tech_feature', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('car_listing_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('tech_feature_id')->constrained()->onDelete('cascade');
        });

        // Comfort Features Pivot
        Schema::create('car_listing_comfort_feature', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('car_listing_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('comfort_feature_id')->constrained()->onDelete('cascade');
        });

        // Exterior Features Pivot
        Schema::create('car_listing_exterior_feature', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('car_listing_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('exterior_feature_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_listing_exterior_feature');
        Schema::dropIfExists('car_listing_comfort_feature');
        Schema::dropIfExists('car_listing_tech_feature');
        Schema::dropIfExists('car_listing_safety_feature');
    }
};
