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
        Schema::create('car_listings', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('emirate_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('car_make_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('car_model_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('car_trim_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('regional_spec_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->integer('year')->nullable();
            $blueprint->integer('kilometers')->nullable();
            $blueprint->foreignId('body_type_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->boolean('is_insured')->default(false);
            $blueprint->decimal('price', 15, 2)->nullable();
            $blueprint->string('phone_number')->nullable();
            $blueprint->string('title')->nullable();
            $blueprint->text('description')->nullable();
            $blueprint->foreignId('exterior_color_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('interior_color_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('warranty_option_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('charging_type_id')->nullable()->constrained('charging_types')->nullOnDelete();
            $blueprint->foreignId('door_type_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('engine_cylinder_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('transmission_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('seating_capacity_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('horsepower_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('steering_side_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->foreignId('engine_capacity_id')->nullable()->constrained()->nullOnDelete();
            $blueprint->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_listings');
    }
};
