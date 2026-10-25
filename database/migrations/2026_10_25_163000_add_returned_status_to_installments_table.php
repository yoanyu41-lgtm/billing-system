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
        if (Schema::hasTable('installments')) {
            // Expand status ENUM or change to VARCHAR to safely support 'returned'
            try {
                DB::statement("ALTER TABLE installments MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'active'");
            } catch (\Throwable $e) {
                // Ignore if already string
            }

            Schema::table('installments', function (Blueprint $table) {
                if (!Schema::hasColumn('installments', 'returned_at')) {
                    $table->dateTime('returned_at')->nullable()->after('status');
                }
                if (!Schema::hasColumn('installments', 'returned_by')) {
                    $table->unsignedBigInteger('returned_by')->nullable()->after('returned_at');
                }
                if (!Schema::hasColumn('installments', 'return_reason')) {
                    $table->text('return_reason')->nullable()->after('returned_by');
                }
                if (!Schema::hasColumn('installments', 'return_restock')) {
                    $table->boolean('return_restock')->default(true)->after('return_reason');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('installments')) {
            Schema::table('installments', function (Blueprint $table) {
                $columns = ['returned_at', 'returned_by', 'return_reason', 'return_restock'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('installments', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
