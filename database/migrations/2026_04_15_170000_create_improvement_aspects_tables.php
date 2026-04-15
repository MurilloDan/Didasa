<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('improvement_aspects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon', 20)->nullable();
            $table->boolean('is_other')->default(false);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('evaluation_improvement_aspect', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('improvement_aspect_id')->constrained('improvement_aspects')->restrictOnDelete();
            $table->text('extra_comment')->nullable();
            $table->timestamps();

            $table->unique(['evaluation_id', 'improvement_aspect_id'], 'eval_aspect_unique');
        });

        $now = now();
        DB::table('improvement_aspects')->insert([
            ['name' => 'Tiempo de atención', 'icon' => '⏱️', 'is_other' => false, 'active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Trato al cliente', 'icon' => '🤝', 'is_other' => false, 'active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Calidad del trabajo', 'icon' => '🔧', 'is_other' => false, 'active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Explicación del servicio', 'icon' => '💬', 'is_other' => false, 'active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Limpieza del área', 'icon' => '🧹', 'is_other' => false, 'active' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Otro', 'icon' => '✏️', 'is_other' => true, 'active' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $aspectMap = DB::table('improvement_aspects')->pluck('id', 'name');
        $evaluaciones = DB::table('evaluations')
            ->where('rating', 'poor')
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->get(['id', 'comment']);

        foreach ($evaluaciones as $evaluacion) {
            $partes = preg_split('/,\s*/', (string) $evaluacion->comment) ?: [];

            foreach ($partes as $parte) {
                $parte = trim($parte);
                if ($parte === '') {
                    continue;
                }

                $aspectName = $parte;
                $extraComment = null;

                if (str_starts_with($parte, 'Otro:')) {
                    $aspectName = 'Otro';
                    $extraComment = trim(substr($parte, strlen('Otro:')));
                } elseif (!isset($aspectMap[$parte])) {
                    $aspectName = 'Otro';
                    $extraComment = $parte === 'Otro' ? null : $parte;
                }

                if (!isset($aspectMap[$aspectName])) {
                    continue;
                }

                DB::table('evaluation_improvement_aspect')->insertOrIgnore([
                    'evaluation_id' => $evaluacion->id,
                    'improvement_aspect_id' => $aspectMap[$aspectName],
                    'extra_comment' => $extraComment,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_improvement_aspect');
        Schema::dropIfExists('improvement_aspects');
    }
};
