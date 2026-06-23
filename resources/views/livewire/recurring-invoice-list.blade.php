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
                <input type="text" placeholder="Search by title or client..." wire:model.live.debounce.400ms="search">
            </div>

            <div class="filter-chips">
                <span class="chip {{ $statusFilter === 'all' ? 'on' : '' }}" wire:click="setFilter('all')">
                    All ({{ $counts['all'] }})
                </span>
                <span class="chip {{ $statusFilter === 'active' ? 'on' : '' }}" wire:click="setFilter('active')">
                    Active ({{ $counts['active'] }})
                </span>
                <span class="chip {{ $statusFilter === 'paused' ? 'on' : '' }}" wire:click="setFilter('paused')">
                    Paused ({{ $counts['paused'] }})
                </span>
            </div>
        </div>

        @if (count($selected) > 0)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 18px;background:var(--blue-light);border-bottom:1px solid var(--gray-border);font-size:13px">
                <span style="font-weight:500;color:var(--blue)">{{ count($selected) }} selected</span>
                <button class="btn btn-outline" style="font-size:12px;padding:5px 12px" wire:click="bulkPause">
                    <i class="ti ti-player-pause"></i> Pause
                </button>
                <button class="btn btn-outline" style="font-size:12px;padding:5px 12px" wire:click="bulkResume">
                    <i class="ti ti-player-play"></i> Resume
                </button>
                <button class="btn btn-outline" style="font-size:12px;padding:5px 12px;color:var(--red);border-color:var(--red-light)"
                        wire:click="bulkDelete"
                        onclick="return confirm('Delete {{ count($selected) }} recurring invoice(s)? This cannot be undone.')">
                    <i class="ti ti-trash"></i> Delete
                </button>
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width:36px">
                        <input type="checkbox" wire:model.live="selectAll">
                    </th>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Frequency</th>
                    <th>Amount / Cycle</th>
                    <th>Next Invoice</th>
                    <th>Generated</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recurringInvoices as $recurring)
                    @php
                        $avClasses = ['av-b', 'av-g', 'av-r', 'av-o', 'av-p'];
                        $avClass = $avClasses[$recurring->client_id % count($avClasses)];
                    @endphp
                    <tr wire:key="recurring-{{ $recurring->id }}" style="cursor:default">
                        <td onclick="event.stopPropagation()">
                            <input type="checkbox" value="{{ $recurring->id }}" wire:model.live="selected">
                        </td>
                        <td style="font-weight:500">{{ $recurring->title }}</td>
                        <td>
                            <div class="client-cell">
                                <div class="av {{ $avClass }}">{{ strtoupper(substr($recurring->client->name ?? '?', 0, 1)) }}</div>
                                {{ $recurring->client->name ?? '—' }}
                            </div>
                        </td>
                        <td style="color:var(--text2);text-transform:capitalize">{{ $recurring->frequency }}</td>
                        <td style="font-weight:500">{{ $recurring->currency }} {{ number_format($recurring->total_amount, 2) }}</td>
                        <td style="color:var(--text2)">
                            {{ $recurring->next_generation_at?->format('M j, Y') ?? '—' }}
                        </td>
                        <td style="color:var(--text2)">{{ $recurring->invoices_generated }}x</td>
                        <td>
                            @if ($recurring->is_active)
                                <span class="pill pill-paid"><span class="pill-dot"></span>Active</span>
                            @else
                                <span class="pill pill-pending"><span class="pill-dot"></span>Paused</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:8px">
                                <a href="{{ route('recurring.edit', $recurring) }}" class="btn btn-outline" style="padding:5px 12px;font-size:12px">
                                    Edit
                                </a>
                                <button class="btn btn-outline" style="padding:5px 12px;font-size:12px"
                                        wire:click="togglePause({{ $recurring->id }})">
                                    <i class="ti ti-{{ $recurring->is_active ? 'player-pause' : 'player-play' }}"></i>
                                    {{ $recurring->is_active ? 'Pause' : 'Resume' }}
                                </button>
                                <button class="btn btn-outline" style="padding:5px 12px;font-size:12px;color:var(--red);border-color:var(--red-light)"
                                        wire:click="deleteRecurring({{ $recurring->id }})"
                                        onclick="return confirm('Delete this recurring invoice? This cannot be undone.')">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty">
                            No recurring invoices found. <a href="{{ route('recurring.create') }}">Set up your first one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($recurringInvoices->hasPages())
        <div style="margin-top:16px">
            {{ $recurringInvoices->links() }}
        </div>
    @endif

</div>