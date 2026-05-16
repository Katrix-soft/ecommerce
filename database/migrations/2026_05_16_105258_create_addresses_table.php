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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // Hogar, Trabajo, etc.
            $table->string('description')->nullable(); // Ej: Casa de mamá
            $table->string('district'); // Distrito / Barrio
            $table->string('address'); // Dirección (Calle y número)
            $table->string('reference')->nullable(); // Referencias
            $table->string('contact'); // Nombre de quien recibe
            $table->string('phone'); // Teléfono de contacto
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
