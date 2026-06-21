<?php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all'; // all | overdue | pending | paid
    public array $selected = [];
    public bool $selectAll = false;
    public bool $showTrashed = false;

    protected $queryString = ['search', 'statusFilter'];

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->getInvoicesQuery()
                ->get()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function setFilter(string $filter): void
    {
        $this->statusFilter = $filter;
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function toggleTrashed(): void
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    // ── Bulk: mark selected as paid ────────────────────────
    public function bulkMarkPaid(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $invoices = Invoice::where('user_id', auth()->id())
            ->whereIn('id', $this->selected)
            ->get();

        foreach ($invoices as $invoice) {
            $this->authorize('update', $invoice);
            $invoice->update([
                'status'             => 'paid',
                'paid_at'            => now(),
                'amount_paid'        => $invoice->total_amount,
                'amount_outstanding' => 0,
            ]);
        }

        session()->flash('success', count($this->selected) . ' invoice(s) marked as paid.');
        $this->selected = [];
        $this->selectAll = false;
    }

    // ── Bulk: mark selected as sent (placeholder for reminder send) ──
    public function bulkSendReminder(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $invoices = Invoice::where('user_id', auth()->id())
            ->whereIn('id', $this->selected)
            ->get();

        foreach ($invoices as $invoice) {
            $this->authorize('update', $invoice);
            if ($invoice->status === 'draft') {
                $invoice->update(['status' => 'sent']);
            }
            $invoice->update(['last_reminder_sent_at' => now()]);
        }

        session()->flash('success', count($this->selected) . ' reminder(s) queued. (Actual sending arrives in Week 3.)');
        $this->selected = [];
        $this->selectAll = false;
    }

    // ── Single row: soft delete ────────────────────────────
    public function deleteInvoice(int $id): void
    {
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);
        $this->authorize('delete', $invoice);
        $invoice->delete();

        session()->flash('success', 'Invoice deleted.');
    }

    // ── Single row: restore from trash ─────────────────────
    public function restoreInvoice(int $id): void
    {
        $invoice = Invoice::onlyTrashed()->where('user_id', auth()->id())->findOrFail($id);
        $this->authorize('update', $invoice);
        $invoice->restore();

        session()->flash('success', 'Invoice restored.');
    }

    private function getInvoicesQuery()
    {
        $query = $this->showTrashed
            ? Invoice::onlyTrashed()
            : Invoice::query();

        $query->where('user_id', auth()->id())->with('client');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('client', function ($cq) {
                        $cq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        match ($this->statusFilter) {
            'overdue' => $query->where('status', 'overdue'),
            'pending' => $query->whereIn('status', ['draft', 'sent', 'viewed', 'pending', 'partial']),
            'paid'    => $query->where('status', 'paid'),
            default   => null,
        };

        return $query->latest();
    }

    public function render()
    {
        $invoices = $this->getInvoicesQuery()->paginate(10);

        $counts = [
            'all'     => Invoice::where('user_id', auth()->id())->count(),
            'overdue' => Invoice::where('user_id', auth()->id())->where('status', 'overdue')->count(),
            'pending' => Invoice::where('user_id', auth()->id())->whereIn('status', ['draft', 'sent', 'viewed', 'pending', 'partial'])->count(),
            'paid'    => Invoice::where('user_id', auth()->id())->where('status', 'paid')->count(),
        ];

        return view('livewire.invoice-list', [
            'invoices' => $invoices,
            'counts'   => $counts,
        ]);
    }
}
