<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDelivery extends Model
{
    protected $fillable = [
        'webhook_endpoint_id',
        'user_id',
        'event',
        'payload',
        'http_status',
        'response_body',
        'was_successful',
        'attempts',
        'next_retry_at',
        'duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'payload'       => 'array',
            'was_successful' => 'boolean',
            'next_retry_at' => 'datetime',
            'duration_ms'   => 'decimal:2',
        ];
    }

    public function endpoint()
    {
        return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
