<div>

<style>
    .f-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}
    .f-field label{display:block;font-size:13px;font-weight:500;color:var(--text);margin-bottom:6px}
    .f-input{width:100%;padding:9px 14px;border:1px solid var(--gray-border);border-radius:var(--r);font-family:var(--font);font-size:13.5px;color:var(--text);outline:none;transition:border-color .15s;background:var(--surface)}
    .f-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-light)}
    .f-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235f6368' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;cursor:pointer}
    .f-error{color:var(--red);font-size:12px;margin-top:4px}
    .f-divider{font-size:12px;font-weight:500;color:var(--text2);letter-spacing:.4px;text-transform:uppercase;margin:22px 0 14px;padding-top:20px;border-top:1px solid var(--gray-border)}
    .f-line-items{border:1px solid var(--gray-border);border-radius:var(--r);overflow:hidden;margin-bottom:14px}
    .f-line-head{display:grid;grid-template-columns:2fr 80px 110px 36px;gap:8px;padding:9px 14px;background:var(--gray-light);border-bottom:1px solid var(--gray-border);font-size:12px;font-weight:500;color:var(--text2)}
    .f-line-row{display:grid;grid-template-columns:2fr 80px 110px 36px;gap:8px;padding:8px 14px;border-bottom:1px solid var(--gray-border);align-items:center}
    .f-line-row:last-child{border-bottom:none}
    .f-line-input{width:100%;border:1px solid var(--gray-border);border-radius:6px;padding:7px 10px;font-family:var(--font);font-size:13px;color:var(--text);outline:none;transition:border-color .15s}
    .f-line-input:focus{border-color:var(--blue)}
    .f-line-del{width:28px;height:28px;border-radius:50%;border:none;background:transparent;cursor:pointer;color:var(--text2);display:flex;align-items:center;justify-content:center;font-size:15px;transition:background .15s,color .15s}
    .f-line-del:hover{background:var(--red-light);color:var(--red)}
    .f-add-line{font-size:13px;color:var(--blue);background:none;border:none;cursor:pointer;padding:8px 14px;font-family:var(--font);font-weight:500;display:flex;align-items:center;gap:6px}
    .f-add-line:hover{text-decoration:underline}
    .f-total-wrap{display:flex;justify-content:flex-end;margin-top:14px}
    .f-total-box{background:var(--blue-light);border:1px solid #c5cae9;border-radius:var(--r);padding:12px 20px;text-align:right;min-width:200px}
    .f-total-label{font-size:12px;color:var(--blue);margin-bottom:4px}
    .f-total-amount{font-size:24px;font-weight:500;color:var(--blue);letter-spacing:-0.5px}
    .f-toggle-row{display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--gray-light);border-radius:var(--r);margin-bottom:16px}
    .f-toggle-row label{font-size:13px;color:var(--text);margin:0}
</style>

@if (session()->has('success'))
    <div style="display:flex;align-items:center;gap:10px;padding:11px 16px;background:var(--green-light);color:var(--green);border-radius:var(--r);font-size:13.5px;margin-bottom:16px">
        <i class="ti ti-check"></i> {{ session('success') }}
    </div>
@endif

{{-- Title / Client --}}
<div class="f-row">
    <div class="f-field">
        <label>Title *</label>
        <input class="f-input" placeholder="e.g. Monthly Retainer" wire:model="title">
        @error('title') <div class="f-error">{{ $message }}</div> @enderror
    </div>
    <div class="f-field">
        <label>Client *</label>
        <select class="f-input f-select" wire:model="clientId">
            <option value="">Select client…</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
        </select>
        @error('clientId') <div class="f-error">{{ $message }}</div> @enderror
    </div>
</div>

{{-- Frequency / Payment due days --}}
<div class="f-row">
    <div class="f-field">
        <label>Frequency *</label>
        <select class="f-input f-select" wire:model="frequency">
            <option value="weekly">Weekly</option>
            <option value="biweekly">Every 2 Weeks</option>
            <option value="monthly">Monthly</option>
            <option value="quarterly">Quarterly</option>
            <option value="yearly">Yearly</option>
        </select>
    </div>
    <div class="f-field">
        <label>Payment Due (days after issue)</label>
        <input class="f-input" type="number" min="0" max="365" wire:model="paymentDueDays">
        @error('paymentDueDays') <div class="f-error">{{ $message }}</div> @enderror
    </div>
</div>

{{-- Start / End dates --}}
<div class="f-row">
    <div class="f-field">
        <label>Start Date *</label>
        <input class="f-input" type="date" wire:model="startDate">
        @error('startDate') <div class="f-error">{{ $message }}</div> @enderror
    </div>
    <div class="f-field">
        <label>End Date (optional)</label>
        <input class="f-input" type="date" wire:model="endDate">
        @error('endDate') <div class="f-error">{{ $message }}</div> @enderror
        <div style="font-size:11.5px;color:var(--text2);margin-top:4px">Leave empty to run indefinitely.</div>
    </div>
</div>

{{-- Line Items --}}
<div class="f-divider">Line Items</div>
<div class="f-line-items">
    <div class="f-line-head">
        <span>Description</span><span>Qty</span><span>Price ({{ $currency }})</span><span></span>
    </div>
    @foreach ($items as $i => $item)
        <div class="f-line-row" wire:key="item-{{ $i }}">
            <input class="f-line-input" placeholder="Service or product…" wire:model.live="items.{{ $i }}.description">
            <input class="f-line-input" type="number" min="0.01" step="0.01" wire:model.live="items.{{ $i }}.quantity">
            <input class="f-line-input" type="number" min="0" step="0.01" placeholder="0.00" wire:model.live="items.{{ $i }}.unit_price">
            @if (count($items) > 1)
                <button class="f-line-del" type="button" wire:click="removeItem({{ $i }})">
                    <i class="ti ti-trash"></i>
                </button>
            @else
                <span></span>
            @endif
        </div>
        @error("items.$i.description")
            <div class="f-error" style="padding:0 14px 8px">{{ $message }}</div>
        @enderror
    @endforeach
</div>

<button class="f-add-line" type="button" wire:click="addItem">
    <i class="ti ti-plus"></i> Add line item
</button>

<div class="f-total-wrap">
    <div class="f-total-box">
        <div class="f-total-label">TOTAL PER CYCLE</div>
        <div class="f-total-amount">{{ $currency }} {{ number_format($total, 2) }}</div>
    </div>
</div>

{{-- Options --}}
<div class="f-divider">Options</div>

<div class="f-row">
    <div class="f-field">
        <label>Currency</label>
        <select class="f-input f-select" wire:model="currency">
            <option value="USD">USD — US Dollar</option>
            <option value="PKR">PKR — Pakistani Rupee</option>
            <option value="EUR">EUR — Euro</option>
            <option value="GBP">GBP — British Pound</option>
            <option value="AED">AED — UAE Dirham</option>
        </select>
    </div>
</div>

<div class="f-toggle-row">
    <input type="checkbox" wire:model="autoSend" id="autoSend" style="width:16px;height:16px">
    <label for="autoSend">Automatically mark generated invoices as "Sent" (otherwise saved as Draft)</label>
</div>

{{-- Actions --}}
<div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid var(--gray-border)">
    <a href="{{ route('recurring.index') }}" class="btn btn-outline">Cancel</a>
    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="save">
            <i class="ti ti-device-floppy"></i> {{ $recurringId ? 'Update Recurring Invoice' : 'Create Recurring Invoice' }}
        </span>
        <span wire:loading wire:target="save">Saving…</span>
    </button>
</div>

</div>