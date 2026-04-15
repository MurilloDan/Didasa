<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\AspectoMejora;
use App\Models\Empleado;
use App\Models\Evaluacion;
use App\Models\Taller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluacionAspectosTest extends TestCase
{
    use RefreshDatabase;

    public function test_guarda_aspectos_por_id_y_comentario_otro(): void
    {
        $taller = Taller::create([
            'name' => 'Centro',
            'city' => 'Managua',
            'active' => true,
        ]);

        $area = Area::create([
            'name' => 'Mecánica',
            'description' => 'Área general',
            'active' => true,
        ]);

        $empleado = Empleado::create([
            'workshop_id' => $taller->id,
            'department_id' => $area->id,
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'position' => 'Técnico',
            'active' => true,
        ]);

        $trato = AspectoMejora::create([
            'name' => 'Trato al cliente',
            'icon' => '🤝',
            'is_other' => false,
            'sort_order' => 1,
            'active' => true,
        ]);

        $otro = AspectoMejora::create([
            'name' => 'Otro',
            'icon' => '✏️',
            'is_other' => true,
            'sort_order' => 2,
            'active' => true,
        ]);

        $response = $this->post(route('evaluar.store'), [
            'employee_id' => $empleado->id,
            'rating' => 'poor',
            'aspect_ids' => [$trato->id, $otro->id],
            'other_comment' => 'Demoró mucho la entrega',
        ]);

        $response->assertRedirect();

        $evaluacion = Evaluacion::with('improvementAspects')->first();

        $this->assertNotNull($evaluacion);
        $this->assertSame('poor', $evaluacion->rating);
        $this->assertStringContainsString('Trato al cliente', $evaluacion->comment ?? '');
        $this->assertStringContainsString('Otro: Demoró mucho la entrega', $evaluacion->comment ?? '');
        $this->assertCount(2, $evaluacion->improvementAspects);
        $this->assertDatabaseHas('evaluation_improvement_aspect', [
            'evaluation_id' => $evaluacion->id,
            'improvement_aspect_id' => $otro->id,
            'extra_comment' => 'Demoró mucho la entrega',
        ]);
    }
}
