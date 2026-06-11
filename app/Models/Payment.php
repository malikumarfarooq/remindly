<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_id',
        'client_id',
        'gateway',
        'gateway_transaction_id',
        'gateway_payment_method',
        'amount',
        'currency',
        'exchange_rate',
        'amount_in_base',
        'status',
        'paid_at',
        'notes',
        'refunded_amount',
        'is_partial',
    ];

    protected function casts(): array
    {
        return [
            'paid_at'        => 'datetime',
            'amount'         => 'decimal:2',
            'amount_in_base' => 'decimal:2',
            'exchange_rate'  => 'decimal:6',
            'refunded_amount' => 'decimal:2',
            'is_partial'     => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isSuccessful(): bool
    {
        return $this->status === 'paid';
    }
}
