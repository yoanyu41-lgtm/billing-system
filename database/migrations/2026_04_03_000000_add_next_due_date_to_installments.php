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
        Schema::table('installments', function (Blueprint $table) {
            $table->date('next_due_date')->nullable()->after('remaining_balance');
            $table->dateTime('last_reminder_sent_at')->nullable()->after('next_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn(['next_due_date', 'last_reminder_sent_at']);
        });
    }
};
