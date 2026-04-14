<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla principal de evaluaciones de atención al cliente.
     *
     * Calificaciones (modelo de 3 caritas):
     *   optimo   😊 — Desempeño óptimo
     *   regular  😐 — Desempeño regular
     *   critico  😞 — Desempeño crítico
     */
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->restrictOnDelete();
            $table->enum('rating', ['good', 'fair', 'poor']);
            $table->text('comment')->nullable();
            $table->string('client_ip', 45)->nullable();
            $table->string('device', 50)->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'created_at']);
            $table->index('rating');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
