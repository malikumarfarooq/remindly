<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Str;
use Livewire\Component;

class InvoiceForm extends Component
{
    // ── Meta ──────────────────────────────────────────────
    public ?int   $invoiceId     = null;
    public string $invoiceNumber = '';

    // ── Core fields ───────────────────────────────────────
    public ?int   $clientId  = null;
    public string $issueDate = '';
    public string $dueDate   = '';
    public string $currency  = 'USD';
    public string $notes     = '';
    public string $terms     = '';

    // ── Tax (invoice-level) ───────────────────────────────
    public string $taxName = '';
    public float  $taxRate = 0;

    // ── Discount (invoice-level) ──────────────────────────
    // discount_type in DB: 'percent' | 'fixed' | null
    public string $discountType  = 'none';   // UI value: none | fixed | percent
    public float  $discountValue = 0;

    // ── Line items ────────────────────────────────────────
    // Each row matches invoice_items columns exactly:
    // description, details, unit, quantity, unit_price, tax_rate, discount_percent, total, sort_order
    public array $items = [];

    // ── Computed totals (display only, not stored directly) ──
    public float $subtotal       = 0;
    public float $discountAmount = 0;
    public float $taxAmount      = 0;
    public float $total          = 0;

    // ─────────────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'clientId'              => 'required|exists:clients,id',
            'issueDate'             => 'required|date',
            'dueDate'               => 'required|date|after_or_equal:issueDate',
            'currency'              => 'required|string|size:3',
            'taxRate'               => 'numeric|min:0|max:100',
            'discountType'          => 'in:none,fixed,percent',
            'discountValue'         => 'numeric|min:0',
            'items'                 => 'required|array|min:1',
            'items.*.description'   => 'required|string|max:255',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ];
    }

    // ─────────────────────────────────────────────────────
    public function mount(?Invoice $invoice = null): void
    {
        if ($invoice && $invoice->exists) {
            // ── Edit mode ──
            $this->invoiceId     = $invoice->id;
            $this->invoiceNumber = $invoice->invoice_number;
            $this->clientId      = $invoice->client_id;
            $this->issueDate     = $invoice->issue_date->format('Y-m-d');
            $this->dueDate       = $invoice->due_date->format('Y-m-d');
            $this->currency      = $invoice->currency;
            $this->taxName       = $invoice->tax_name ?? '';
            $this->taxRate       = (float) $invoice->tax_rate;
            $this->discountType  = $invoice->discount_type ?? 'none';
            $this->discountValue = (float) $invoice->discount_value;
            $this->notes         = $invoice->notes ?? '';
            $this->terms         = $invoice->terms ?? '';

            $this->items = $invoice->items
                ->sortBy('sort_order')
                ->map(fn($item) => [
                    'description'      => $item->description,
                    'details'          => $item->details ?? '',
                    'unit'             => $item->unit ?? '',
                    'quantity'         => (float) $item->quantity,
                    'unit_price'       => (float) $item->unit_price,
                    'tax_rate'         => (float) $item->tax_rate,
                    'discount_percent' => (float) $item->discount_percent,
                    'total'            => (float) $item->total,
                ])
                ->values()
                ->toArray();
        } else {
            // ── Create mode ──
            $this->invoiceNumber = $this->generateInvoiceNumber();
            $this->issueDate     = now()->format('Y-m-d');
            $this->dueDate       = now()->addDays(30)->format('Y-m-d');
            $this->items         = [$this->blankItem()];
        }

        $this->calculateTotals();
    }

    // ─────────────────────────────────────────────────────
    private function blankItem(): array
    {
        return [
            'description'      => '',
            'details'          => '',
            'unit'             => '',
            'quantity'         => 1,
            'unit_price'       => 0,
            'tax_rate'         => 0,
            'discount_percent' => 0,
            'total'            => 0,
        ];
    }

    // ─────────────────────────────────────────────────────
    private function generateInvoiceNumber(): string
    {
        $user   = auth()->user();
        $prefix = $user->invoice_prefix ?? 'INV';
        $year   = now()->year;
        $count  = Invoice::where('user_id', $user->id)->withTrashed()->count() + 1;

        return $prefix . '-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ── Line item actions ─────────────────────────────────
    public function addItem(): void
    {
        $this->items[] = $this->blankItem();
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            array_splice($this->items, $index, 1);
            $this->calculateTotals();
        }
    }

    // ── Livewire watchers — recalculate on any change ─────
    public function updatedItems(): void
    {
        $this->calculateTotals();
    }
    public function updatedTaxRate(): void
    {
        $this->calculateTotals();
    }
    public function updatedDiscountType(): void
    {
        $this->calculateTotals();
    }
    public function updatedDiscountValue(): void
    {
        $this->calculateTotals();
    }

    // ─────────────────────────────────────────────────────
    private function calculateTotals(): void
    {
        // Recalculate each line's total (quantity × unit_price)
        // Note: line-level tax & discount_percent are stored but NOT applied to invoice total
        //       (they will be used in PDF display; invoice totals use invoice-level tax/discount)
        foreach ($this->items as &$item) {
            $qty   = (float) ($item['quantity']   ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $item['total'] = round($qty * $price, 2);
        }
        unset($item);

        $this->subtotal = round(
            array_sum(array_column($this->items, 'total')),
            2
        );

        // Invoice-level discount
        $this->discountAmount = match ($this->discountType) {
            'fixed'   => min((float) $this->discountValue, $this->subtotal),
            'percent' => round($this->subtotal * ((float) $this->discountValue / 100), 2),
            default   => 0.0,
        };

        $afterDiscount   = $this->subtotal - $this->discountAmount;
        $this->taxAmount = round($afterDiscount * ((float) $this->taxRate / 100), 2);
        $this->total     = round($afterDiscount + $this->taxAmount, 2);
    }

    // ─────────────────────────────────────────────────────
    public function save(): void
    {
        $this->calculateTotals();
        $this->validate();

        $userId = auth()->id();

        // Map UI discount type to DB values (null when none)
        $dbDiscountType = $this->discountType === 'none' ? null : $this->discountType;

        $invoiceData = [
            'user_id'            => $userId,
            'client_id'          => $this->clientId,
            'invoice_number'     => $this->invoiceNumber,
            'issue_date'         => $this->issueDate,
            'due_date'           => $this->dueDate,
            'currency'           => $this->currency,
            'tax_name'           => $this->taxName ?: null,
            'tax_rate'           => $this->taxRate,
            'tax_amount'         => $this->taxAmount,
            'discount_type'      => $dbDiscountType,
            'discount_value'     => $this->discountValue,
            'discount_amount'    => $this->discountAmount,
            'subtotal'           => $this->subtotal,
            'total_amount'       => $this->total,
            'amount_outstanding' => $this->total,
            'notes'              => $this->notes ?: null,
            'terms'              => $this->terms ?: null,
        ];

        if ($this->invoiceId) {
            // ── Edit: security check — only owner can update ──
            $invoice = Invoice::where('user_id', $userId)->findOrFail($this->invoiceId);
            $invoice->update($invoiceData);
        } else {
            // ── Create: set initial values ──
            $invoiceData['status']             = 'draft';
            $invoiceData['amount_paid']        = 0;
            $invoiceData['payment_link_token'] = Str::random(64);
            $invoice = Invoice::create($invoiceData);
        }

        // ── Sync line items (delete old, insert new) ──
        $invoice->items()->delete();

        foreach ($this->items as $sortOrder => $item) {
            $invoice->items()->create([
                'description'      => $item['description'],
                'details'          => $item['details'] ?: null,
                'unit'             => $item['unit'] ?: null,
                'quantity'         => $item['quantity'],
                'unit_price'       => $item['unit_price'],
                'tax_rate'         => $item['tax_rate'] ?? 0,
                'discount_percent' => $item['discount_percent'] ?? 0,
                'total'            => $item['total'],
                'sort_order'       => $sortOrder,
            ]);
        }

        session()->flash('success', $this->invoiceId ? 'Invoice updated.' : 'Invoice created.');
        $this->redirect(route('invoices.index'), navigate: true);
    }

    // ─────────────────────────────────────────────────────
    public function render()
    {
        $clients = Client::where('user_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.invoice-form', compact('clients'));
    }
}
