<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationConnection extends Model
{
    protected $fillable = [
        'user_id',
        'integration',
        'is_active',
        'credentials',
        'settings',
        'workspace_name',
        'connected_at',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'credentials'   => 'encrypted:array',
            'settings'      => 'array',
            'is_active'     => 'boolean',
            'connected_at'  => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
