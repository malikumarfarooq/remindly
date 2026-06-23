<?php

namespace App\Http\Controllers;

use App\Models\ReminderSequence;

class ReminderController extends Controller
{
    public function index()
    {
        return view('reminders.index');
    }

    public function create()
    {
        return view('reminders.create');
    }

    public function edit(ReminderSequence $sequence)
    {
        abort_unless($sequence->user_id === auth()->id(), 403);
        return view('reminders.edit', compact('sequence'));
    }

    public function destroy(ReminderSequence $sequence)
    {
        abort_unless($sequence->user_id === auth()->id(), 403);
        $sequence->delete();

        return redirect()->route('reminders.index')
            ->with('success', 'Reminder sequence deleted.');
    }
}
