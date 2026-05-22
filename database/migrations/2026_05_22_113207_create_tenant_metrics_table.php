<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla para controlar qué métricas del dashboard tiene habilitadas cada tenant/admin.
     */
    public function up(): void
    {
        Schema::create('tenant_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('metric_key'); // Clave de la métrica (ej: 'ingresos_totales')
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'metric_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_metrics');
    }
};
