<?php

namespace App\Http\Controllers;

use App\Exports\ReporteExport;
use App\Models\Empleado;
use App\Models\Evaluacion;
use App\Models\ReporteQuincenal;
use App\Models\Taller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    /**
     * Dashboard de reportes quincenales.
     */
    public function index(Request $request): Response
    {
        // Taller seleccionado para filtrar
        $workshopId = $request->filled('taller') ? (int) $request->taller : null;

        // Período seleccionado (por defecto: quincena actual)
        $fecha = Carbon::now();
        [$inicio, $fin, $quincena] = ReporteQuincenal::rangoQuincena($fecha);

        if ($request->filled('fecha_inicio')) {
            $inicio   = Carbon::parse($request->fecha_inicio)->startOfDay();
            $fin      = Carbon::parse($request->fecha_fin ?? $request->fecha_inicio)->endOfDay();
            $quincena = null;
        }

        // Generar/actualizar reportes en tiempo real para la quincena actual
        if ($quincena !== null) {
            $qEmp = Empleado::where('active', true);
            if ($workshopId) $qEmp->where('workshop_id', $workshopId);
            $qEmp->each(fn (Empleado $emp) => ReporteQuincenal::generarParaEmpleado($emp, $fecha));
        }

        // IDs de empleados del taller filtrado (para filtrar evaluaciones)
        $empIds = $workshopId
            ? Empleado::where('workshop_id', $workshopId)->pluck('id')
            : null;

        // Estadísticas globales del período
        $globalQ = Evaluacion::whereBetween('created_at', [$inicio, $fin]);
        if ($empIds) $globalQ->whereIn('employee_id', $empIds);
        $global = $globalQ->selectRaw('COUNT(*) as total, SUM(rating = "good") as good, SUM(rating = "fair") as fair, SUM(rating = "poor") as poor')->first();

        // Por empleado
        $empQ = Empleado::with(['area', 'workshop'])
            ->withCount([
                'evaluaciones as total' => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin]),
                'evaluaciones as good'  => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin])->where('rating', 'good'),
                'evaluaciones as fair'  => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin])->where('rating', 'fair'),
                'evaluaciones as poor'  => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin])->where('rating', 'poor'),
            ])
            ->where('active', true);

        if ($workshopId) $empQ->where('workshop_id', $workshopId);

        $empleados = $empQ->get()
            ->map(function ($emp) {
                $total = $emp->total ?: 0;
                return [
                    'id'                 => $emp->id,
                    'full_name'          => $emp->full_name,
                    'position'           => $emp->position,
                    'department'         => $emp->area->name ?? '—',
                    'workshop'           => $emp->workshop->name ?? '—',
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

        // Estadísticas agrupadas por taller (solo cuando no hay filtro activo)
        $talleres_stats = [];
        if (!$workshopId) {
            $talleres_stats = Taller::orderBy('name')->get()->map(function ($taller) use ($inicio, $fin) {
                $ids = Empleado::where('workshop_id', $taller->id)->where('active', true)->pluck('id');
                if ($ids->isEmpty()) {
                    return [
                        'id' => $taller->id, 'name' => $taller->name,
                        'total' => 0, 'good' => 0, 'fair' => 0, 'poor' => 0,
                        'satisfaction_index' => 0,
                    ];
                }
                $row = Evaluacion::whereIn('employee_id', $ids)
                    ->whereBetween('created_at', [$inicio, $fin])
                    ->selectRaw('COUNT(*) as total, SUM(rating="good") as good, SUM(rating="fair") as fair, SUM(rating="poor") as poor')
                    ->first();
                $t = $row->total ?? 0;
                return [
                    'id'                 => $taller->id,
                    'name'               => $taller->name,
                    'total'              => $t,
                    'good'               => $row->good ?? 0,
                    'fair'               => $row->fair ?? 0,
                    'poor'               => $row->poor ?? 0,
                    'satisfaction_index' => $t > 0 ? round((($row->good * 100) + ($row->fair * 50)) / $t, 1) : 0,
                ];
            })->values();
        }

        // Evolución diaria (últimos 30 días)
        $evolQ = Evaluacion::whereBetween('created_at', [
            Carbon::now()->subDays(29)->startOfDay(),
            Carbon::now()->endOfDay(),
        ]);
        if ($empIds) $evolQ->whereIn('employee_id', $empIds);
        $evolucion = $evolQ->selectRaw('DATE(created_at) as fecha, rating, COUNT(*) as cantidad')
            ->groupBy('fecha', 'rating')
            ->orderBy('fecha')
            ->get();

        // Comentarios de evaluaciones "Debe mejorar"
        $comentariosQ = Evaluacion::whereBetween('created_at', [$inicio, $fin])
            ->where('rating', 'poor')
            ->whereNotNull('comment')
            ->where('comment', '!=', '');
        if ($empIds) $comentariosQ->whereIn('employee_id', $empIds);
        $comentariosRaw = $comentariosQ->orderByDesc('created_at')
            ->limit(100)
            ->with('empleado:id,first_name,last_name,position')
            ->get();

        $comentarios = $comentariosRaw->map(fn ($e) => [
            'empleado' => optional($e->empleado)->full_name ?? '—',
            'cargo'    => optional($e->empleado)->position ?? '—',
            'comment'  => $e->comment,
            'fecha'    => $e->created_at->format('d/m/Y H:i'),
        ]);

        // Frecuencia de motivos (conteo de cada aspecto mencionado)
        $motivosFreq = [];
        foreach ($comentariosRaw as $c) {
            foreach (explode(', ', $c->comment ?? '') as $part) {
                $part = trim($part);
                if (!$part) continue;
                $key = str_starts_with($part, 'Otro:') ? 'Otro' : $part;
                $motivosFreq[$key] = ($motivosFreq[$key] ?? 0) + 1;
            }
        }
        arsort($motivosFreq);
        $motivosFreq = array_map(
            fn ($label, $count) => ['label' => $label, 'count' => $count],
            array_keys($motivosFreq),
            $motivosFreq
        );

        // Historial de cortes quincenales (todos los períodos guardados)
        $historicoQ = DB::table('biweekly_reports');
        if ($workshopId) {
            $empIdsHist = Empleado::where('workshop_id', $workshopId)->pluck('id');
            $historicoQ->whereIn('employee_id', $empIdsHist);
        }
        $historico = $historicoQ
            ->selectRaw('year, month, fortnight, start_date, end_date,
                         SUM(total_evaluations) as total,
                         SUM(total_good) as good,
                         SUM(total_fair) as fair,
                         SUM(total_poor) as poor,
                         ROUND(AVG(satisfaction_index), 1) as satisfaction_index')
            ->groupBy('year', 'month', 'fortnight', 'start_date', 'end_date')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('fortnight')
            ->get()
            ->map(fn ($row) => (array) $row);

        return Inertia::render('Reportes/Index', [
            'global'      => $global,
            'empleados'   => $empleados,
            'evolucion'   => $evolucion,
            'periodo'     => [
                'inicio'    => $inicio->toDateString(),
                'fin'       => $fin->toDateString(),
                'fortnight' => $quincena,
            ],
            'talleres'       => Taller::orderBy('name')->get(['id', 'name']),
            'taller_id'      => $workshopId,
            'historico'      => $historico,
            'talleres_stats' => $talleres_stats,
            'comentarios'    => $comentarios,
            'motivos_freq'   => array_values($motivosFreq),
        ]);
    }

    public function exportExcel(Request $request)
    {
        [$empleados, $global, $periodo, $tallerNombre] = $this->buildReportData($request);
        $filename = 'reporte-didasa-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new ReporteExport($empleados->toArray(), $global ? $global->toArray() : [], $periodo, $tallerNombre), $filename);
    }

    public function exportPdf(Request $request)
    {
        [$empleados, $global, $periodo, $tallerNombre] = $this->buildReportData($request);
        $filename = 'reporte-didasa-' . now()->format('Y-m-d') . '.pdf';
        return Pdf::loadView('exports.reporte_pdf', [
            'empleados'    => $empleados,
            'global'       => $global ? $global->toArray() : [],
            'periodo'      => $periodo,
            'tallerNombre' => $tallerNombre,
        ])->setPaper('a4', 'landscape')->download($filename);
    }

    private function buildReportData(Request $request): array
    {
        $workshopId = $request->filled('taller') ? (int) $request->taller : null;

        $fecha = Carbon::now();
        [$inicio, $fin, $quincena] = ReporteQuincenal::rangoQuincena($fecha);

        if ($request->filled('fecha_inicio')) {
            $inicio   = Carbon::parse($request->fecha_inicio)->startOfDay();
            $fin      = Carbon::parse($request->fecha_fin ?? $request->fecha_inicio)->endOfDay();
            $quincena = null;
        }

        $empIds = $workshopId
            ? Empleado::where('workshop_id', $workshopId)->pluck('id')
            : null;

        $globalQ = Evaluacion::whereBetween('created_at', [$inicio, $fin]);
        if ($empIds) $globalQ->whereIn('employee_id', $empIds);
        $global = $globalQ->selectRaw('COUNT(*) as total, SUM(rating = "good") as good, SUM(rating = "fair") as fair, SUM(rating = "poor") as poor')->first();

        $empQ = Empleado::with(['area', 'workshop'])
            ->withCount([
                'evaluaciones as total' => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin]),
                'evaluaciones as good'  => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin])->where('rating', 'good'),
                'evaluaciones as fair'  => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin])->where('rating', 'fair'),
                'evaluaciones as poor'  => fn ($q) => $q->whereBetween('created_at', [$inicio, $fin])->where('rating', 'poor'),
            ])
            ->where('active', true);

        if ($workshopId) $empQ->where('workshop_id', $workshopId);

        $empleados = $empQ->get()
            ->map(function ($emp) use ($inicio, $fin) {
                $total = $emp->total ?: 0;
                return [
                    'id'                 => $emp->id,
                    'full_name'          => $emp->full_name,
                    'position'           => $emp->position,
                    'department'         => $emp->area->name ?? '—',
                    'workshop'           => $emp->workshop->name ?? '—',
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

        $tallerNombre = $workshopId
            ? (Taller::find($workshopId)?->name ?? 'Todos los talleres')
            : 'Todos los talleres';

        $periodo = [
            'inicio'    => $inicio->toDateString(),
            'fin'       => $fin->toDateString(),
            'fortnight' => $quincena,
        ];

        return [$empleados, $global, $periodo, $tallerNombre];
    }
}
