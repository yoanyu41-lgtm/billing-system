<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Allow installments to be marked as completed (e.g. early payoff).
        DB::statement("ALTER TABLE installments MODIFY COLUMN status ENUM('active', 'cancelled', 'completed') NOT NULL DEFAULT 'active'");

        // Flag a payment as a full early settlement.
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('is_settlement')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('is_settlement');
        });

        DB::statement("ALTER TABLE installments MODIFY COLUMN status ENUM('active', 'cancelled') NOT NULL DEFAULT 'active'");
    }
};
