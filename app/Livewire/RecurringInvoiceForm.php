<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\RecurringInvoice;
use Livewire\Component;

class RecurringInvoiceForm extends Component
{
    public ?int   $recurringId = null;

    public ?int   $clientId = null;
    public string $title = '';
    public string $frequency = 'monthly';
    public string $startDate = '';
    public string $endDate = '';
    public int    $paymentDueDays = 14;
    public string $currency = 'USD';
    public bool   $autoSend = true;

    public array $items = [];

    public float $total = 0;

    protected function rules(): array
    {
        return [
            'clientId'             => 'required|exists:clients,id',
            'title'                => 'required|string|max:255',
            'frequency'            => 'required|in:weekly,biweekly,monthly,quarterly,yearly',
            'startDate'            => 'required|date',
            'endDate'              => 'nullable|date|after:startDate',
            'paymentDueDays'       => 'required|integer|min:0|max:365',
            'items'                => 'required|array|min:1',
            'items.*.description'  => 'required|string|max:255',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ];
    }

    public function mount(?RecurringInvoice $recurring = null): void
    {
        if ($recurring && $recurring->exists) {
            // ── Edit mode ──
            $this->recurringId    = $recurring->id;
            $this->clientId       = $recurring->client_id;
            $this->title          = $recurring->title;
            $this->frequency      = $recurring->frequency;
            $this->startDate      = $recurring->start_date->format('Y-m-d');
            $this->endDate        = $recurring->end_date?->format('Y-m-d') ?? '';
            $this->paymentDueDays = $recurring->payment_due_days;
            $this->currency       = $recurring->currency;
            $this->autoSend       = $recurring->auto_send;
            $this->items          = $recurring->line_items ?: [$this->blankItem()];
        } else {
            // ── Create mode ──
            $this->startDate = now()->format('Y-m-d');
            $this->items     = [$this->blankItem()];
        }

        $this->calculateTotal();
    }

    private function blankItem(): array
    {
        return [
            'description' => '',
            'quantity'    => 1,
            'unit_price'  => 0,
        ];
    }

    public function addItem(): void
    {
        $this->items[] = $this->blankItem();
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            array_splice($this->items, $index, 1);
            $this->calculateTotal();
        }
    }

    public function updatedItems(): void
    {
        $this->calculateTotal();
    }

    private function calculateTotal(): void
    {
        $this->total = round(
            collect($this->items)->sum(
                fn($item) =>
                (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0)
            ),
            2
        );
    }

    public function save(): void
    {
        $this->calculateTotal();
        $this->validate();

        $data = [
            'user_id'          => auth()->id(),
            'client_id'        => $this->clientId,
            'title'            => $this->title,
            'frequency'        => $this->frequency,
            'start_date'       => $this->startDate,
            'end_date'         => $this->endDate ?: null,
            'payment_due_days' => $this->paymentDueDays,
            'line_items'       => $this->items,
            'total_amount'     => $this->total,
            'currency'         => $this->currency,
            'auto_send'        => $this->autoSend,
        ];

        if ($this->recurringId) {
            // ── Update existing ──
            $recurring = RecurringInvoice::where('user_id', auth()->id())->findOrFail($this->recurringId);
            $recurring->update($data);
            session()->flash('success', 'Recurring invoice updated.');
        } else {
            // ── Create new ──
            $data['is_active']          = true;
            $data['next_generation_at'] = $this->startDate;
            RecurringInvoice::create($data);
            session()->flash('success', 'Recurring invoice created.');
        }

        $this->redirect(route('recurring.index'), navigate: true);
    }

    public function render()
    {
        $clients = Client::where('user_id', auth()->id())->orderBy('name')->get(['id', 'name']);

        return view('livewire.recurring-invoice-form', compact('clients'));
    }
}
