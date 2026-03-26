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
        Schema::create('car_listing_images', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('car_listing_id')->constrained()->onDelete('cascade');
            $blueprint->string('image');
            $blueprint->boolean('is_main')->default(false);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_listing_images');
    }
};
