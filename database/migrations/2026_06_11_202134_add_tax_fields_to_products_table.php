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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_taxable')->default(true)->after('is_active');
            $table->decimal('tax_rate', 5, 2)->default(10.00)->after('is_taxable')->comment('Tax rate in percentage');
            $table->enum('tax_type', ['inclusive', 'exclusive', 'none'])->default('exclusive')->after('tax_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_taxable', 'tax_rate', 'tax_type']);
        });
    }
};
