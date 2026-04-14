<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        return Inertia::render('Clients/Index', [
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:clients,email',
            'phone' => 'nullable|string|max:20',
        ]);

        Client::create($data);

        return redirect()->route('clients')->with('success', 'Client created.');
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $client->update($data);

        return redirect()->route('clients')->with('success', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients')->with('success', 'Client deleted.');
    }
}
