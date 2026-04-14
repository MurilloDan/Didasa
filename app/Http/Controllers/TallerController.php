<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TallerController extends Controller
{
    public function index()
    {
        $talleres = Taller::orderBy('name')->get();

        return Inertia::render('Talleres/Index', [
            'talleres' => $talleres,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'city' => 'required|string|max:80',
        ]);

        Taller::create($data);

        return redirect()->route('talleres')->with('success', 'Taller creado correctamente.');
    }

    public function update(Request $request, Taller $taller)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:120',
            'city'   => 'required|string|max:80',
            'active' => 'boolean',
        ]);

        $taller->update($data);

        return redirect()->route('talleres')->with('success', 'Taller actualizado.');
    }

    public function destroy(Taller $taller)
    {
        $taller->delete();

        return redirect()->route('talleres')->with('success', 'Taller eliminado.');
    }
}
