<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Empleado;
use App\Models\Evaluacion;
use Illuminate\Database\Seeder;

class EvaluacionSeeder extends Seeder
{
    public function run(): void
    {
        // --- Departments ---
        $departments = [
            ['name' => 'Caja',               'description' => 'Atención en caja y cobros'],
            ['name' => 'Servicio al Cliente', 'description' => 'Mesa de ayuda y consultas'],
            ['name' => 'Ventas',              'description' => 'Asesoría y ventas directas'],
            ['name' => 'Despacho',            'description' => 'Entrega y despacho de pedidos'],
        ];

        foreach ($departments as $data) {
            Area::firstOrCreate(['name' => $data['name']], $data);
        }

        // --- Employees ---
        $employeesData = [
            ['dept' => 'Caja',               'first_name' => 'María',   'last_name' => 'González', 'position' => 'Cajera'],
            ['dept' => 'Caja',               'first_name' => 'Carlos',  'last_name' => 'Ramírez',  'position' => 'Cajero'],
            ['dept' => 'Servicio al Cliente','first_name' => 'Ana',     'last_name' => 'López',    'position' => 'Asesora'],
            ['dept' => 'Servicio al Cliente','first_name' => 'Pedro',   'last_name' => 'Martínez', 'position' => 'Asesor'],
            ['dept' => 'Ventas',             'first_name' => 'Lucía',   'last_name' => 'Herrera',  'position' => 'Vendedora'],
            ['dept' => 'Ventas',             'first_name' => 'Jorge',   'last_name' => 'Castro',   'position' => 'Vendedor'],
            ['dept' => 'Despacho',           'first_name' => 'Sofía',   'last_name' => 'Vargas',   'position' => 'Despachadora'],
        ];

        foreach ($employeesData as $data) {
            $dept = Area::where('name', $data['dept'])->first();
            Empleado::firstOrCreate(
                ['first_name' => $data['first_name'], 'last_name' => $data['last_name']],
                ['department_id' => $dept->id, 'position' => $data['position'], 'active' => true]
            );
        }

        // --- Evaluations (last 30 days) ---
        $employees = Empleado::all();
        $ratings   = Evaluacion::RATINGS;
        $devices   = ['tablet', 'kiosk', 'mobile'];

        $weights = [
            'good' => 60,
            'fair' => 30,
            'poor' => 10,
        ];

        foreach ($employees as $employee) {
            $total = random_int(20, 60);

            for ($i = 0; $i < $total; $i++) {
                $rating   = $this->weightedRating($weights);
                $daysAgo  = random_int(0, 29);

                Evaluacion::create([
                    'employee_id' => $employee->id,
                    'rating'      => $rating,
                    'comment'     => null,
                    'client_ip'   => '192.168.1.' . random_int(1, 254),
                    'device'      => $devices[array_rand($devices)],
                    'created_at'  => now()->subDays($daysAgo)->subMinutes(random_int(0, 1440)),
                    'updated_at'  => now()->subDays($daysAgo),
                ]);
            }
        }
    }

    private function weightedRating(array $weights): string
    {
        $rand = random_int(1, 100);
        $acc  = 0;
        foreach ($weights as $rating => $weight) {
            $acc += $weight;
            if ($rand <= $acc) {
                return $rating;
            }
        }
        return 'good';
    }
}
