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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('total_price', 10, 2);
            $table->decimal('down_payment', 10, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->integer('duration_months');
            $table->decimal('monthly_payment', 10, 2);
            $table->decimal('remaining_balance', 10, 2);
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->unsignedBigInteger('created_by');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
