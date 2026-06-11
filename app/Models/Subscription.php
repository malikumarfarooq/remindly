<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'billing_cycle',
        'amount',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'stripe_subscription_id',
        'stripe_status',
        'promo_code_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
            'trial_ends_at' => 'datetime',
            'cancelled_at'  => 'datetime',
            'amount'        => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }
}
