<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\RecurringInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRecurringInvoices extends Command
{
    protected $signature = 'invoices:generate-recurring';

    protected $description = 'Generate invoices from active recurring invoice templates that are due.';

    public function handle(): int
    {
        $due = RecurringInvoice::where('is_active', true)
            ->whereNotNull('next_generation_at')
            ->where('next_generation_at', '<=', now()->startOfDay())
            ->get();

        if ($due->isEmpty()) {
            $this->info('No recurring invoices are due today.');
            return self::SUCCESS;
        }

        $generated = 0;

        foreach ($due as $recurring) {

            if ($recurring->hasEnded()) {
                $recurring->update(['is_active' => false]);
                $this->line("Skipped #{$recurring->id} ({$recurring->title}) — end date passed, deactivated.");
                continue;
            }

            $issueDate = now()->startOfDay();
            $dueDate   = $issueDate->copy()->addDays($recurring->payment_due_days);

            $lineItems = $recurring->line_items ?? [];
            $subtotal  = collect($lineItems)->sum(function ($item) {
                return (float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0);
            });

            $invoiceCount  = Invoice::where('user_id', $recurring->user_id)->withTrashed()->count() + 1;
            $invoiceNumber = ($recurring->user->invoice_prefix ?? 'INV') . '-' . now()->year . '-' . str_pad($invoiceCount, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'user_id'              => $recurring->user_id,
                'client_id'            => $recurring->client_id,
                'recurring_invoice_id' => $recurring->id,
                'invoice_number'       => $invoiceNumber,
                'status'               => $recurring->auto_send ? 'sent' : 'draft',
                'issue_date'           => $issueDate,
                'due_date'             => $dueDate,
                'currency'             => $recurring->currency,
                'subtotal'             => $subtotal,
                'total_amount'         => $subtotal,
                'amount_paid'          => 0,
                'amount_outstanding'   => $subtotal,
                'payment_link_token'   => Str::random(64),
                'notes'                => "Auto-generated from recurring invoice: {$recurring->title}",
            ]);

            foreach ($lineItems as $sortOrder => $item) {
                $invoice->items()->create([
                    'description' => $item['description'] ?? 'Item',
                    'unit'        => $item['unit'] ?? null,
                    'quantity'    => $item['quantity'] ?? 1,
                    'unit_price'  => $item['unit_price'] ?? 0,
                    'total'       => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0),
                    'sort_order'  => $sortOrder,
                ]);
            }

            ActivityLog::record(
                $recurring->user_id,
                'created',
                "Invoice {$invoice->invoice_number} auto-generated from recurring template \"{$recurring->title}\".",
                $invoice
            );

            $recurring->update([
                'last_generated_at'  => $issueDate,
                'next_generation_at' => $recurring->calculateNextDate(),
                'invoices_generated' => $recurring->invoices_generated + 1,
            ]);

            $generated++;
            $this->line("Generated {$invoice->invoice_number} for {$recurring->client->name} (from \"{$recurring->title}\").");
        }

        $this->info("Done. {$generated} invoice(s) generated.");

        return self::SUCCESS;
    }
}
