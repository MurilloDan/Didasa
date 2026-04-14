<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Empleado;
use App\Models\Evaluacion;
use App\Models\Taller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EvaluacionController extends Controller
{
    /**
     * Muestra la pantalla pública de evaluación (Paso 0: taller, Paso 1: empleado, Paso 2: calificación).
     */
    public function index(): Response
    {
        $talleres = Taller::activos()
            ->with(['empleados' => function ($q) {
                $q->where('active', true)
                  ->select('id', 'workshop_id', 'department_id', 'first_name', 'last_name', 'position', 'photo')
                  ->with(['area:id,name']);
            }])
            ->get(['id', 'name', 'city']);

        return Inertia::render('Evaluar', [
            'talleres' => $talleres,
        ]);
    }

    /**
     * Guarda la evaluación enviada por el cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'rating'      => ['required', 'string', 'in:good,fair,poor'],
            'comment'     => ['nullable', 'string', 'max:500'],
        ]);

        Evaluacion::create([
            'employee_id' => $validated['employee_id'],
            'rating'      => $validated['rating'],
            'comment'     => $validated['comment'] ?? null,
            'client_ip'   => $request->ip(),
            'device'      => $this->detectarDispositivo($request->userAgent() ?? ''),
        ]);

        return back()->with('success', true);
    }

    private function detectarDispositivo(string $ua): string
    {
        $ua = strtolower($ua);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android')) {
            return 'movil';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }
        return 'kiosko';
    }
}
