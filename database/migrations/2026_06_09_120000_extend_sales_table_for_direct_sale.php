<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'invoice_no')) {
                $table->string('invoice_no')->nullable()->after('id');
            }
            if (!Schema::hasColumn('sales', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('invoice_no')
                    ->constrained('customers')->nullOnDelete();
            }
            if (!Schema::hasColumn('sales', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('sales', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('sale_date');
            }
            if (!Schema::hasColumn('sales', 'discount')) {
                $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('sales', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('total');
            }
            if (!Schema::hasColumn('sales', 'note')) {
                $table->text('note')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('sales', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('note')
                    ->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            foreach (['customer_id', 'created_by'] as $fk) {
                if (Schema::hasColumn('sales', $fk)) {
                    $table->dropConstrainedForeignId($fk);
                }
            }
            foreach (['invoice_no', 'customer_phone', 'subtotal', 'discount', 'payment_method', 'note'] as $col) {
                if (Schema::hasColumn('sales', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
