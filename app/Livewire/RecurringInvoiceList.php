<?php

namespace App\Livewire;

use App\Models\RecurringInvoice;
use Livewire\Component;
use Livewire\WithPagination;

class RecurringInvoiceList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all'; // all | active | paused
    public array $selected = [];
    public bool $selectAll = false;

    protected $queryString = ['search', 'statusFilter'];

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function setFilter(string $filter): void
    {
        $this->statusFilter = $filter;
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->getQuery()
                ->get()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function togglePause(int $id): void
    {
        $recurring = RecurringInvoice::where('user_id', auth()->id())->findOrFail($id);
        $recurring->update(['is_active' => ! $recurring->is_active]);

        session()->flash('success', $recurring->is_active
            ? 'Recurring invoice resumed.'
            : 'Recurring invoice paused.');
    }

    public function deleteRecurring(int $id): void
    {
        $recurring = RecurringInvoice::where('user_id', auth()->id())->findOrFail($id);
        $recurring->delete();

        session()->flash('success', 'Recurring invoice deleted.');
    }

    // ── Bulk: pause selected ───────────────────────────────
    public function bulkPause(): void
    {
        if (empty($this->selected)) {
            return;
        }

        RecurringInvoice::where('user_id', auth()->id())
            ->whereIn('id', $this->selected)
            ->update(['is_active' => false]);

        session()->flash('success', count($this->selected) . ' recurring invoice(s) paused.');
        $this->selected = [];
        $this->selectAll = false;
    }

    // ── Bulk: resume selected ──────────────────────────────
    public function bulkResume(): void
    {
        if (empty($this->selected)) {
            return;
        }

        RecurringInvoice::where('user_id', auth()->id())
            ->whereIn('id', $this->selected)
            ->update(['is_active' => true]);

        session()->flash('success', count($this->selected) . ' recurring invoice(s) resumed.');
        $this->selected = [];
        $this->selectAll = false;
    }

    // ── Bulk: delete selected ──────────────────────────────
    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        RecurringInvoice::where('user_id', auth()->id())
            ->whereIn('id', $this->selected)
            ->delete();

        session()->flash('success', count($this->selected) . ' recurring invoice(s) deleted.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery()
    {
        $query = RecurringInvoice::where('user_id', auth()->id())->with('client');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('client', function ($cq) {
                        $cq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        match ($this->statusFilter) {
            'active' => $query->where('is_active', true),
            'paused' => $query->where('is_active', false),
            default  => null,
        };

        return $query->latest();
    }

    public function render()
    {
        $recurringInvoices = $this->getQuery()->paginate(10);

        $counts = [
            'all'    => RecurringInvoice::where('user_id', auth()->id())->count(),
            'active' => RecurringInvoice::where('user_id', auth()->id())->where('is_active', true)->count(),
            'paused' => RecurringInvoice::where('user_id', auth()->id())->where('is_active', false)->count(),
        ];

        return view('livewire.recurring-invoice-list', compact('recurringInvoices', 'counts'));
    }
}
