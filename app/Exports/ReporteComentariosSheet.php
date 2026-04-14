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

class ReporteComentariosSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(private array $comentarios, private array $motivosFreq) {}

    public function title(): string
    {
        return 'Comentarios';
    }

    public function array(): array
    {
        $totalMotivos = array_sum(array_map(fn ($m) => (int) ($m['count'] ?? 0), $this->motivosFreq));

        $rows = [
            ['TECNICENTRO DIDASA - COMENTARIOS DE MEJORA', '', '', '', ''],
            ['Listado de comentarios de evaluaciones con "Debe mejorar"', '', '', '', ''],
            ['', '', '', '', ''],
            ['ASPECTOS MAS MENCIONADOS', '', '', '', ''],
            ['Aspecto', 'Cantidad', '%', '', ''],
        ];

        if (empty($this->motivosFreq)) {
            $rows[] = ['Sin aspectos registrados', 0, '0%', '', ''];
        } else {
            foreach ($this->motivosFreq as $m) {
                $count = (int) ($m['count'] ?? 0);
                $pct = $totalMotivos > 0 ? round(($count / $totalMotivos) * 100, 1) : 0;
                $rows[] = [
                    $m['label'] ?? '—',
                    $count,
                    $pct . '%',
                    '',
                    '',
                ];
            }
        }

        $rows[] = ['', '', '', '', ''];
        $rows[] = ['COMENTARIOS RECIENTES', '', '', '', ''];
        $rows[] = ['#', 'Empleado', 'Cargo', 'Comentario', 'Fecha'];

        $commentsStart = count($rows) + 1;

        if (empty($this->comentarios)) {
            $rows[] = ['', 'Sin comentarios registrados en este periodo.', '', '', ''];
            return $rows;
        }

        foreach ($this->comentarios as $i => $c) {
            $rows[] = [
                $i + 1,
                $c['empleado'] ?? '—',
                $c['cargo'] ?? '—',
                $c['comment'] ?? '',
                $c['fecha'] ?? '',
            ];
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 26,
            'C' => 20,
            'D' => 52,
            'E' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $motivesRows = max(1, count($this->motivosFreq));
                $motivesStart = 6;
                $motivesEnd = $motivesStart + $motivesRows - 1;
                $commentsTitleRow = $motivesEnd + 2;
                $commentsHeaderRow = $commentsTitleRow + 1;
                $commentsDataStart = $commentsHeaderRow + 1;
                $commentsRows = max(1, count($this->comentarios));
                $lastRow = $commentsDataStart + $commentsRows - 1;

                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A4:E4');
                $sheet->mergeCells("A{$commentsTitleRow}:E{$commentsTitleRow}");

                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(22);
                $sheet->getRowDimension($commentsTitleRow)->setRowHeight(22);

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C80000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'C80000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $sheet->getStyle("A{$commentsTitleRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'C80000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $sheet->getStyle('A5:C5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A1A1A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '333333']]],
                ]);

                $sheet->getStyle("A{$commentsHeaderRow}:E{$commentsHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A1A1A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '333333']]],
                ]);

                for ($row = $motivesStart; $row <= $motivesEnd; $row++) {
                    $bg = ($row % 2 === 0) ? 'FFFFFF' : 'FEF2F2';
                    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getStyle("B{$row}:C{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                for ($row = $commentsDataStart; $row <= $lastRow; $row++) {
                    $bg = ($row % 2 === 0) ? 'FFFFFF' : 'FEF2F2';
                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_TOP],
                    ]);

                    $sheet->getStyle("A{$row}:A{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);

                    $sheet->getStyle("D{$row}")->applyFromArray([
                        'alignment' => ['wrapText' => true],
                    ]);

                    $sheet->getRowDimension($row)->setRowHeight(28);
                }

                $sheet->getStyle("A5:C{$motivesEnd}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'C80000']]],
                ]);

                $sheet->getStyle("A{$commentsHeaderRow}:E{$lastRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'C80000']]],
                ]);

                if (!empty($this->comentarios)) {
                    $sheet->setAutoFilter("A{$commentsHeaderRow}:E{$commentsHeaderRow}");
                }

                $sheet->freezePane("A{$commentsDataStart}");
            },
        ];
    }
}
