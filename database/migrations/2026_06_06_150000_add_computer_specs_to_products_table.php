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
            $table->string('cpu')->nullable()->after('model');
            $table->string('ram')->nullable()->after('cpu');
            $table->string('storage')->nullable()->after('ram');
            $table->string('graphics_card')->nullable()->after('storage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cpu', 'ram', 'storage', 'graphics_card']);
        });
    }
};
