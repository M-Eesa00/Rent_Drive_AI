<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['stripe_checkout_session_id']);
            $table->dropColumn([
                'pickup_latitude',
                'pickup_longitude',
                'return_latitude',
                'return_longitude',
                'stripe_checkout_session_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->decimal('return_latitude', 10, 7)->nullable();
            $table->decimal('return_longitude', 10, 7)->nullable();
            $table->string('stripe_checkout_session_id')->nullable()->unique();
        });
    }
};
