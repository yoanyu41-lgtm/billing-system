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
        Schema::create('customer_credit_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('checked_by')->constrained('users');
            $table->string('employment_status')->nullable(); // employed, self-employed, unemployed, student
            $table->decimal('monthly_income', 12, 2)->nullable();
            $table->decimal('existing_debt', 12, 2)->default(0);
            $table->integer('credit_score')->default(0); // 0–100
            $table->string('risk_level')->default('medium'); // low, medium, high
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_credit_checks');
    }
};
