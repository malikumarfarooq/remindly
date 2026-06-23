<div>

<style>
    .f-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}
    .f-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:16px}
    .f-field label{display:block;font-size:13px;font-weight:500;color:var(--text);margin-bottom:6px}
    .f-input{width:100%;padding:9px 14px;border:1px solid var(--gray-border);border-radius:var(--r);font-family:var(--font);font-size:13.5px;color:var(--text);outline:none;transition:border-color .15s;background:var(--surface)}
    .f-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-light)}
    .f-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235f6368' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;cursor:pointer}
    .f-error{color:var(--red);font-size:12px;margin-top:4px}
    .f-divider{font-size:12px;font-weight:500;color:var(--text2);letter-spacing:.4px;text-transform:uppercase;margin:22px 0 14px;padding-top:20px;border-top:1px solid var(--gray-border)}
    .f-toggle-row{display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--gray-light);border-radius:var(--r);margin-bottom:16px}
    .f-toggle-row label{font-size:13px;color:var(--text);margin:0}
</style>

@if (session()->has('success'))
    <div style="display:flex;align-items:center;gap:10px;padding:11px 16px;background:var(--green-light);color:var(--green);border-radius:var(--r);font-size:13.5px;margin-bottom:16px">
        <i class="ti ti-check"></i> {{ session('success') }}
    </div>
@endif

{{-- Basic Info --}}
<div class="f-row">
    <div class="f-field">
        <label>Client / Business Name *</label>
        <input class="f-input" placeholder="e.g. Acme Corp" wire:model="name">
        @error('name') <div class="f-error">{{ $message }}</div> @enderror
    </div>
    <div class="f-field">
        <label>Contact Person</label>
        <input class="f-input" placeholder="e.g. John Smith" wire:model="contactPerson">
    </div>
</div>

<div class="f-row">
    <div class="f-field">
        <label>Email</label>
        <input class="f-input" type="email" placeholder="client@example.com" wire:model="email">
        @error('email') <div class="f-error">{{ $message }}</div> @enderror
    </div>
    <div class="f-field">
        <label>Company</label>
        <input class="f-input" placeholder="e.g. Acme Corporation Ltd." wire:model="company">
    </div>
</div>

<div class="f-row">
    <div class="f-field">
        <label>Phone</label>
        <input class="f-input" placeholder="+1 555 000 0000" wire:model="phone">
    </div>
    <div class="f-field">
        <label>WhatsApp</label>
        <input class="f-input" placeholder="+1 555 000 0000" wire:model="whatsapp">
    </div>
</div>

{{-- Preferences --}}
<div class="f-divider">Preferences</div>

<div class="f-row">
    <div class="f-field">
        <label>Preferred Contact Channel</label>
        <select class="f-input f-select" wire:model="preferredChannel">
            <option value="auto">Auto (smart selection)</option>
            <option value="email">Email</option>
            <option value="sms">SMS</option>
            <option value="whatsapp">WhatsApp</option>
        </select>
    </div>
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

{{-- Address --}}
<div class="f-divider">Address</div>

<div class="f-field" style="margin-bottom:16px">
    <label>Street Address</label>
    <input class="f-input" placeholder="123 Main Street" wire:model="address">
</div>

<div class="f-row-3">
    <div class="f-field">
        <label>City</label>
        <input class="f-input" wire:model="city">
    </div>
    <div class="f-field">
        <label>State / Province</label>
        <input class="f-input" wire:model="state">
    </div>
    <div class="f-field">
        <label>Postal Code</label>
        <input class="f-input" wire:model="postalCode">
    </div>
</div>

<div class="f-row">
    <div class="f-field">
        <label>Country Code (2 letters)</label>
        <input class="f-input" placeholder="US, PK, GB..." maxlength="2" style="text-transform:uppercase" wire:model="country">
        @error('country') <div class="f-error">{{ $message }}</div> @enderror
    </div>
    <div class="f-field">
        <label>Tax Number</label>
        <input class="f-input" placeholder="VAT/GST/NTN number" wire:model="taxNumber">
    </div>
</div>

<div class="f-field" style="margin-bottom:16px">
    <label>Website</label>
    <input class="f-input" placeholder="https://example.com" wire:model="website">
</div>

{{-- Notes --}}
<div class="f-divider">Notes</div>
<div class="f-field" style="margin-bottom:16px">
    <textarea class="f-input" rows="3" placeholder="Internal notes about this client…" style="resize:vertical" wire:model="notes"></textarea>
</div>

<div class="f-toggle-row">
    <input type="checkbox" wire:model="isActive" id="isActive" style="width:16px;height:16px">
    <label for="isActive">Active client (uncheck to archive without deleting)</label>
</div>

{{-- Actions --}}
<div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid var(--gray-border)">
    <a href="{{ route('clients.index') }}" class="btn btn-outline">Cancel</a>
    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="save">
            <i class="ti ti-device-floppy"></i> {{ $clientId ? 'Update Client' : 'Create Client' }}
        </span>
        <span wire:loading wire:target="save">Saving…</span>
    </button>
</div>

</div>