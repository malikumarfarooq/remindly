<?php

namespace App\Http\Controllers;

use App\Models\RecurringInvoice;

class RecurringInvoiceController extends Controller
{
    public function index()
    {
        return view('recurring.index');
    }

    public function create()
    {
        return view('recurring.create');
    }

    public function edit(RecurringInvoice $recurring)
    {
        abort_unless($recurring->user_id === auth()->id(), 403);

        return view('recurring.edit', compact('recurring'));
    }

    public function togglePause(RecurringInvoice $recurring)
    {
        abort_unless($recurring->user_id === auth()->id(), 403);

        $recurring->update(['is_active' => ! $recurring->is_active]);

        return back()->with('success', $recurring->is_active
            ? 'Recurring invoice resumed.'
            : 'Recurring invoice paused.');
    }

    public function destroy(RecurringInvoice $recurring)
    {
        abort_unless($recurring->user_id === auth()->id(), 403);

        $recurring->delete();

        return redirect()->route('recurring.index')
            ->with('success', 'Recurring invoice deleted.');
    }
}
