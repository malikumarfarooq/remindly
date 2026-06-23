@extends('layouts.app')

@section('page-title', $client->name)

@section('topbar-actions')
    <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline"><i class="ti ti-edit"></i> Edit</a>
    <a href="{{ route('clients.index') }}" class="btn btn-outline"><i class="ti ti-arrow-left"></i> Back</a>
@endsection

@section('content')

@php
    $riskPillMap = ['low' => 'pill-paid', 'medium' => 'pill-pending', 'high' => 'pill-overdue'];
    $riskClass = $riskPillMap[$client->risk_level] ?? 'pill-pending';
@endphp

<div class="stats-grid" style="margin-bottom:20px">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon si-blue"><i class="ti ti-file-invoice"></i></div>
        </div>
        <div class="stat-label">Total Invoiced</div>
        <div class="stat-value">{{ $client->currency }} {{ number_format($client->total_invoiced, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon si-red"><i class="ti ti-alert-circle"></i></div>
        </div>
        <div class="stat-label">Outstanding</div>
        <div class="stat-value">{{ $client->currency }} {{ number_format($client->total_outstanding, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon si-green"><i class="ti ti-check"></i></div>
        </div>
        <div class="stat-label">Total Paid</div>
        <div class="stat-value">{{ $client->currency }} {{ number_format($client->total_paid, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon si-yellow"><i class="ti ti-clock"></i></div>
        </div>
        <div class="stat-label">Avg. Days Late</div>
        <div class="stat-value">{{ $client->avg_days_late }}</div>
    </div>
</div>

<div class="card" style="padding:20px;margin-bottom:20px">
    <div style="display:flex;gap:32px;flex-wrap:wrap">
        <div>
            <div class="stat-label">Contact</div>
            <div style="font-size:13.5px;margin-top:4px">{{ $client->email ?? '—' }}</div>
            <div style="font-size:13.5px;color:var(--text2)">{{ $client->phone ?? '—' }}</div>
        </div>
        <div>
            <div class="stat-label">Company</div>
            <div style="font-size:13.5px;margin-top:4px">{{ $client->company ?? '—' }}</div>
        </div>
        <div>
            <div class="stat-label">Risk Level</div>
            <span class="pill {{ $riskClass }}" style="margin-top:4px"><span class="pill-dot"></span>{{ ucfirst($client->risk_level) }}</span>
        </div>
        <div>
            <div class="stat-label">Preferred Channel</div>
            <div style="font-size:13.5px;margin-top:4px;text-transform:capitalize">{{ $client->preferred_channel }}</div>
        </div>
        <div>
            <div class="stat-label">Member Since</div>
            <div style="font-size:13.5px;margin-top:4px">{{ $client->member_since?->format('M j, Y') ?? '—' }}</div>
        </div>
    </div>

    @if ($client->notes)
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--gray-border)">
            <div class="stat-label">Notes</div>
            <div style="font-size:13.5px;margin-top:4px">{{ $client->notes }}</div>
        </div>
    @endif
</div>

<div style="font-size:12px;font-weight:500;color:var(--text2);letter-spacing:.4px;text-transform:uppercase;margin-bottom:12px">
    Invoice History
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Amount</th>
                <th>Issued</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $invoice)
                @php
                    $statusPillMap = [
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
                    $pillClass = $statusPillMap[$invoice->status] ?? 'pill-pending';
                @endphp
                <tr onclick="window.location='{{ route('invoices.show', $invoice) }}'">
                    <td style="color:var(--text2)">#{{ $invoice->invoice_number }}</td>
                    <td style="font-weight:500">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
                    <td style="color:var(--text2)">{{ $invoice->issue_date->format('M j, Y') }}</td>
                    <td style="color:var(--text2)">{{ $invoice->due_date->format('M j, Y') }}</td>
                    <td>
                        <span class="pill {{ $pillClass }}"><span class="pill-dot"></span>{{ ucfirst($invoice->status) }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">No invoices for this client yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($invoices->hasPages())
        <div style="margin-top:16px;padding:0 18px 16px">
            {{ $invoices->links() }}
        </div>
    @endif
</div>

@endsection