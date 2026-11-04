<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('installment_items')) {
            Schema::create('installment_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('installment_id')->constrained('installments')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->decimal('price', 12, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->timestamps();
            });

            // Backfill existing installments into installment_items
            try {
                $installments = DB::table('installments')->whereNotNull('product_id')->get();
                $now = now();
                $rows = [];
                foreach ($installments as $inst) {
                    $rows[] = [
                        'installment_id' => $inst->id,
                        'product_id'     => $inst->product_id,
                        'quantity'       => 1,
                        'price'          => $inst->subtotal_before_tax ?: $inst->total_price,
                        'tax_rate'       => $inst->tax_rate ?: 0,
                        'tax_amount'     => $inst->tax_amount ?: 0,
                        'total'          => $inst->total_price,
                        'created_at'     => $inst->created_at ?: $now,
                        'updated_at'     => $inst->updated_at ?: $now,
                    ];
                }
                if (!empty($rows)) {
                    foreach (array_chunk($rows, 50) as $chunk) {
                        DB::table('installment_items')->insert($chunk);
                    }
                }
            } catch (\Throwable $e) {
                // Ignore backfill errors
            }
        }

        // Make product_id on installments table nullable if needed
        if (Schema::hasTable('installments') && Schema::hasColumn('installments', 'product_id')) {
            try {
                Schema::table('installments', function (Blueprint $table) {
                    $table->unsignedBigInteger('product_id')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // Ignore change error on some DB engines
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_items');
    }
};

