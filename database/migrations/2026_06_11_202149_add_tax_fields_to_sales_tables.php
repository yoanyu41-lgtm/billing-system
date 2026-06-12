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
        // Add tax fields to sales table
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('discount');
            }
            if (!Schema::hasColumn('sales', 'subtotal_before_tax')) {
                $table->decimal('subtotal_before_tax', 10, 2)->default(0)->after('subtotal');
            }
        });

        // Add tax fields to sale_items table
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('sale_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            }
        });

        // Add tax fields to purchases table
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('total');
            }
        });

        // Add tax fields to purchase_items table
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('purchase_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'subtotal_before_tax']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'tax_amount']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['tax_amount']);
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'tax_amount']);
        });
    }
};
