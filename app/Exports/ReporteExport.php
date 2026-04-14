<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReporteExport implements WithMultipleSheets
{
    public function __construct(
        private array  $empleados,
        private array  $global,
        private array  $periodo,
        private string $tallerNombre,
        private array  $comentarios,
        private array  $motivosFreq,
    ) {}

    public function sheets(): array
    {
        return [
            new ReporteResumenSheet($this->global, $this->periodo, $this->tallerNombre),
            new ReporteEmpleadosSheet($this->empleados),
            new ReporteComentariosSheet($this->comentarios, $this->motivosFreq),
        ];
    }
}
