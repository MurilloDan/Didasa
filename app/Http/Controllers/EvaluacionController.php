<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Empleado;
use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EvaluacionController extends Controller
{
    /**
     * Muestra la pantalla pública de evaluación (Paso 1 + Paso 2).
     */
    public function index(): Response
    {
        $areas = Area::with(['empleadosActivos' => function ($q) {
            $q->select('id', 'department_id', 'first_name', 'last_name', 'position', 'photo');
        }])
        ->where('active', true)
        ->get(['id', 'name']);

        return Inertia::render('Evaluar', [
            'areas' => $areas,
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
        ]);

        Evaluacion::create([
            'employee_id' => $validated['employee_id'],
            'rating'      => $validated['rating'],
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
