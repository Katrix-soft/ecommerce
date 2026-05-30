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
        Schema::create('category_option', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('category_id')
                  ->constrained()
                  ->onDelete('cascade');
                  
            $table->foreignId('option_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('option_subcategory', function (Blueprint $table) {
            $table->id();

            $table->foreignId('option_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('subcategory_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_subcategory');
        Schema::dropIfExists('category_option');
    }
};
