<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiInsight extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'severity',
        'message',
        'action_label',
        'action_url',
        'is_dismissed',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_dismissed' => 'boolean',
            'expires_at'   => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function insightable()
    {
        return $this->morphTo();
    }

    // ── Helpers ────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    public function dismiss(): void
    {
        $this->update(['is_dismissed' => true]);
    }
}
