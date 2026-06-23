<?php

namespace App\Livewire;

use App\Models\ReminderSequence;
use Livewire\Component;
use Livewire\WithPagination;

class ReminderSequenceList extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $sequence = ReminderSequence::where('user_id', auth()->id())->findOrFail($id);
        $sequence->update(['is_active' => ! $sequence->is_active]);

        session()->flash('success', $sequence->is_active
            ? 'Sequence activated.'
            : 'Sequence deactivated.');
    }

    public function deleteSequence(int $id): void
    {
        $sequence = ReminderSequence::where('user_id', auth()->id())->findOrFail($id);
        $sequence->delete();

        session()->flash('success', 'Sequence deleted.');
    }

    public function setDefault(int $id): void
    {
        ReminderSequence::where('user_id', auth()->id())
            ->update(['is_default' => false]);

        ReminderSequence::where('user_id', auth()->id())
            ->where('id', $id)
            ->update(['is_default' => true]);

        session()->flash('success', 'Default sequence updated.');
    }

    public function render()
    {
        $sequences = ReminderSequence::where('user_id', auth()->id())
            ->when(
                $this->search,
                fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
            )
            ->withCount('steps')
            ->latest()
            ->paginate(10);

        return view('livewire.reminder-sequence-list', compact('sequences'));
    }
}
