<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'template_id',
        'recurring_invoice_id',
        'invoice_number',
        'status',
        'issue_date',
        'due_date',
        'paid_at',
        'viewed_at',
        'currency',
        'exchange_rate',
        'subtotal',
        'tax_name',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'amount_outstanding',
        'days_overdue',
        'notes',
        'terms',
        'payment_link',
        'payment_link_token',
        'payment_link_views',
        'ai_risk_score',
        'ai_escalated',
        'ai_recommended_tone',
        'ai_risk_assessed_at',
        'reminder_step',
        'last_reminder_sent_at',
        'next_reminder_at',
        'reminder_paused',
    ];

    protected function casts(): array
    {
        return [
            'issue_date'            => 'date',
            'due_date'              => 'date',
            'paid_at'               => 'datetime',
            'viewed_at'             => 'datetime',
            'last_reminder_sent_at' => 'datetime',
            'next_reminder_at'      => 'datetime',
            'ai_risk_assessed_at'   => 'datetime',
            'ai_escalated'          => 'boolean',
            'reminder_paused'       => 'boolean',
            'subtotal'              => 'decimal:2',
            'tax_rate'              => 'decimal:2',
            'tax_amount'            => 'decimal:2',
            'discount_value'        => 'decimal:2',
            'discount_amount'       => 'decimal:2',
            'total_amount'          => 'decimal:2',
            'amount_paid'           => 'decimal:2',
            'amount_outstanding'    => 'decimal:2',
            'ai_risk_score'         => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function template()
    {
        return $this->belongsTo(InvoiceTemplate::class, 'template_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->due_date < now() && !in_array($this->status, ['paid', 'cancelled']);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function hasOutstanding(): bool
    {
        return $this->amount_outstanding > 0;
    }
}
