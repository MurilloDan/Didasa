<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index()
    {
        return Inertia::render('Staff/Index', [
            'staff' => Staff::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'role'      => 'required|string|max:80',
            'initials'  => 'required|string|max:5',
            'avatar_bg' => 'nullable|string|max:20',
        ]);

        Staff::create($data);

        return redirect()->route('staff')->with('success', 'Staff member created.');
    }

    public function update(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'role'      => 'required|string|max:80',
            'initials'  => 'required|string|max:5',
            'avatar_bg' => 'nullable|string|max:20',
            'active'    => 'boolean',
        ]);

        $staff->update($data);

        return redirect()->route('staff')->with('success', 'Staff member updated.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('staff')->with('success', 'Staff member deleted.');
    }
}
