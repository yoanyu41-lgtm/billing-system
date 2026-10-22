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
        if (Schema::hasTable('installments') && !Schema::hasColumn('installments', 'interest_type')) {
            Schema::table('installments', function (Blueprint $table) {
                $table->string('interest_type', 30)->default('flat')->after('interest_rate');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('installments') && Schema::hasColumn('installments', 'interest_type')) {
            Schema::table('installments', function (Blueprint $table) {
                $table->dropColumn('interest_type');
            });
        }
    }
};
