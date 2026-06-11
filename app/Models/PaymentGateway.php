<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'user_id',
        'gateway',
        'is_connected',
        'is_default',
        'credentials',
        'settings',
        'account_email',
        'account_id',
        'connected_at',
    ];

    protected function casts(): array
    {
        return [
            'credentials'  => 'encrypted:array',
            'settings'     => 'array',
            'is_connected' => 'boolean',
            'is_default'   => 'boolean',
            'connected_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
