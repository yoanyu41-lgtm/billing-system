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
        if (Schema::hasTable('installments')) {
            Schema::table('installments', function (Blueprint $table) {
                if (!Schema::hasColumn('installments', 'payoff_min_month')) {
                    $table->integer('payoff_min_month')->nullable()->after('duration_months');
                }
                if (!Schema::hasColumn('installments', 'payoff_type')) {
                    $table->string('payoff_type', 50)->nullable()->after('payoff_min_month');
                }
                if (!Schema::hasColumn('installments', 'payoff_interest_mode')) {
                    $table->string('payoff_interest_mode', 50)->nullable()->after('payoff_type');
                }
            });

            // Backfill existing records with settings active at this time
            try {
                $currentMinMonth = (int) (DB::table('settings')->where('key', 'payoff_min_month')->value('value') ?? 0);
                $currentType = DB::table('settings')->where('key', 'default_payoff_type')->value('value') ?? 'principal_only';
                $currentMode = DB::table('settings')->where('key', 'payoff_interest_mode')->value('value') ?? 'one_month';

                DB::table('installments')->whereNull('payoff_min_month')->update([
                    'payoff_min_month'     => $currentMinMonth,
                    'payoff_type'          => $currentType,
                    'payoff_interest_mode' => $currentMode,
                ]);
            } catch (\Throwable $e) {
                // Ignore if settings table is not present yet
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('installments')) {
            Schema::table('installments', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('installments', 'payoff_min_month')) {
                    $cols[] = 'payoff_min_month';
                }
                if (Schema::hasColumn('installments', 'payoff_type')) {
                    $cols[] = 'payoff_type';
                }
                if (Schema::hasColumn('installments', 'payoff_interest_mode')) {
                    $cols[] = 'payoff_interest_mode';
                }
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};

