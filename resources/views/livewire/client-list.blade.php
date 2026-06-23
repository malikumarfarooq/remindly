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
                <input type="text" placeholder="Search by name, email, company..." wire:model.live.debounce.400ms="search">
            </div>

            <div class="filter-chips">
                <span class="chip {{ $riskFilter === 'all' ? 'on' : '' }}" wire:click="setFilter('all')">
                    All ({{ $counts['all'] }})
                </span>
                <span class="chip {{ $riskFilter === 'low' ? 'on' : '' }}" wire:click="setFilter('low')">
                    Low Risk ({{ $counts['low'] }})
                </span>
                <span class="chip {{ $riskFilter === 'medium' ? 'on' : '' }}" wire:click="setFilter('medium')">
                    Medium Risk ({{ $counts['medium'] }})
                </span>
                <span class="chip {{ $riskFilter === 'high' ? 'on' : '' }}" wire:click="setFilter('high')">
                    High Risk ({{ $counts['high'] }})
                </span>
            </div>
        </div>

        @if (count($selected) > 0)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 18px;background:var(--blue-light);border-bottom:1px solid var(--gray-border);font-size:13px">
                <span style="font-weight:500;color:var(--blue)">{{ count($selected) }} selected</span>
                <button class="btn btn-outline" style="font-size:12px;padding:5px 12px;color:var(--red);border-color:var(--red-light)"
                        wire:click="bulkDelete"
                        onclick="return confirm('Delete {{ count($selected) }} client(s)? This cannot be undone.')">
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
                    <th>Client</th>
                    <th>Email</th>
                    <th>Outstanding</th>
                    <th>Total Invoiced</th>
                    <th>Risk</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $client)
                    @php
                        $avClasses = ['av-b', 'av-g', 'av-r', 'av-o', 'av-p'];
                        $avClass = $avClasses[$client->id % count($avClasses)];
                        $riskPillMap = ['low' => 'pill-paid', 'medium' => 'pill-pending', 'high' => 'pill-overdue'];
                        $riskClass = $riskPillMap[$client->risk_level] ?? 'pill-pending';
                    @endphp
                    <tr wire:key="client-{{ $client->id }}">
                        <td onclick="event.stopPropagation()">
                            <input type="checkbox" value="{{ $client->id }}" wire:model.live="selected">
                        </td>
                        <td onclick="window.location='{{ route('clients.show', $client) }}'" style="cursor:pointer">
                            <div class="client-cell">
                                <div class="av {{ $avClass }}">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
                                {{ $client->name }}
                            </div>
                        </td>
                        <td style="color:var(--text2)">{{ $client->email ?? '—' }}</td>
                        <td style="font-weight:500;color:{{ $client->total_outstanding > 0 ? 'var(--red)' : 'var(--text)' }}">
                            {{ $client->currency }} {{ number_format($client->total_outstanding, 2) }}
                        </td>
                        <td>{{ $client->currency }} {{ number_format($client->total_invoiced, 2) }}</td>
                        <td>
                            <span class="pill {{ $riskClass }}"><span class="pill-dot"></span>{{ ucfirst($client->risk_level) }}</span>
                        </td>
                        <td>
                            @if ($client->is_active)
                                <span class="pill pill-paid"><span class="pill-dot"></span>Active</span>
                            @else
                                <span class="pill pill-pending"><span class="pill-dot"></span>Archived</span>
                            @endif
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div style="display:flex;gap:8px">
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline" style="padding:5px 12px;font-size:12px">Edit</a>
                                <button class="btn btn-outline" style="padding:5px 12px;font-size:12px;color:var(--red);border-color:var(--red-light)"
                                        wire:click="deleteClient({{ $client->id }})"
                                        onclick="return confirm('Delete this client? Their invoices will remain but lose the client link.')">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">
                            No clients found. <a href="{{ route('clients.create') }}">Add your first one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($clients->hasPages())
            <div style="margin-top:16px;padding:0 18px 16px">
                {{ $clients->links() }}
            </div>
        @endif

    </div>

</div>