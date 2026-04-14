<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReporteEmpleadosSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(private array $empleados) {}

    public function title(): string { return 'Por Empleado'; }

    public function array(): array
    {
        $rows = [
            ['#', 'Empleado', 'Cargo', 'Departamento', 'Total', 'Excelente', '% Exc.', 'Regular', '% Reg.', 'Debe Mejorar', '% Mej.', 'Satisfacción %'],
        ];

        foreach ($this->empleados as $i => $emp) {
            $rows[] = [
                $i + 1,
                $emp['full_name'],
                $emp['position'] ?? '—',
                $emp['department'] ?? '—',
                $emp['total'],
                $emp['good'],
                $emp['pct_good'],
                $emp['fair'],
                $emp['pct_fair'],
                $emp['poor'],
                $emp['pct_poor'],
                $emp['satisfaction_index'],
            ];
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  'B' => 26, 'C' => 20, 'D' => 20,
            'E' => 8,  'F' => 12, 'G' => 9,
            'H' => 12, 'I' => 9,
            'J' => 14, 'K' => 9,
            'L' => 16,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = count($this->empleados) + 1; // +1 for header

                // ── Header row ───────────────────────────────────────
                $sheet->getRowDimension(1)->setRowHeight(22);
                $sheet->getStyle('A1:L1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C80000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9B0000']]],
                ]);
                // Left-align text columns
                $sheet->getStyle('B1:D1')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // ── Freeze header row ────────────────────────────────
                $sheet->freezePane('A2');

                // ── Data rows ────────────────────────────────────────
                for ($row = 2; $row <= $lastRow; $row++) {
                    $empIndex = $row - 2;
                    $idx = isset($this->empleados[$empIndex])
                        ? (float) $this->empleados[$empIndex]['satisfaction_index']
                        : 0;

                    $sheet->getRowDimension($row)->setRowHeight(18);

                    // Alternating background
                    $bg = ($row % 2 === 0) ? 'FFFFFF' : 'FEF2F2';
                    $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Center numeric columns
                    $sheet->getStyle("A{$row}:A{$row}")->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                    $sheet->getStyle("E{$row}:L{$row}")->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                    // % columns — format as number with % symbol
                    foreach (['G', 'I', 'K', 'L'] as $col) {
                        $cell = $sheet->getCell("{$col}{$row}");
                        $val  = $cell->getValue();
                        $cell->setValue($val . '%');
                    }

                    // Satisfaction index — conditional color
                    if ($idx >= 75) {
                        $sheet->getStyle("L{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => '16A34A']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
                        ]);
                    } elseif ($idx >= 40) {
                        $sheet->getStyle("L{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => 'D97706']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEFCE8']],
                        ]);
                    } else {
                        $sheet->getStyle("L{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => 'C80000']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF1F2']],
                        ]);
                    }

                    // Excelente cells — light green tint
                    $sheet->getStyle("F{$row}:G{$row}")->applyFromArray([
                        'font' => ['color' => ['rgb' => '16A34A']],
                    ]);
                    // Regular cells — light yellow tint
                    $sheet->getStyle("H{$row}:I{$row}")->applyFromArray([
                        'font' => ['color' => ['rgb' => 'D97706']],
                    ]);
                    // Poor cells — light red tint
                    $sheet->getStyle("J{$row}:K{$row}")->applyFromArray([
                        'font' => ['color' => ['rgb' => 'C80000']],
                    ]);
                }

                // ── Outer border around data table ───────────────────
                if ($lastRow >= 2) {
                    $sheet->getStyle("A1:L{$lastRow}")->applyFromArray([
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'C80000']]],
                    ]);
                }

                // ── Auto-filter ──────────────────────────────────────
                $sheet->setAutoFilter("A1:L1");
            },
        ];
    }
}
