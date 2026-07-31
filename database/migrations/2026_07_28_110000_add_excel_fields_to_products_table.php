<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('code');
            }
            if (!Schema::hasColumn('products', 'name2')) {
                $table->string('name2')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->nullable()->after('name2');
            }
            if (!Schema::hasColumn('products', 'attributes')) {
                $table->text('attributes')->nullable()->after('unit');
            }
            if (!Schema::hasColumn('products', 'location')) {
                $table->string('location')->nullable()->after('category');
            }
            if (!Schema::hasColumn('products', 'stock_note')) {
                $table->text('stock_note')->nullable();
            }
            if (!Schema::hasColumn('products', 'summary')) {
                $table->text('summary')->nullable();
            }
            if (!Schema::hasColumn('products', 'imei')) {
                $table->string('imei')->nullable();
            }
            if (!Schema::hasColumn('products', 'max_stock_qty')) {
                $table->integer('max_stock_qty')->nullable();
            }
            if (!Schema::hasColumn('products', 'last_stock_in_at')) {
                $table->dateTime('last_stock_in_at')->nullable();
            }
            if (!Schema::hasColumn('products', 'exchange_unit')) {
                $table->string('exchange_unit')->nullable();
            }
            if (!Schema::hasColumn('products', 'seo')) {
                $table->text('seo')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'barcode', 'name2', 'unit', 'attributes', 'location', 
                'stock_note', 'summary', 'imei', 'max_stock_qty', 'last_stock_in_at'
            ]);
        });
    }
};
