<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Empleado;
use App\Models\Taller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        return Inertia::render('Employees/Index', [
            'employees'   => Empleado::with(['area', 'workshop'])->orderBy('first_name')->get(),
            'departments' => Area::orderBy('name')->get(['id', 'name']),
            'workshops'   => Taller::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'position'      => 'nullable|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'workshop_id'   => 'nullable|exists:workshops,id',
        ]);

        Empleado::create(array_merge($data, ['active' => true]));

        return redirect()->route('employees')->with('success', 'Employee created.');
    }

    public function update(Request $request, Empleado $employee)
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'position'      => 'nullable|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'workshop_id'   => 'nullable|exists:workshops,id',
            'active'        => 'boolean',
        ]);

        $employee->update($data);

        return redirect()->route('employees')->with('success', 'Employee updated.');
    }

    public function destroy(Empleado $employee)
    {
        $employee->delete();

        return redirect()->route('employees')->with('success', 'Employee deleted.');
    }
}
