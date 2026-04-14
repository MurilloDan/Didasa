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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteResumenSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(
        private array  $global,
        private array  $periodo,
        private string $tallerNombre,
    ) {}

    public function title(): string { return 'Resumen'; }

    public function array(): array
    {
        $good  = $this->global['good']  ?? 0;
        $fair  = $this->global['fair']  ?? 0;
        $poor  = $this->global['poor']  ?? 0;
        $total = $this->global['total'] ?? 0;
        $idx   = $total > 0 ? round(($good * 100 + $fair * 50) / $total, 1) : 0;

        $pctGood = $total > 0 ? round($good / $total * 100, 1) : 0;
        $pctFair = $total > 0 ? round($fair / $total * 100, 1) : 0;
        $pctPoor = $total > 0 ? round($poor / $total * 100, 1) : 0;

        $quincena = $this->periodo['fortnight']
            ? 'Quincena ' . $this->periodo['fortnight']
            : 'Período personalizado';

        return [
            // Row 1 — Brand title
            ['TECNICENTRO DIDASA', '', ''],
            // Row 2 — subtitle
            ['Reporte de Satisfacción al Cliente', '', ''],
            // Row 3 — spacer
            ['', '', ''],
            // Row 4-6 — meta
            ['Taller',       $this->tallerNombre, ''],
            ['Período',      $quincena,            ''],
            ['Desde / Hasta', ($this->periodo['inicio'] ?? '') . '  →  ' . ($this->periodo['fin'] ?? ''), ''],
            // Row 7 — spacer
            ['', '', ''],
            // Row 8 — section header
            ['RESULTADOS GENERALES', '', ''],
            // Row 9 — table header
            ['Indicador', 'Cantidad', 'Porcentaje'],
            // Rows 10-13 — data
            ['Total de evaluaciones', $total,  '100%'],
            ['😊  Excelente',          $good,   $pctGood . '%'],
            ['😐  Regular',            $fair,   $pctFair . '%'],
            ['😞  Debe Mejorar',        $poor,   $pctPoor . '%'],
            // Row 14 — spacer
            ['', '', ''],
            // Row 15 — index
            ['ÍNDICE DE SATISFACCIÓN', $idx . '%', ''],
            // Row 16 — spacer
            ['', '', ''],
            // Row 17 — generation date
            ['Generado el', now()->format('d/m/Y H:i'), ''],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 20, 'C' => 14];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(32);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(8)->setRowHeight(22);
                $sheet->getRowDimension(9)->setRowHeight(20);
                $sheet->getRowDimension(15)->setRowHeight(26);

                // Merge title rows
                $sheet->mergeCells('A1:C1');
                $sheet->mergeCells('A2:C2');
                $sheet->mergeCells('A8:C8');
                $sheet->mergeCells('B15:C15');

                // ── Row 1: Brand header ──────────────────────────────
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C80000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // ── Row 2: subtitle ──────────────────────────────────
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => false, 'size' => 10, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ── Rows 4-6: meta info ──────────────────────────────
                $sheet->getStyle('A4:A6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '444444']],
                ]);
                $sheet->getStyle('B4:B6')->applyFromArray([
                    'font' => ['color' => ['rgb' => '1a1a1a']],
                ]);

                // ── Row 8: section header ────────────────────────────
                $sheet->getStyle('A8')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'C80000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // ── Row 9: table header ──────────────────────────────
                $sheet->getStyle('A9:C9')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a1a1a']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '333333']]],
                ]);
                $sheet->getStyle('A9')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // ── Row 10: total ────────────────────────────────────
                $sheet->getStyle('A10:C10')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                ]);

                // ── Row 11: Excelente — green ────────────────────────
                $sheet->getStyle('A11:C11')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
                    'font' => ['color' => ['rgb' => '16A34A']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BBF7D0']]],
                ]);

                // ── Row 12: Regular — yellow ─────────────────────────
                $sheet->getStyle('A12:C12')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEFCE8']],
                    'font' => ['color' => ['rgb' => 'D97706']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FDE68A']]],
                ]);

                // ── Row 13: Debe Mejorar — red ───────────────────────
                $sheet->getStyle('A13:C13')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF1F2']],
                    'font' => ['color' => ['rgb' => 'C80000']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FECACA']]],
                ]);

                // ── Row 15: Índice ───────────────────────────────────
                $sheet->getStyle('A15:C15')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E09900']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'B07800']]],
                ]);
                $sheet->getStyle('A15')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // ── B col center alignment ───────────────────────────
                $sheet->getStyle('B9:C17')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ── Row 17: generation date ──────────────────────────
                $sheet->getStyle('A17:C17')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '9CA3AF']],
                ]);
            },
        ];
    }
}
