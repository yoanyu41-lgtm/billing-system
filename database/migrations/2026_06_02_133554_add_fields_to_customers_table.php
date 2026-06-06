<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('phone');
            $table->date('dob')->nullable()->after('gender');
            $table->string('id_card')->nullable()->after('dob');
            $table->string('photo')->nullable()->after('id_card');
            $table->string('id_card_photo')->nullable()->after('photo');
            $table->string('family_photo')->nullable()->after('id_card_photo');
            $table->string('income_proof')->nullable()->after('family_photo');
            $table->string('guarantor_doc')->nullable()->after('income_proof');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'gender', 'dob', 'id_card', 'photo',
                'id_card_photo', 'family_photo', 'income_proof', 'guarantor_doc',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
