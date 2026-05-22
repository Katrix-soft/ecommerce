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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->string('store_status', 30)->default('active'); // active, maintenance, suspended
            $table->text('maintenance_message')->nullable();
            $table->text('suspended_message')->nullable();
            $table->string('store_whatsapp')->nullable();
            $table->string('store_instagram')->nullable();
            $table->string('store_email')->nullable();
            $table->string('store_currency', 10)->default('ARS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'store_status',
                'maintenance_message',
                'suspended_message',
                'store_whatsapp',
                'store_instagram',
                'store_email',
                'store_currency',
            ]);
        });
    }
};
