<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_terms', function (Blueprint $table) {
            $table->id();
            $table->string('title_km');           // ឧ. មាត្រា១ - កាតព្វកិច្ចអ្នកទិញ
            $table->string('title_en')->nullable(); // e.g. BUYER'S OBLIGATIONS
            $table->text('content_km');           // ខ្លឹមសារ (មួយជួរ ឬ បន្ទាត់ច្រើន)
            $table->text('content_en')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_terms');
    }
};
