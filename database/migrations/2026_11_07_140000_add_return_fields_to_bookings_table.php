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
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('return_requested_at')->nullable()->after('payment_status');
            $table->timestamp('returned_at')->nullable()->after('return_requested_at');
            $table->text('return_notes')->nullable()->after('returned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['return_requested_at', 'returned_at', 'return_notes']);
        });
    }
};

