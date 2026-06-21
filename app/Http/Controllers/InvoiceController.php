<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Invoice::class);
        return view('invoices.index');
    }

    public function create()
    {
        $this->authorize('create', Invoice::class);
        return view('invoices.create');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('items', 'client');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        return view('invoices.edit', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);

        ActivityLog::record(
            auth()->id(),
            'deleted',
            "Invoice {$invoice->invoice_number} deleted.",
            $invoice
        );

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    // ── PDF download (logged-in user) ──────────────────────
    public function downloadPdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('items', 'client');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    // ── Status: draft → sent ───────────────────────────────
    public function markAsSent(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent']);

            ActivityLog::record(
                auth()->id(),
                'sent',
                "Invoice {$invoice->invoice_number} marked as sent.",
                $invoice
            );
        }

        return back()->with('success', 'Invoice marked as sent.');
    }

    // ── Status: anything → paid ────────────────────────────
    public function markAsPaid(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $invoice->update([
            'status'             => 'paid',
            'paid_at'            => now(),
            'amount_paid'        => $invoice->total_amount,
            'amount_outstanding' => 0,
        ]);

        ActivityLog::record(
            auth()->id(),
            'paid',
            "Invoice {$invoice->invoice_number} marked as paid.",
            $invoice,
            ['amount' => (string) $invoice->total_amount]
        );

        return back()->with('success', 'Invoice marked as paid.');
    }

    // ── Public view (no login — client opens via token link) ──
    public function publicShow(string $token)
    {
        $invoice = Invoice::where('payment_link_token', $token)
            ->with('items', 'client')
            ->firstOrFail();

        if (in_array($invoice->status, ['sent', 'pending']) && ! $invoice->viewed_at) {
            $invoice->update([
                'status'    => 'viewed',
                'viewed_at' => now(),
            ]);

            ActivityLog::record(
                $invoice->user_id,
                'viewed',
                "Invoice {$invoice->invoice_number} viewed by client.",
                $invoice
            );
        }

        $invoice->increment('payment_link_views');

        return view('invoices.public', compact('invoice'));
    }
}
