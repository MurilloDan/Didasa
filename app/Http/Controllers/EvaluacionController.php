<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AspectoMejora;
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
            'aspectos_mejora' => AspectoMejora::activos()
                ->orderBy('sort_order')
                ->get(['id', 'name', 'icon', 'is_other']),
        ]);
    }

    /**
     * Guarda la evaluación enviada por el cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'   => ['required', 'integer', 'exists:employees,id'],
            'rating'        => ['required', 'string', 'in:good,fair,poor'],
            'comment'       => ['nullable', 'string', 'max:500'],
            'aspect_ids'    => ['nullable', 'array'],
            'aspect_ids.*'  => ['integer', 'exists:improvement_aspects,id'],
            'other_comment' => ['nullable', 'string', 'max:300'],
        ]);

        $aspectIds = array_values(array_unique($validated['aspect_ids'] ?? []));
        $otherComment = trim($validated['other_comment'] ?? '');
        $selectedAspects = collect();
        $comment = $validated['comment'] ?? null;

        if ($validated['rating'] === 'poor' && !empty($aspectIds)) {
            $selectedAspects = AspectoMejora::whereIn('id', $aspectIds)
                ->orderBy('sort_order')
                ->get();

            $comment = $this->buildCommentFromAspects($selectedAspects, $otherComment);
        } elseif ($validated['rating'] === 'poor' && $otherComment !== '') {
            $comment = 'Otro: ' . $otherComment;
        }

        $evaluacion = Evaluacion::create([
            'employee_id' => $validated['employee_id'],
            'rating'      => $validated['rating'],
            'comment'     => $comment,
            'client_ip'   => $request->ip(),
            'device'      => $this->detectarDispositivo($request->userAgent() ?? ''),
        ]);

        if ($selectedAspects->isNotEmpty()) {
            $syncData = [];
            foreach ($selectedAspects as $aspect) {
                $syncData[$aspect->id] = [
                    'extra_comment' => $aspect->is_other && $otherComment !== '' ? $otherComment : null,
                ];
            }
            $evaluacion->improvementAspects()->sync($syncData);
        }

        return back()->with('success', true);
    }

    private function buildCommentFromAspects($selectedAspects, string $otherComment): ?string
    {
        $parts = [];

        foreach ($selectedAspects as $aspect) {
            if ($aspect->is_other) {
                $parts[] = $otherComment !== '' ? 'Otro: ' . $otherComment : 'Otro';
                continue;
            }

            $parts[] = $aspect->name;
        }

        return !empty($parts) ? implode(', ', $parts) : null;
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
