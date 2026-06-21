<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'title',
        'frequency',
        'start_date',
        'end_date',
        'payment_due_days',
        'line_items',
        'total_amount',
        'currency',
        'auto_send',
        'is_active',
        'last_generated_at',
        'next_generation_at',
        'invoices_generated',
    ];

    protected function casts(): array
    {
        return [
            'start_date'         => 'date',
            'end_date'           => 'date',
            'last_generated_at'  => 'date',
            'next_generation_at' => 'date',
            'line_items'         => 'array',
            'total_amount'       => 'decimal:2',
            'auto_send'          => 'boolean',
            'is_active'          => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'recurring_invoice_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function calculateNextDate(): \Illuminate\Support\Carbon
    {
        $from = $this->next_generation_at ?? $this->start_date;

        return match ($this->frequency) {
            'weekly'    => $from->copy()->addWeek(),
            'biweekly'  => $from->copy()->addWeeks(2),
            'monthly'   => $from->copy()->addMonth(),
            'quarterly' => $from->copy()->addMonths(3),
            'yearly'    => $from->copy()->addYear(),
        };
    }

    public function hasEnded(): bool
    {
        return $this->end_date !== null && now()->startOfDay()->gt($this->end_date);
    }

    public function isDue(): bool
    {
        return $this->is_active
            && ! $this->hasEnded()
            && $this->next_generation_at !== null
            && $this->next_generation_at->lte(now()->startOfDay());
    }
}
