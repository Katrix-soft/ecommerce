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
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('province')->after('user_id');
            $table->string('locality')->after('province');
            $table->string('zip_code')->after('locality');
            $table->string('apartment')->nullable()->after('address'); // Piso/Depto
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['province', 'locality', 'zip_code', 'apartment']);
        });
    }
};
