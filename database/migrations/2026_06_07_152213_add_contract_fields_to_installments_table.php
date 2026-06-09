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
            $table->string('signed_contract')->nullable()->after('status');
            $table->timestamp('contract_signed_at')->nullable()->after('signed_contract');
            $table->string('contract_signed_by')->nullable()->after('contract_signed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn(['signed_contract', 'contract_signed_at', 'contract_signed_by']);
        });
    }
};
