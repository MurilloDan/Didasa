<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de reportes quincenales generados automáticamente.
     *
     * Cada registro almacena el resumen consolidado de una quincena
     * (días 1-15 o días 16-fin de mes) por empleado.
     * Se genera mediante un job programado cada 15 días.
     */
    public function up(): void
    {
        Schema::create('biweekly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('fortnight');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');

            $table->unsignedInteger('total_evaluations')->default(0);
            $table->unsignedInteger('total_good')->default(0);
            $table->unsignedInteger('total_fair')->default(0);
            $table->unsignedInteger('total_poor')->default(0);

            $table->decimal('pct_good', 5, 2)->default(0);
            $table->decimal('pct_fair', 5, 2)->default(0);
            $table->decimal('pct_poor', 5, 2)->default(0);

            $table->decimal('satisfaction_index', 5, 2)->default(0);

            $table->timestamp('generated_at')->useCurrent();

            $table->timestamps();

            $table->unique(['employee_id', 'year', 'month', 'fortnight'], 'uq_biweekly_report');
            $table->index(['year', 'month', 'fortnight']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biweekly_reports');
    }
};
