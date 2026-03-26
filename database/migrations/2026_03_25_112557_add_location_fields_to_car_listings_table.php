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
        Schema::table('car_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('car_listings', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('car_listings', 'building_street')) {
                $table->string('building_street')->nullable();
            }
            if (!Schema::hasColumn('car_listings', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('car_listings', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_listings', function (Blueprint $table) {
            $table->dropColumn(['location', 'building_street', 'latitude', 'longitude']);
        });
    }
};
