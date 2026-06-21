@extends('layouts.app')

@section('page-title', '#' . $invoice->invoice_number)

@section('topbar-actions')
    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline"><i class="ti ti-download"></i> Download PDF</a>

    @if ($invoice->status === 'draft')
        <form action="{{ route('invoices.markSent', $invoice) }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-outline"><i class="ti ti-send"></i> Mark as Sent</button>
        </form>
    @endif

    @if (! $invoice->isPaid())
        <form action="{{ route('invoices.markPaid', $invoice) }}" method="POST" style="display:inline" onsubmit="return confirm('Mark this invoice as fully paid?')">
            @csrf
            <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Mark as Paid</button>
        </form>
    @endif

    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline"><i class="ti ti-edit"></i> Edit</a>
    <a href="{{ route('invoices.index') }}" class="btn btn-outline"><i class="ti ti-arrow-left"></i> Back</a>
@endsection

@section('content')

@php
    $pillMap = [
        'paid'      => 'pill-paid',
        'draft'     => 'pill-pending',
        'sent'      => 'pill-sent',
        'viewed'    => 'pill-sent',
        'pending'   => 'pill-pending',
        'partial'   => 'pill-pending',
        'overdue'   => 'pill-overdue',
        'cancelled' => 'pill-pending',
        'disputed'  => 'pill-overdue',
    ];
    $pillClass = $pillMap[$invoice->status] ?? 'pill-pending';
@endphp

@if (session('success'))
    <div style="display:flex;align-items:center;gap:10px;padding:11px 16px;background:var(--green-light);color:var(--green);border-radius:var(--r);font-size:13.5px;margin-bottom:16px">
        <i class="ti ti-check"></i> {{ session('success') }}
    </div>
@endif

<div class="card" style="padding:20px">

    <div style="background:var(--gray-light);border:1px solid var(--gray-border);border-radius:var(--r);padding:10px 14px;margin-bottom:20px;font-size:12.5px;color:var(--text2);display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <i class="ti ti-link"></i> Client link:
        <code style="background:#fff;padding:2px 8px;border-radius:4px;color:var(--blue)">{{ route('invoices.public', $invoice->payment_link_token) }}</code>
    </div>

    <div style="display:flex;gap:32px;margin-bottom:20px;flex-wrap:wrap">
        <div>
            <div class="stat-label">Client</div>
            <div class="client-cell" style="margin-top:4px">
                <div class="av av-b">{{ strtoupper(substr($invoice->client->name ?? '?', 0, 1)) }}</div>
                {{ $invoice->client->name ?? '—' }}
            </div>
        </div>
        <div>
            <div class="stat-label">Status</div>
            <span class="pill {{ $pillClass }}" style="margin-top:4px"><span class="pill-dot"></span>{{ ucfirst($invoice->status) }}</span>
        </div>
        <div>
            <div class="stat-label">Issue Date</div>
            <div style="font-size:13.5px;margin-top:4px">{{ $invoice->issue_date->format('M j, Y') }}</div>
        </div>
        <div>
            <div class="stat-label">Due Date</div>
            <div style="font-size:13.5px;margin-top:4px">{{ $invoice->due_date->format('M j, Y') }}</div>
        </div>
    </div>

    <div style="font-size:12px;font-weight:500;color:var(--text2);letter-spacing:.4px;text-transform:uppercase;margin:20px 0 12px;padding-top:16px;border-top:1px solid var(--gray-border)">
        Line Items
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items->sortBy('sort_order') as $item)
                <tr style="cursor:default">
                    <td>
                        {{ $item->description }}
                        @if ($item->unit)
                            <span style="color:var(--text2);font-size:12px"> ({{ $item->unit }})</span>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td style="font-weight:500">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex;justify-content:flex-end;margin-top:16px">
        <div style="background:var(--blue-light);border:1px solid #c5cae9;border-radius:var(--r);padding:14px 20px;min-width:240px">
            <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--text2);margin-bottom:6px">
                <span>Subtotal</span><span>{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if ($invoice->discount_amount > 0)
                <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--text2);margin-bottom:6px">
                    <span>Discount</span><span>− {{ $invoice->currency }} {{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
            @endif
            @if ($invoice->tax_rate > 0)
                <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--text2);margin-bottom:6px">
                    <span>{{ $invoice->tax_name ?? 'Tax' }} ({{ $invoice->tax_rate }}%)</span>
                    <span>{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
            @endif
            <div style="display:flex;justify-content:space-between;border-top:1px solid #c5cae9;padding-top:8px;margin-top:4px">
                <span style="font-size:13px;font-weight:500;color:var(--blue)">TOTAL</span>
                <span style="font-size:18px;font-weight:500;color:var(--blue)">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--text2);margin-top:8px">
                <span>Amount Paid</span><span>{{ $invoice->currency }} {{ number_format($invoice->amount_paid, 2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--red);font-weight:500">
                <span>Outstanding</span><span>{{ $invoice->currency }} {{ number_format($invoice->amount_outstanding, 2) }}</span>
            </div>
        </div>
    </div>

    @if ($invoice->notes || $invoice->terms)
        <div style="font-size:12px;font-weight:500;color:var(--text2);letter-spacing:.4px;text-transform:uppercase;margin:24px 0 12px;padding-top:16px;border-top:1px solid var(--gray-border)">
            Notes &amp; Terms
        </div>
        <div style="display:flex;gap:32px;flex-wrap:wrap">
            @if ($invoice->notes)
                <div style="flex:1;min-width:200px">
                    <div class="stat-label">Notes</div>
                    <div style="font-size:13.5px;margin-top:4px">{{ $invoice->notes }}</div>
                </div>
            @endif
            @if ($invoice->terms)
                <div style="flex:1;min-width:200px">
                    <div class="stat-label">Terms</div>
                    <div style="font-size:13.5px;margin-top:4px">{{ $invoice->terms }}</div>
                </div>
            @endif
        </div>
    @endif

</div>

@endsection