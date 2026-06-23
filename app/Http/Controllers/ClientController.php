<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Client::class);
        return view('clients.index');
    }

    public function create()
    {
        $this->authorize('create', Client::class);
        return view('clients.create');
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);

        $invoices = $client->invoices()
            ->latest()
            ->paginate(10);

        return view('clients.show', compact('client', 'invoices'));
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);
        return view('clients.edit', compact('client'));
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
