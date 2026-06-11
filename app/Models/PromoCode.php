<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'max_uses',
        'used_count',
        'plan_id',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value'       => 'decimal:2',
            'valid_from'  => 'datetime',
            'valid_until' => 'datetime',
            'is_active'   => 'boolean',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        if ($this->valid_until && now()->gt($this->valid_until)) return false;
        return true;
    }
}
