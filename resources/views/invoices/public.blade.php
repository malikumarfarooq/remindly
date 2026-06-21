<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#f8f9fa;color:#202124;padding:40px 20px}
        .wrap{max-width:680px;margin:0 auto;background:#fff;border:1px solid #dadce0;border-radius:12px;padding:32px}
        .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px}
        .brand{font-size:20px;font-weight:700;color:#6d28d9}
        .inv-num{font-size:16px;font-weight:600;text-align:right}
        .pill{display:inline-block;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;margin-top:6px}
        .pill-paid{background:#d1fae5;color:#059669}
        .pill-draft,.pill-pending,.pill-partial,.pill-cancelled{background:#fef7e0;color:#b06000}
        .pill-overdue,.pill-disputed{background:#fce8e6;color:#ea4335}
        .pill-sent,.pill-viewed{background:#ede9fe;color:#6d28d9}

        .meta{display:flex;gap:32px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #dadce0}
        .meta-label{font-size:11px;color:#5f6368;text-transform:uppercase;margin-bottom:4px}
        .meta-value{font-size:13.5px}

        table{width:100%;border-collapse:collapse;margin-bottom:20px}
        thead th{text-align:left;padding:8px;font-size:11.5px;color:#5f6368;border-bottom:1px solid #dadce0;background:#f8f9fa}
        tbody td{padding:10px 8px;font-size:13.5px;border-bottom:1px solid #eee}
        .num{text-align:right}

        .totals{display:flex;justify-content:flex-end;margin-bottom:24px}
        .totals-box{background:#ede9fe;border:1px solid #c5cae9;border-radius:8px;padding:14px 20px;min-width:220px}
        .t-row{display:flex;justify-content:space-between;font-size:13px;color:#5f6368;margin-bottom:6px}
        .t-grand{display:flex;justify-content:space-between;border-top:1px solid #c5cae9;padding-top:8px;margin-top:4px}
        .t-grand span{font-weight:700;color:#6d28d9}
        .t-grand .amt{font-size:18px}

        .notes{font-size:12.5px;color:#5f6368;margin-bottom:8px;line-height:1.6}
        .notes strong{display:block;color:#202124;margin-bottom:2px;font-size:11px;text-transform:uppercase}

        .pay-btn{display:block;text-align:center;background:#6d28d9;color:#fff;text-decoration:none;padding:13px;border-radius:8px;font-weight:600;font-size:14px;margin-top:24px}
        .pay-btn:hover{background:#5b21b6}
        .footer{text-align:center;font-size:11px;color:#9aa0a6;margin-top:24px}
    </style>
</head>
<body>

<div class="wrap">

    <div class="top">
        <div>
            <div class="brand">Remindly</div>
        </div>
        <div>
            <div class="inv-num">{{ $invoice->invoice_number }}</div>
            <span class="pill pill-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
        </div>
    </div>

    <div class="meta">
        <div>
            <div class="meta-label">Billed To</div>
            <div class="meta-value">{{ $invoice->client->name ?? '—' }}</div>
        </div>
        <div>
            <div class="meta-label">Issue Date</div>
            <div class="meta-value">{{ $invoice->issue_date->format('M j, Y') }}</div>
        </div>
        <div>
            <div class="meta-label">Due Date</div>
            <div class="meta-value">{{ $invoice->due_date->format('M j, Y') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr><th>Description</th><th class="num">Qty</th><th class="num">Price</th><th class="num">Total</th></tr>
        </thead>
        <tbody>
            @foreach ($invoice->items->sortBy('sort_order') as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="num">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-box">
            <div class="t-row"><span>Subtotal</span><span>{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span></div>
            @if ($invoice->discount_amount > 0)
                <div class="t-row"><span>Discount</span><span>− {{ $invoice->currency }} {{ number_format($invoice->discount_amount, 2) }}</span></div>
            @endif
            @if ($invoice->tax_rate > 0)
                <div class="t-row"><span>{{ $invoice->tax_name ?? 'Tax' }} ({{ $invoice->tax_rate }}%)</span><span>{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</span></div>
            @endif
            <div class="t-grand"><span>Total</span><span class="amt">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</span></div>
        </div>
    </div>

    @if ($invoice->notes)
        <div class="notes"><strong>Notes</strong>{{ $invoice->notes }}</div>
    @endif
    @if ($invoice->terms)
        <div class="notes"><strong>Terms</strong>{{ $invoice->terms }}</div>
    @endif

    @if (! $invoice->isPaid())
        <a href="#" class="pay-btn">Pay {{ $invoice->currency }} {{ number_format($invoice->amount_outstanding, 2) }}</a>
    @endif

</div>

<div class="footer">Powered by Remindly</div>

</body>
</html>