<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'secret',
        'events',
        'is_active',
        'description',
        'success_count',
        'failure_count',
        'last_triggered_at',
    ];

    protected function casts(): array
    {
        return [
            'events'           => 'array',
            'is_active'        => 'boolean',
            'last_triggered_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries()
    {
        return $this->hasMany(WebhookDelivery::class);
    }
}
