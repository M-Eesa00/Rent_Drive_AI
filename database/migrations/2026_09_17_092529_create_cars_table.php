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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->string('plate_number')->unique(); // Added for physical identification
            $table->decimal('totalMileage', 8, 2);
            $table->decimal('daily_rate', 8, 2);
            $table->string('gear_type'); // Automatic / Manual
            $table->string('fuel_type'); // Petrol, Diesel, Electric, Hybrid
            $table->integer('seats')->default(5); // Added for user filtering
            $table->string('image')->nullable(); // Added for car photo
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available'); // Added for availability
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
