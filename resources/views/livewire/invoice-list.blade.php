<div>

    @if (session()->has('success'))
        <div style="display:flex;align-items:center;gap:10px;padding:11px 16px;background:var(--green-light);color:var(--green);border-radius:var(--r);font-size:13.5px;margin-bottom:16px">
            <i class="ti ti-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card">

        <div class="card-head" style="flex-wrap:wrap;gap:10px">
            <div class="search-bar">
                <i class="ti ti-search"></i>
                <input type="text" placeholder="Search invoices, clients..." wire:model.live.debounce.400ms="search">
            </div>

            <div class="filter-chips">
                <span class="chip {{ $statusFilter === 'all' ? 'on' : '' }}" wire:click="setFilter('all')">
                    All ({{ $counts['all'] }})
                </span>
                <span class="chip {{ $statusFilter === 'overdue' ? 'on' : '' }}" wire:click="setFilter('overdue')">
                    Overdue ({{ $counts['overdue'] }})
                </span>
                <span class="chip {{ $statusFilter === 'pending' ? 'on' : '' }}" wire:click="setFilter('pending')">
                    Pending ({{ $counts['pending'] }})
                </span>
                <span class="chip {{ $statusFilter === 'paid' ? 'on' : '' }}" wire:click="setFilter('paid')">
                    Paid ({{ $counts['paid'] }})
                </span>
            </div>

            <button class="btn btn-outline" style="font-size:12.5px;padding:6px 14px" wire:click="toggleTrashed">
                <i class="ti ti-{{ $showTrashed ? 'file-invoice' : 'trash' }}"></i>
                {{ $showTrashed ? 'Show Active' : 'Show Deleted' }}
            </button>
        </div>

        @if (count($selected) > 0)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 18px;background:var(--blue-light);border-bottom:1px solid var(--gray-border);font-size:13px">
                <span style="font-weight:500;color:var(--blue)">{{ count($selected) }} selected</span>
                <button class="btn btn-primary" style="font-size:12px;padding:5px 12px" wire:click="bulkMarkPaid" onclick="return confirm('Mark {{ count($selected) }} invoice(s) as paid?')">
                    <i class="ti ti-check"></i> Mark Paid
                </button>
                <button class="btn btn-outline" style="font-size:12px;padding:5px 12px" wire:click="bulkSendReminder">
                    <i class="ti ti-send"></i> Send Reminder
                </button>
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width:36px">
                        <input type="checkbox" wire:model.live="selectAll">
                    </th>
                    <th>Client</th>
                    <th>Invoice #</th>
                    <th>Amount</th>
                    <th>Issued</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
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
                        $initial = strtoupper(substr($invoice->client->name ?? '?', 0, 1));
                        $avClasses = ['av-b', 'av-g', 'av-r', 'av-o', 'av-p'];
                        $avClass = $avClasses[$invoice->client_id % count($avClasses)];
                    @endphp
                    <tr wire:key="invoice-{{ $invoice->id }}">
                        <td onclick="event.stopPropagation()">
                            <input type="checkbox" value="{{ $invoice->id }}" wire:model.live="selected">
                        </td>
                        <td onclick="window.location='{{ $showTrashed ? '#' : route('invoices.show', $invoice) }}'" style="cursor:{{ $showTrashed ? 'default' : 'pointer' }}">
                            <div class="client-cell">
                                <div class="av {{ $avClass }}">{{ $initial }}</div>
                                {{ $invoice->client->name ?? '—' }}
                            </div>
                        </td>
                        <td style="color:var(--text2)">#{{ $invoice->invoice_number }}</td>
                        <td style="font-weight:500">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
                        <td style="color:var(--text2)">{{ $invoice->issue_date->format('M j') }}</td>
                        <td style="color:var(--text2)">{{ $invoice->due_date->format('M j') }}</td>
                        <td>
                            <span class="pill {{ $pillClass }}"><span class="pill-dot"></span>{{ ucfirst($invoice->status) }}</span>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div style="display:flex;gap:8px">
                                @if ($showTrashed)
                                    <button class="btn btn-outline" style="padding:5px 12px;font-size:12px" wire:click="restoreInvoice({{ $invoice->id }})">
                                        <i class="ti ti-refresh"></i> Restore
                                    </button>
                                @else
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline" style="padding:5px 12px;font-size:12px">Edit</a>
                                    <button class="btn btn-outline" style="padding:5px 12px;font-size:12px;color:var(--red);border-color:var(--red-light)"
                                            wire:click="deleteInvoice({{ $invoice->id }})"
                                            onclick="return confirm('Delete this invoice?')">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">
                            @if ($showTrashed)
                                No deleted invoices.
                            @else
                                No invoices found. <a href="{{ route('invoices.create') }}">Create your first one.</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px">
        {{ $invoices->links() }}
    </div>

</div>