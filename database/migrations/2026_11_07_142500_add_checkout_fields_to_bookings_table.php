<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('reference', 40)->nullable()->unique();
            $table->string('pickup_location')->nullable();
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->string('return_location')->nullable();
            $table->decimal('return_latitude', 10, 7)->nullable();
            $table->decimal('return_longitude', 10, 7)->nullable();
            $table->text('special_requests')->nullable();
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->timestamp('email_sent_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropUnique(['stripe_checkout_session_id']);
            $table->dropColumn([
                'reference',
                'pickup_location',
                'pickup_latitude',
                'pickup_longitude',
                'return_location',
                'return_latitude',
                'return_longitude',
                'special_requests',
                'stripe_checkout_session_id',
                'email_sent_at',
            ]);
        });
    }
};
