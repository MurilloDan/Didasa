<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Evaluacion;
use App\Models\ReporteQuincenal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReporteController extends Controller
{
    /**
     * Dashboard de reportes quincenales.
     */
    public function index(Request $request): Response
    {
        // Período seleccionado (por defecto: quincena actual)
        $fecha = Carbon::now();
        [$inicio, $fin, $quincena] = ReporteQuincenal::rangoQuincena($fecha);

        if ($request->filled('fecha_inicio')) {
            $inicio   = Carbon::parse($request->fecha_inicio)->startOfDay();
            $fin      = Carbon::parse($request->fecha_fin ?? $request->fecha_inicio)->endOfDay();
            $quincena = null; // período personalizado
        }

        // Generar/actualizar reportes en tiempo real para la quincena actual
        if ($quincena !== null) {
            Empleado::where('active', true)->each(function (Empleado $emp) use ($fecha) {
                ReporteQuincenal::generarParaEmpleado($emp, $fecha);
            });
        }

        // Estadísticas globales del período
        $global = Evaluacion::whereBetween('created_at', [$inicio, $fin])
            ->selectRaw('
                COUNT(*) as total,
                SUM(rating = "good") as good,
                SUM(rating = "fair") as fair,
                SUM(rating = "poor") as poor
            ')
            ->first();

        // Por empleado
        $empleados = Empleado::with('area')
            ->withCount([
                'evaluaciones as total'  => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin]),
                'evaluaciones as good'   => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin])->where('rating', 'good'),
                'evaluaciones as fair'   => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin])->where('rating', 'fair'),
                'evaluaciones as poor'   => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin])->where('rating', 'poor'),
            ])
            ->where('active', true)
            ->get()
            ->map(function ($emp) {
                $total = $emp->total ?: 0;
                return [
                    'id'                 => $emp->id,
                    'full_name'          => $emp->full_name,
                    'position'           => $emp->position,
                    'department'         => $emp->area->name ?? '—',
                    'photo'              => $emp->photo,
                    'total'              => $total,
                    'good'               => $emp->good,
                    'fair'               => $emp->fair,
                    'poor'               => $emp->poor,
                    'pct_good'           => $total > 0 ? round($emp->good / $total * 100, 1) : 0,
                    'pct_fair'           => $total > 0 ? round($emp->fair / $total * 100, 1) : 0,
                    'pct_poor'           => $total > 0 ? round($emp->poor / $total * 100, 1) : 0,
                    'satisfaction_index' => $total > 0 ? round(($emp->good * 100 + $emp->fair * 50) / $total, 1) : 0,
                ];
            })
            ->sortByDesc('satisfaction_index')
            ->values();

        // Evolución diaria (últimos 30 días)
        $evolucion = Evaluacion::whereBetween('created_at', [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now()->endOfDay(),
            ])
            ->selectRaw('DATE(created_at) as fecha, rating, COUNT(*) as cantidad')
            ->groupBy('fecha', 'rating')
            ->orderBy('fecha')
            ->get();

        return Inertia::render('Reportes/Index', [
            'global'        => $global,
            'empleados'     => $empleados,
            'evolucion'     => $evolucion,
            'periodo'       => [
                'inicio'     => $inicio->toDateString(),
                'fin'        => $fin->toDateString(),
                'fortnight'  => $quincena,
            ],
        ]);
    }
}
