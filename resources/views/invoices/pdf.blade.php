<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #202124; margin: 0; padding: 30px; }
        .header { width: 100%; margin-bottom: 30px; }
        .header td { vertical-align: top; }
        .brand { font-size: 20px; font-weight: bold; color: #6d28d9; }
        .inv-meta { text-align: right; }
        .inv-num { font-size: 16px; font-weight: bold; }
        .status { display: inline-block; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .status-paid { background: #d1fae5; color: #059669; }
        .status-draft, .status-pending, .status-partial, .status-cancelled { background: #fef7e0; color: #b06000; }
        .status-overdue, .status-disputed { background: #fce8e6; color: #ea4335; }
        .status-sent, .status-viewed { background: #ede9fe; color: #6d28d9; }

        table.info { width: 100%; margin-bottom: 25px; }
        table.info td { vertical-align: top; padding-bottom: 4px; }
        .label { color: #5f6368; font-size: 10px; text-transform: uppercase; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th { background: #f8f9fa; text-align: left; padding: 8px; font-size: 11px; color: #5f6368; border-bottom: 1px solid #dadce0; }
        table.items td { padding: 8px; border-bottom: 1px solid #eee; font-size: 12px; }
        table.items .num { text-align: right; }

        table.totals { width: 100%; margin-top: 10px; }
        table.totals td { padding: 4px 8px; font-size: 12px; }
        table.totals .t-label { text-align: right; color: #5f6368; }
        table.totals .t-value { text-align: right; width: 100px; }
        table.totals .grand td { border-top: 2px solid #6d28d9; font-size: 15px; font-weight: bold; color: #6d28d9; padding-top: 8px; }

        .notes { margin-top: 30px; font-size: 11px; color: #5f6368; }
        .notes .label { margin-bottom: 4px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #9aa0a6; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <div class="brand">Remindly</div>
            </td>
            <td class="inv-meta">
                <div class="inv-num">{{ $invoice->invoice_number }}</div>
                <span class="status status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </td>
        </tr>
    </table>

    <table class="info">
        <tr>
            <td width="50%">
                <div class="label">Billed To</div>
                <div><strong>{{ $invoice->client->name ?? '—' }}</strong></div>
                @if ($invoice->client->email ?? false)
                    <div>{{ $invoice->client->email }}</div>
                @endif
            </td>
            <td width="25%">
                <div class="label">Issue Date</div>
                <div>{{ $invoice->issue_date->format('M j, Y') }}</div>
            </td>
            <td width="25%">
                <div class="label">Due Date</div>
                <div>{{ $invoice->due_date->format('M j, Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="num">Qty</th>
                <th class="num">Unit Price</th>
                <th class="num">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items->sortBy('sort_order') as $item)
                <tr>
                    <td>
                        {{ $item->description }}
                        @if ($item->unit) <span style="color:#5f6368"> ({{ $item->unit }})</span> @endif
                    </td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="num">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="t-label">Subtotal</td>
            <td class="t-value">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if ($invoice->discount_amount > 0)
        <tr>
            <td class="t-label">Discount</td>
            <td class="t-value">− {{ $invoice->currency }} {{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>
        @endif
        @if ($invoice->tax_rate > 0)
        <tr>
            <td class="t-label">{{ $invoice->tax_name ?? 'Tax' }} ({{ $invoice->tax_rate }}%)</td>
            <td class="t-value">{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        @endif
        <tr class="grand">
            <td class="t-label">Total</td>
            <td class="t-value">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
    </table>

    @if ($invoice->notes)
        <div class="notes">
            <div class="label">Notes</div>
            <div>{{ $invoice->notes }}</div>
        </div>
    @endif

    @if ($invoice->terms)
        <div class="notes">
            <div class="label">Terms &amp; Conditions</div>
            <div>{{ $invoice->terms }}</div>
        </div>
    @endif

    <div class="footer">Generated by Remindly — {{ now()->format('M j, Y') }}</div>

</body>
</html>