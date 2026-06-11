<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'owner_user_id',
        'member_user_id',
        'role',
        'status',
        'invite_token',
        'invited_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'invited_at'  => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_user_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
