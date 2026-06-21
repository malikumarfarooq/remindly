@extends('layouts.app')
@section('title','Dashboard')
@section('page-title', 'Good morning, ' . explode(' ', auth()->user()->name)[0] . ' 👋')

@section('topbar-actions')
    <button class="btn btn-outline"><i class="ti ti-download"></i> Export</button>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> New Invoice</a>
@endsection

@section('content')

{{-- STAT CARDS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-top">
            <div>
                <div class="stat-label">Total Outstanding</div>
                <div class="stat-value">${{ number_format($stats['outstanding'],0) }}</div>
            </div>
            <div class="stat-icon si-blue"><i class="ti ti-receipt"></i></div>
        </div>
        <div class="stat-sub sub-red"><i class="ti ti-trending-up"></i> {{ $stats['overdue_count'] }} invoices overdue</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div>
                <div class="stat-label">Collected This Month</div>
                <div class="stat-value">${{ number_format($stats['paid_month'],0) }}</div>
            </div>
            <div class="stat-icon si-green"><i class="ti ti-check"></i></div>
        </div>
        <div class="stat-sub sub-green"><i class="ti ti-trending-up"></i> {{ $stats['paid_count'] }} payments received</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div>
                <div class="stat-label">Overdue Invoices</div>
                <div class="stat-value">{{ $stats['overdue_count'] }}</div>
            </div>
            <div class="stat-icon si-red"><i class="ti ti-alert-circle"></i></div>
        </div>
        <div class="stat-sub sub-red"><i class="ti ti-clock"></i>
            @if($stats['oldest_overdue']) Oldest: {{ $stats['oldest_overdue'] }}d @else No overdue @endif
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div>
                <div class="stat-label">AI Recovery Rate</div>
                <div class="stat-value">{{ $stats['recovery_rate'] }}%</div>
            </div>
            <div class="stat-icon si-yellow"><i class="ti ti-robot"></i></div>
        </div>
        <div class="stat-sub sub-gray"><i class="ti ti-sparkles"></i> AI reminders sent: {{ $stats['reminders_sent'] }}</div>
    </div>
</div>

{{-- MAIN GRID --}}
<div class="dash-grid">

    {{-- INVOICE TABLE --}}
    <div class="card">
        <div class="card-head">
            <div class="search-bar">
                <i class="ti ti-search"></i>
                <input type="text" placeholder="Search invoices…">
            </div>
            <div class="filter-chips">
                <div class="chip on">All</div>
                <div class="chip">Overdue</div>
                <div class="chip">Paid</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Invoice</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>AI Risk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentInvoices as $inv)
                <tr>
                    <td>
                        <div class="client-cell">
                            @php
                                $colors = ['av-r','av-b','av-g','av-o','av-p'];
                                $ci = $loop->index % 5;
                                $letter = strtoupper(substr($inv->client->name ?? 'U', 0, 1));
                            @endphp
                            <div class="av {{ $colors[$ci] }}">{{ $letter }}</div>
                            {{ $inv->client->name ?? '—' }}
                        </div>
                    </td>
                    <td style="color:var(--text2)">#{{ $inv->invoice_number }}</td>
                    <td style="font-weight:500">${{ number_format($inv->total_amount,0) }}</td>
                    <td style="color:{{ $inv->due_date && $inv->due_date->isPast() && $inv->status !== 'paid' ? 'var(--red)' : 'var(--text2)' }}">
                        {{ $inv->due_date ? $inv->due_date->format('M j') : '—' }}
                    </td>
                    <td>
                        @php
                            $pillMap = ['paid'=>'pill-paid','overdue'=>'pill-overdue','draft'=>'pill-pending','sent'=>'pill-sent','partial'=>'pill-pending'];
                            $pillClass = $pillMap[$inv->status] ?? 'pill-pending';
                        @endphp
                        <span class="pill {{ $pillClass }}">
                            <span class="pill-dot"></span>
                            {{ ucfirst($inv->status) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $risk = $inv->ai_risk_score ?? 50;
                            $rClass = $risk >= 70 ? 'rf-low' : ($risk >= 40 ? 'rf-mid' : 'rf-high');
                            $rColor = $risk >= 70 ? 'var(--green)' : ($risk >= 40 ? '#b06000' : 'var(--red)');
                            $rLabel = $risk >= 70 ? 'Low risk' : ($risk >= 40 ? 'Medium' : 'High risk');
                        @endphp
                        <div class="risk-bar">
                            <div class="risk-track"><div class="risk-fill {{ $rClass }}" style="width:{{ $risk }}%"></div></div>
                            <span style="font-size:12px;color:{{ $rColor }}">{{ $rLabel }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty">
                        No invoices yet. <a href="{{ route('invoices.create') }}">Create your first invoice →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- RIGHT PANEL --}}
    <div>
        {{-- AI INSIGHTS --}}
        <div class="card">
            <div class="card-head">
                <i class="ti ti-sparkles" style="color:var(--blue);font-size:17px"></i>
                <span class="card-title">AI Insights</span>
                <span class="ai-badge" style="margin-left:auto">Live</span>
            </div>
            <div style="padding:14px 16px">
                @if(isset($aiInsights) && $aiInsights->count())
                    @foreach($aiInsights as $ins)
                    <div class="insight">
                        <div class="i-dot {{ $ins->severity === 'critical' ? 'dot-red' : ($ins->severity === 'warning' ? 'dot-yellow' : 'dot-blue') }}"></div>
                        <div>
                            <div class="i-text">{{ $ins->message }}</div>
                            @if($ins->action_label)
                                <div class="i-action">→ {{ $ins->action_label }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="insight">
                        <div class="i-dot dot-green"></div>
                        <div><div class="i-text">AI is monitoring your invoices. Insights will appear here as data comes in.</div></div>
                    </div>
                    <div class="insight">
                        <div class="i-dot dot-yellow"></div>
                        <div><div class="i-text">Send your first invoice to activate AI payment predictions.</div></div>
                    </div>
                    <div class="insight">
                        <div class="i-dot dot-blue"></div>
                        <div><div class="i-text">Connect WhatsApp to enable multi-channel reminders.</div></div>
                    </div>
                @endif
            </div>
        </div>

        {{-- AUTO REMINDER FLOW --}}
        <div class="flow-card">
            <div class="card-head" style="padding:12px 14px">
                <i class="ti ti-mail-forward" style="color:var(--blue);font-size:16px"></i>
                <span class="card-title" style="font-size:13px">Auto-reminder flow</span>
            </div>
            <div class="r-item">
                <div class="r-step" style="background:#d1fae5;color:#059669">1</div>
                <div>
                    <div class="r-title">Pre-due (3 days before)</div>
                    <div class="r-sub">Friendly tone · Automated</div>
                    <div class="r-ai">AI written</div>
                </div>
            </div>
            <div class="r-item">
                <div class="r-step">2</div>
                <div>
                    <div class="r-title">Due date reminder</div>
                    <div class="r-sub">Professional tone · 10 AM</div>
                    <div class="r-ai">AI written</div>
                </div>
            </div>
            <div class="r-item">
                <div class="r-step" style="background:#fef7e0;color:#b06000">3</div>
                <div>
                    <div class="r-title">+7 days overdue</div>
                    <div class="r-sub">Firm tone · Urgent subject line</div>
                    <div class="r-ai">AI written</div>
                </div>
            </div>
            <div class="r-item">
                <div class="r-step" style="background:#fce8e6;color:#ea4335">4</div>
                <div>
                    <div class="r-title">+21 days · Final notice</div>
                    <div class="r-sub">Final warning · CC escalation</div>
                    <div class="r-ai">AI written</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection