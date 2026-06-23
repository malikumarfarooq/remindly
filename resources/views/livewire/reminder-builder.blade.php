<div>

<style>
    .f-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}
    .f-field label{display:block;font-size:13px;font-weight:500;color:var(--text);margin-bottom:6px}
    .f-input{width:100%;padding:9px 14px;border:1px solid var(--gray-border);border-radius:var(--r);font-family:var(--font);font-size:13.5px;color:var(--text);outline:none;transition:border-color .15s;background:var(--surface)}
    .f-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-light)}
    .f-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235f6368' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;cursor:pointer}
    .f-divider{font-size:12px;font-weight:500;color:var(--text2);letter-spacing:.4px;text-transform:uppercase;margin:22px 0 14px;padding-top:20px;border-top:1px solid var(--gray-border)}
    .f-error{color:var(--red);font-size:12px;margin-top:4px}
    .f-toggle-row{display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--gray-light);border-radius:var(--r);margin-bottom:12px}
    .f-toggle-row label{font-size:13px;color:var(--text);margin:0}
    .f-textarea{width:100%;padding:9px 14px;border:1px solid var(--gray-border);border-radius:var(--r);font-family:var(--font);font-size:13px;color:var(--text);outline:none;resize:vertical;background:var(--surface)}
    .f-textarea:focus{border-color:var(--blue)}

    .step-card{border:1px solid var(--gray-border);border-radius:var(--r);margin-bottom:12px;overflow:hidden}
    .step-header{display:flex;align-items:center;gap:12px;padding:10px 14px;background:var(--gray-light);border-bottom:1px solid var(--gray-border)}
    .step-num{width:24px;height:24px;border-radius:50%;background:var(--blue);color:#fff;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .step-label-txt{font-size:13px;font-weight:500;color:var(--text);flex:1}
    .step-body{padding:14px}
    .step-grid{display:grid;grid-template-columns:2fr 80px 1fr 1fr 1fr;gap:10px;margin-bottom:12px;align-items:end}
    .step-grid label{display:block;font-size:12px;font-weight:500;color:var(--text2);margin-bottom:5px}
    .icon-btn{width:28px;height:28px;border-radius:6px;border:1px solid var(--gray-border);background:var(--surface);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text2);font-size:14px}
    .icon-btn:hover{background:var(--gray-light)}
    .icon-btn.del:hover{background:var(--red-light);color:var(--red);border-color:var(--red-light)}

    .tone-badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600}
    .tone-friendly{background:#e6f4ea;color:#1e7e34}
    .tone-professional{background:#e8f0fe;color:#1a73e8}
    .tone-firm{background:#fef7e0;color:#b06000}
    .tone-final{background:#fce8e6;color:#ea4335}
    .tone-demand{background:#f3e8fd;color:#7b1fa2}
</style>

@if (session()->has('success'))
    <div style="display:flex;align-items:center;gap:10px;padding:11px 16px;background:var(--green-light);color:var(--green);border-radius:var(--r);font-size:13.5px;margin-bottom:16px">
        <i class="ti ti-check"></i> {{ session('success') }}
    </div>
@endif

{{-- Sequence Name --}}
<div class="f-row">
    <div class="f-field">
        <label>Sequence Name *</label>
        <input class="f-input" placeholder="e.g. Standard 3-Step Reminder" wire:model="name">
        @error('name') <div class="f-error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="f-toggle-row">
    <input type="checkbox" wire:model="isDefault" id="isDefault" style="width:16px;height:16px">
    <label for="isDefault">Set as default sequence (auto-applied to new invoices)</label>
</div>

<div class="f-toggle-row">
    <input type="checkbox" wire:model="isActive" id="isActive" style="width:16px;height:16px">
    <label for="isActive">Active (inactive sequences are skipped during dispatch)</label>
</div>

{{-- Steps --}}
<div class="f-divider">Reminder Steps</div>

@foreach ($steps as $i => $step)
    <div class="step-card" wire:key="step-{{ $i }}">

        <div class="step-header">
            <div class="step-num">{{ $i + 1 }}</div>
            <div class="step-label-txt">{{ $step['label'] ?: 'Step ' . ($i + 1) }}</div>
            <span class="tone-badge tone-{{ $step['tone'] }}">{{ ucfirst($step['tone']) }}</span>
            <div style="display:flex;gap:6px">
                @if ($i > 0)
                    <button class="icon-btn" type="button" wire:click="moveUp({{ $i }})" title="Move up">
                        <i class="ti ti-arrow-up"></i>
                    </button>
                @endif
                @if ($i < count($steps) - 1)
                    <button class="icon-btn" type="button" wire:click="moveDown({{ $i }})" title="Move down">
                        <i class="ti ti-arrow-down"></i>
                    </button>
                @endif
                @if (count($steps) > 1)
                    <button class="icon-btn del" type="button" wire:click="removeStep({{ $i }})" title="Remove">
                        <i class="ti ti-trash"></i>
                    </button>
                @endif
            </div>
        </div>

        <div class="step-body">

            <div class="step-grid">
                <div>
                    <label>Step Label</label>
                    <input class="f-input" placeholder="e.g. 3 Days Before Due"
                           wire:model.live="steps.{{ $i }}.label">
                </div>
                <div>
                    <label>Days</label>
                    <input class="f-input" type="number"
                           wire:model="steps.{{ $i }}.offset_days"
                           title="Negative = before due, Positive = after due">
                </div>
                <div>
                    <label>Relative To</label>
                    <select class="f-input f-select" wire:model="steps.{{ $i }}.offset_from">
                        <option value="due_date">Due Date</option>
                        <option value="issue_date">Issue Date</option>
                    </select>
                </div>
                <div>
                    <label>Tone</label>
                    <select class="f-input f-select" wire:model.live="steps.{{ $i }}.tone">
                        <option value="friendly">Friendly</option>
                        <option value="professional">Professional</option>
                        <option value="firm">Firm</option>
                        <option value="final">Final Notice</option>
                        <option value="demand">Legal Demand</option>
                    </select>
                </div>
                <div>
                    <label>Channel</label>
                    <select class="f-input f-select" wire:model="steps.{{ $i }}.channel">
                        <option value="auto">Auto (smart)</option>
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                </div>
            </div>

            <div class="f-toggle-row" style="margin-bottom:10px">
                <input type="checkbox" wire:model.live="steps.{{ $i }}.ai_generate"
                       id="ai-{{ $i }}" style="width:15px;height:15px">
                <label for="ai-{{ $i }}" style="font-size:13px">
                    AI-generate message at send time
                    <span style="color:var(--text2)">(uses tone + invoice data — Day 18 feature)</span>
                </label>
            </div>

            @if (! $steps[$i]['ai_generate'])
                <div style="margin-bottom:10px">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--text2);margin-bottom:5px">
                        Subject Template
                        <span style="font-weight:400">
                            — variables: <code>&#123;&#123;client_name&#125;&#125;</code>
                            <code>&#123;&#123;invoice_number&#125;&#125;</code>
                            <code>&#123;&#123;amount&#125;&#125;</code>
                            <code>&#123;&#123;due_date&#125;&#125;</code>
                        </span>
                    </label>
                    <input class="f-input"
                           placeholder="Payment Reminder — Invoice {{invoice_number}}"
                           wire:model="steps.{{ $i }}.subject_template">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--text2);margin-bottom:5px">
                        Body Template
                    </label>
                    <textarea class="f-textarea" rows="5"
                              placeholder="Hi {{client_name}},&#10;&#10;Invoice {{invoice_number}} for {{amount}} is due on {{due_date}}.&#10;&#10;Please arrange payment at your earliest convenience.&#10;&#10;Thank you."
                              wire:model="steps.{{ $i }}.body_template"></textarea>
                </div>
            @else
                <div style="padding:10px 14px;background:var(--blue-light);border-radius:6px;font-size:13px;color:var(--blue)">
                    <i class="ti ti-sparkles"></i>
                    AI will compose a <strong>{{ $steps[$i]['tone'] }}</strong> message using client name, invoice number, amount, and due date at send time.
                </div>
            @endif

        </div>
    </div>
@endforeach

<button class="btn btn-outline" type="button" wire:click="addStep" style="margin-bottom:24px">
    <i class="ti ti-plus"></i> Add Step
</button>

@error('steps') <div class="f-error" style="margin-bottom:12px">{{ $message }}</div> @enderror

{{-- Actions --}}
<div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--gray-border)">
    <a href="{{ route('reminders.index') }}" class="btn btn-outline">Cancel</a>
    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="save">
            <i class="ti ti-device-floppy"></i>
            {{ $sequenceId ? 'Update Sequence' : 'Create Sequence' }}
        </span>
        <span wire:loading wire:target="save">Saving…</span>
    </button>
</div>

</div>