<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageEvent extends Model
{
    protected $fillable = [
        'reminder_id',
        'user_id',
        'external_message_id',
        'gateway',
        'event_type',
        'recipient',
        'raw_payload',
        'user_agent',
        'ip_address',
        'url_clicked',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
