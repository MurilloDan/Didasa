<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteQuincenal extends Model
{
    protected $table = 'biweekly_reports';

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'fortnight',
        'year',
        'month',
        'total_evaluations',
        'total_good',
        'total_fair',
        'total_poor',
        'pct_good',
        'pct_fair',
        'pct_poor',
        'satisfaction_index',
        'generated_at',
    ];

    protected $casts = [
        'start_date'         => 'date',
        'end_date'           => 'date',
        'generated_at'       => 'datetime',
        'pct_good'           => 'float',
        'pct_fair'           => 'float',
        'pct_poor'           => 'float',
        'satisfaction_index' => 'float',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'employee_id');
    }

    public static function generarParaEmpleado(Empleado $empleado, \Carbon\Carbon $fecha): self
    {
        [$inicio, $fin, $fortnight] = self::rangoQuincena($fecha);

        $evaluaciones = Evaluacion::where('employee_id', $empleado->id)
            ->whereBetween('created_at', [$inicio->startOfDay(), $fin->copy()->endOfDay()])
            ->get();

        $total = $evaluaciones->count();
        $good  = $evaluaciones->where('rating', Evaluacion::GOOD)->count();
        $fair  = $evaluaciones->where('rating', Evaluacion::FAIR)->count();
        $poor  = $evaluaciones->where('rating', Evaluacion::POOR)->count();

        $index = $total > 0
            ? round(($good * 100 + $fair * 50) / $total, 2)
            : 0;

        return self::updateOrCreate(
            [
                'employee_id' => $empleado->id,
                'year'        => $inicio->year,
                'month'       => $inicio->month,
                'fortnight'   => $fortnight,
            ],
            [
                'start_date'         => $inicio->toDateString(),
                'end_date'           => $fin->toDateString(),
                'total_evaluations'  => $total,
                'total_good'         => $good,
                'total_fair'         => $fair,
                'total_poor'         => $poor,
                'pct_good'           => $total > 0 ? round($good / $total * 100, 2) : 0,
                'pct_fair'           => $total > 0 ? round($fair / $total * 100, 2) : 0,
                'pct_poor'           => $total > 0 ? round($poor / $total * 100, 2) : 0,
                'satisfaction_index' => $index,
                'generated_at'       => now(),
            ]
        );
    }

    public static function rangoQuincena(\Carbon\Carbon $fecha): array
    {
        $year  = $fecha->year;
        $month = $fecha->month;

        if ($fecha->day <= 15) {
            $inicio    = \Carbon\Carbon::create($year, $month, 1);
            $fin       = \Carbon\Carbon::create($year, $month, 15);
            $fortnight = 1;
        } else {
            $inicio    = \Carbon\Carbon::create($year, $month, 16);
            $fin       = \Carbon\Carbon::create($year, $month)->endOfMonth();
            $fortnight = 2;
        }

        return [$inicio, $fin, $fortnight];
    }
}
