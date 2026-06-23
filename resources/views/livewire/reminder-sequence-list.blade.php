<div>

    @if (session()->has('success'))
        <div style="display:flex;align-items:center;gap:10px;padding:11px 16px;background:var(--green-light);color:var(--green);border-radius:var(--r);font-size:13.5px;margin-bottom:16px">
            <i class="ti ti-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card">

        <div class="card-head">
            <div class="search-bar">
                <i class="ti ti-search"></i>
                <input type="text" placeholder="Search sequences..." wire:model.live.debounce.400ms="search">
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Steps</th>
                    <th>Status</th>
                    <th>Default</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sequences as $sequence)
                    <tr wire:key="seq-{{ $sequence->id }}" style="cursor:default">
                        <td style="font-weight:500">{{ $sequence->name }}</td>
                        <td style="color:var(--text2)">
                            {{ $sequence->steps_count }} step{{ $sequence->steps_count !== 1 ? 's' : '' }}
                        </td>
                        <td>
                            @if ($sequence->is_active)
                                <span class="pill pill-paid"><span class="pill-dot"></span>Active</span>
                            @else
                                <span class="pill pill-pending"><span class="pill-dot"></span>Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if ($sequence->is_default)
                                <span class="pill pill-sent"><span class="pill-dot"></span>Default</span>
                            @else
                                <button class="btn btn-outline" style="padding:4px 10px;font-size:12px"
                                        wire:click="setDefault({{ $sequence->id }})">
                                    Set Default
                                </button>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:8px">
                                <a href="{{ route('reminders.edit', $sequence) }}"
                                   class="btn btn-outline" style="padding:5px 12px;font-size:12px">
                                    Edit
                                </a>
                                <button class="btn btn-outline" style="padding:5px 12px;font-size:12px"
                                        wire:click="toggleActive({{ $sequence->id }})">
                                    {{ $sequence->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button class="btn btn-outline"
                                        style="padding:5px 12px;font-size:12px;color:var(--red);border-color:var(--red-light)"
                                        wire:click="deleteSequence({{ $sequence->id }})"
                                        onclick="return confirm('Delete this sequence?')">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">
                            No sequences yet. <a href="{{ route('reminders.create') }}">Create your first one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($sequences->hasPages())
            <div style="margin-top:16px;padding:0 18px 16px">
                {{ $sequences->links() }}
            </div>
        @endif

    </div>

</div>