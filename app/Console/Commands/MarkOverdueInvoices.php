<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature = 'invoices:mark-overdue';

    protected $description = 'Mark unpaid invoices past their due date as overdue, and update days_overdue counts.';

    public function handle(): int
    {
        $candidates = Invoice::whereIn('status', ['sent', 'viewed', 'pending', 'partial'])
            ->where('due_date', '<', now()->startOfDay())
            ->get();

        $marked = 0;

        foreach ($candidates as $invoice) {
            $daysOverdue = (int) now()->startOfDay()->diffInDays($invoice->due_date);

            $invoice->update([
                'status'       => 'overdue',
                'days_overdue' => $daysOverdue,
            ]);

            ActivityLog::record(
                $invoice->user_id,
                'overdue',
                "Invoice {$invoice->invoice_number} marked overdue ({$daysOverdue} days past due).",
                $invoice
            );

            $marked++;
        }

        // Refresh days_overdue for invoices already marked overdue
        Invoice::where('status', 'overdue')->get()->each(function (Invoice $invoice) {
            $invoice->update([
                'days_overdue' => (int) now()->startOfDay()->diffInDays($invoice->due_date),
            ]);
        });

        $this->info("{$marked} invoice(s) newly marked overdue.");

        return self::SUCCESS;
    }
}
