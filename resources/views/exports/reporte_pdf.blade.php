<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9.5px; color: #1a1a1a; background: #fff; }

    /* ── Header ─────────────────────────────────────────── */
    .page-header {
        background: #c80000;
        color: #fff;
        padding: 14px 20px;
        margin-bottom: 0;
        display: table;
        width: 100%;
    }
    .page-header-left  { display: table-cell; vertical-align: middle; }
    .page-header-right { display: table-cell; vertical-align: middle; text-align: right; }
    .page-header h1 { font-size: 18px; font-weight: bold; letter-spacing: .5px; }
    .page-header .sub { font-size: 9px; opacity: .85; margin-top: 2px; }
    .page-header .date { font-size: 9px; opacity: .75; }

    /* ── Meta band ──────────────────────────────────────── */
    .meta-band {
        background: #1a1a1a;
        color: #fff;
        padding: 8px 20px;
        margin-bottom: 14px;
        display: table;
        width: 100%;
    }
    .meta-item { display: table-cell; padding-right: 30px; }
    .meta-item .meta-label { font-size: 7.5px; color: #aaa; text-transform: uppercase; letter-spacing: .5px; }
    .meta-item .meta-value { font-size: 10px; font-weight: bold; color: #fff; margin-top: 1px; }

    /* ── Section title ──────────────────────────────────── */
    .section-title {
        font-size: 9.5px; font-weight: bold; text-transform: uppercase;
        color: #c80000; letter-spacing: .6px;
        border-bottom: 2px solid #c80000; padding-bottom: 3px;
        margin-bottom: 8px; margin-top: 14px;
    }

    /* ── KPI row ────────────────────────────────────────── */
    .kpi-row { display: table; width: 100%; border-collapse: separate; border-spacing: 4px; margin-bottom: 14px; }
    .kpi-cell {
        display: table-cell; text-align: center;
        padding: 10px 6px; border-radius: 6px;
        border: 1px solid #e5e7eb;
    }
    .kpi-cell .kpi-num  { font-size: 24px; font-weight: bold; line-height: 1; }
    .kpi-cell .kpi-pct  { font-size: 9px; font-weight: bold; margin-top: 1px; }
    .kpi-cell .kpi-lbl  { font-size: 8px; color: #6b7280; margin-top: 4px; }
    .kpi-total { background: #f9fafb; }
    .kpi-total .kpi-num { color: #1a1a1a; }
    .kpi-good  { background: #f0fdf4; border-color: #bbf7d0; }
    .kpi-good  .kpi-num, .kpi-good .kpi-pct { color: #16a34a; }
    .kpi-fair  { background: #fefce8; border-color: #fde68a; }
    .kpi-fair  .kpi-num, .kpi-fair .kpi-pct { color: #d97706; }
    .kpi-poor  { background: #fef2f2; border-color: #fecaca; }
    .kpi-poor  .kpi-num, .kpi-poor .kpi-pct { color: #c80000; }

    /* ── Satisfaction index ─────────────────────────────── */
    .idx-bar-wrap {
        background: #1a1a1a; color: #fff;
        padding: 10px 20px; border-radius: 6px;
        display: table; width: 100%; margin-bottom: 14px;
    }
    .idx-bar-left  { display: table-cell; vertical-align: middle; }
    .idx-bar-right { display: table-cell; vertical-align: middle; text-align: right; }
    .idx-label { font-size: 9px; text-transform: uppercase; letter-spacing: .6px; color: #aaa; }
    .idx-value { font-size: 26px; font-weight: bold; color: #E09900; }
    .idx-track { background: #444; border-radius: 4px; height: 10px; width: 200px; display: inline-block; overflow: hidden; vertical-align: middle; margin-left: 10px; }
    .idx-fill  { height: 10px; border-radius: 4px; background: #E09900; display: inline-block; }

    /* ── Employee table ─────────────────────────────────── */
    table.data { width: 100%; border-collapse: collapse; font-size: 8.5px; }
    table.data thead tr { background: #c80000; color: #fff; }
    table.data thead th { padding: 5px 5px; font-weight: bold; text-align: center; border: 1px solid #9e0000; }
    table.data thead th.left { text-align: left; }
    table.data tbody tr:nth-child(even) td { background: #fef2f2; }
    table.data tbody tr:nth-child(odd)  td { background: #fff; }
    table.data tbody td { padding: 4px 5px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    table.data tbody td.center { text-align: center; }
    table.data tbody td.right  { text-align: right; }
    table.data tbody td.emp-name { font-weight: bold; font-size: 9px; }
    table.data tbody td.emp-pos  { font-size: 8px; color: #6b7280; }

    /* satisfaction chips */
    .chip { display: inline-block; padding: 1px 5px; border-radius: 10px; font-size: 7.5px; font-weight: bold; }
    .chip-green  { background: #dcfce7; color: #16a34a; }
    .chip-yellow { background: #fef9c3; color: #b45309; }
    .chip-red    { background: #fee2e2; color: #c80000; }

    /* mini bar */
    .mini-track { background: #e5e7eb; border-radius: 2px; height: 5px; width: 50px; display: inline-block; vertical-align: middle; overflow: hidden; }
    .mini-fill  { height: 5px; border-radius: 2px; display: inline-block; }
    .bar-green  { background: #4ade80; }
    .bar-yellow { background: #fbbf24; }
    .bar-red    { background: #ef4444; }

    .footer { text-align: right; font-size: 7.5px; color: #9ca3af; margin-top: 12px; border-top: 1px solid #e5e7eb; padding-top: 4px; }
</style>
</head>
<body>

@php
    $total   = $global['total'] ?? 0;
    $good    = $global['good']  ?? 0;
    $fair    = $global['fair']  ?? 0;
    $poor    = $global['poor']  ?? 0;
    $pctGood = $total > 0 ? round($good / $total * 100, 1) : 0;
    $pctFair = $total > 0 ? round($fair / $total * 100, 1) : 0;
    $pctPoor = $total > 0 ? round($poor / $total * 100, 1) : 0;
    $idx     = $total > 0 ? round(($good * 100 + $fair * 50) / $total, 1) : 0;
    $periodoLabel = $periodo['fortnight'] ? 'Quincena ' . $periodo['fortnight'] : 'Período personalizado';
@endphp

{{-- Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h1>TECNICENTRO DIDASA</h1>
        <div class="sub">Reporte de Satisfacción al Cliente</div>
    </div>
    <div class="page-header-right">
        <div class="date">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

{{-- Meta band --}}
<div class="meta-band">
    <div class="meta-item">
        <div class="meta-label">Taller</div>
        <div class="meta-value">{{ $tallerNombre }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Período</div>
        <div class="meta-value">{{ $periodoLabel }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Desde</div>
        <div class="meta-value">{{ $periodo['inicio'] }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Hasta</div>
        <div class="meta-value">{{ $periodo['fin'] }}</div>
    </div>
</div>

{{-- KPI Cards --}}
<div class="section-title">Resumen General</div>
<div class="kpi-row">
    <div class="kpi-cell kpi-total">
        <div class="kpi-num">{{ $total }}</div>
        <div class="kpi-lbl">Total evaluaciones</div>
    </div>
    <div class="kpi-cell kpi-good">
        <div class="kpi-num">{{ $good }}</div>
        <div class="kpi-pct">{{ $pctGood }}%</div>
        <div class="kpi-lbl">Excelente</div>
    </div>
    <div class="kpi-cell kpi-fair">
        <div class="kpi-num">{{ $fair }}</div>
        <div class="kpi-pct">{{ $pctFair }}%</div>
        <div class="kpi-lbl">Regular</div>
    </div>
    <div class="kpi-cell kpi-poor">
        <div class="kpi-num">{{ $poor }}</div>
        <div class="kpi-pct">{{ $pctPoor }}%</div>
        <div class="kpi-lbl">Debe Mejorar</div>
    </div>
</div>

{{-- Satisfaction Index --}}
<div class="idx-bar-wrap">
    <div class="idx-bar-left">
        <div class="idx-label">Índice de Satisfacción del Período</div>
    </div>
    <div class="idx-bar-right">
        <span class="idx-value">{{ $idx }}%</span>
        <span class="idx-track"><span class="idx-fill" style="width:{{ $idx }}%;"></span></span>
    </div>
</div>

{{-- Employee Table --}}
<div class="section-title">Detalle por Empleado</div>
<table class="data">
    <thead>
        <tr>
            <th style="width:18px;">#</th>
            <th class="left" style="width:110px;">Empleado</th>
            <th class="left" style="width:70px;">Cargo</th>
            <th class="left" style="width:70px;">Departamento</th>
            <th style="width:28px;">Total</th>
            <th style="width:28px;">Exc.</th>
            <th style="width:28px;">% Exc.</th>
            <th style="width:28px;">Reg.</th>
            <th style="width:28px;">% Reg.</th>
            <th style="width:28px;">Mej.</th>
            <th style="width:28px;">% Mej.</th>
            <th style="width:65px;">Satisfacción</th>
        </tr>
    </thead>
    <tbody>
        @forelse($empleados as $i => $emp)
        @php
            $si = $emp['satisfaction_index'];
            $chipClass = $si >= 75 ? 'chip-green' : ($si >= 40 ? 'chip-yellow' : 'chip-red');
            $barClass  = $si >= 75 ? 'bar-green'  : ($si >= 40 ? 'bar-yellow'  : 'bar-red');
        @endphp
        <tr>
            <td class="center" style="color:#9ca3af;font-weight:bold;">{{ $i + 1 }}</td>
            <td>
                <div class="emp-name">{{ $emp['full_name'] }}</div>
            </td>
            <td style="color:#374151;">{{ $emp['position'] ?? '—' }}</td>
            <td style="color:#374151;">{{ $emp['department'] ?? '—' }}</td>
            <td class="center" style="font-weight:bold;">{{ $emp['total'] }}</td>
            <td class="center" style="color:#16a34a;font-weight:bold;">{{ $emp['good'] }}</td>
            <td class="center" style="color:#16a34a;">{{ $emp['pct_good'] }}%</td>
            <td class="center" style="color:#d97706;font-weight:bold;">{{ $emp['fair'] }}</td>
            <td class="center" style="color:#d97706;">{{ $emp['pct_fair'] }}%</td>
            <td class="center" style="color:#c80000;font-weight:bold;">{{ $emp['poor'] }}</td>
            <td class="center" style="color:#c80000;">{{ $emp['pct_poor'] }}%</td>
            <td class="center">
                <span class="chip {{ $chipClass }}">{{ $si }}%</span><br>
                <span class="mini-track"><span class="mini-fill {{ $barClass }}" style="width:{{ min($si,100) }}%;"></span></span>
            </td>
        </tr>
        @empty
        <tr><td colspan="12" class="center" style="color:#9ca3af;padding:14px;">Sin evaluaciones en este período</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">TECNICENTRO DIDASA &mdash; Documento generado automáticamente &mdash; {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>


    .header { background: #c80000; color: #fff; padding: 12px 16px; margin-bottom: 14px; }
    .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 2px; }
    .header p { font-size: 10px; opacity: .85; }

    .meta-grid { display: table; width: 100%; margin-bottom: 14px; }
    .meta-cell { display: table-cell; width: 50%; vertical-align: top; }
    .meta-cell table { width: 100%; border-collapse: collapse; }
    .meta-cell td { padding: 4px 6px; font-size: 10px; border-bottom: 1px solid #e5e7eb; }
    .meta-cell td:first-child { font-weight: bold; color: #6b7280; width: 120px; }

    h2 { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #c80000;
         letter-spacing: .5px; margin-bottom: 6px; border-bottom: 2px solid #c80000; padding-bottom: 2px; }

    .global-grid { display: table; width: 100%; margin-bottom: 16px; border-collapse: collapse; }
    .kpi { display: table-cell; text-align: center; padding: 10px; border: 1px solid #e5e7eb;
           background: #f9fafb; }
    .kpi .num { font-size: 22px; font-weight: bold; }
    .kpi .label { font-size: 9px; color: #6b7280; margin-top: 3px; }
    .kpi.good .num { color: #16a34a; }
    .kpi.fair .num { color: #d97706; }
    .kpi.poor .num { color: #c80000; }
    .kpi.idx  .num { color: #1d4ed8; }

    table.data { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    table.data th { background: #c80000; color: #fff; padding: 5px 7px; font-size: 9px;
                    text-align: center; font-weight: bold; }
    table.data td { padding: 4px 7px; font-size: 9px; border-bottom: 1px solid #e5e7eb; }
    table.data tr:nth-child(even) td { background: #fef2f2; }
    table.data td.right { text-align: right; }
    table.data td.center { text-align: center; }

    .bar-wrap { background: #e5e7eb; border-radius: 3px; height: 8px; width: 60px; display: inline-block; vertical-align: middle; }
    .bar-fill  { height: 8px; border-radius: 3px; display: inline-block; }

    .footer { text-align: right; font-size: 8px; color: #9ca3af; margin-top: 10px; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>TECNICENTRO DIDASA &mdash; Reporte de Satisfacción</h1>
    <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
</div>

{{-- Meta info --}}
<div class="meta-grid">
    <div class="meta-cell">
        <table>
            <tr><td>Taller</td><td>{{ $tallerNombre }}</td></tr>
            <tr><td>Quincena</td><td>{{ $periodo['fortnight'] ? 'Quincena '.$periodo['fortnight'] : 'Período personalizado' }}</td></tr>
        </table>
    </div>
    <div class="meta-cell" style="padding-left:16px;">
        <table>
            <tr><td>Desde</td><td>{{ $periodo['inicio'] }}</td></tr>
            <tr><td>Hasta</td><td>{{ $periodo['fin'] }}</td></tr>
        </table>
    </div>
</div>

{{-- Global KPIs --}}
<h2>Resumen General</h2>
@php
    $total   = $global->total ?? 0;
    $good    = $global->good  ?? 0;
    $fair    = $global->fair  ?? 0;
    $poor    = $global->poor  ?? 0;
    $pctGood = $total > 0 ? round($good / $total * 100, 1) : 0;
    $pctFair = $total > 0 ? round($fair / $total * 100, 1) : 0;
    $pctPoor = $total > 0 ? round($poor / $total * 100, 1) : 0;
    $idx     = $total > 0 ? round(($good * 100 + $fair * 50) / $total, 1) : 0;
@endphp
<table class="global-grid" style="margin-bottom:16px;">
    <tr>
        <td class="kpi">
            <div class="num">{{ $total }}</div>
            <div class="label">Total evaluaciones</div>
        </td>
        <td class="kpi good">
            <div class="num">{{ $good }}</div>
            <div class="label">😊 Excelente ({{ $pctGood }}%)</div>
        </td>
        <td class="kpi fair">
            <div class="num">{{ $fair }}</div>
            <div class="label">😐 Regular ({{ $pctFair }}%)</div>
        </td>
        <td class="kpi poor">
            <div class="num">{{ $poor }}</div>
            <div class="label">😞 Debe Mejorar ({{ $pctPoor }}%)</div>
        </td>
        <td class="kpi idx">
            <div class="num">{{ $idx }}%</div>
            <div class="label">Índice de Satisfacción</div>
        </td>
    </tr>
</table>

{{-- Per-employee table --}}
<h2>Detalle por Empleado</h2>
<table class="data">
    <thead>
        <tr>
            <th>#</th>
            <th style="text-align:left;">Empleado</th>
            <th style="text-align:left;">Cargo</th>
            <th style="text-align:left;">Departamento</th>
            <th>Total</th>
            <th>😊 Exc.</th>
            <th>%</th>
            <th>😐 Reg.</th>
            <th>%</th>
            <th>😞 Mej.</th>
            <th>%</th>
            <th>Satisfacción</th>
        </tr>
    </thead>
    <tbody>
        @forelse($empleados as $i => $emp)
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $emp['full_name'] }}</td>
            <td>{{ $emp['position'] ?? '—' }}</td>
            <td>{{ $emp['department'] ?? '—' }}</td>
            <td class="center">{{ $emp['total'] }}</td>
            <td class="center">{{ $emp['good'] }}</td>
            <td class="center">{{ $emp['pct_good'] }}%</td>
            <td class="center">{{ $emp['fair'] }}</td>
            <td class="center">{{ $emp['pct_fair'] }}%</td>
            <td class="center">{{ $emp['poor'] }}</td>
            <td class="center">{{ $emp['pct_poor'] }}%</td>
            <td class="center" style="font-weight:bold;color:#1d4ed8;">{{ $emp['satisfaction_index'] }}%</td>
        </tr>
        @empty
        <tr><td colspan="12" class="center" style="color:#9ca3af;padding:12px;">Sin evaluaciones en este período</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">TECNICENTRO DIDASA &mdash; Documento generado automáticamente</div>
</body>
</html>
