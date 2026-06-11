<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'client_id',
        'user_id',
        'reminder_step_id',
        'step_number',
        'channel',
        'tone',
        'subject',
        'body',
        'recipient',
        'ai_generated',
        'ai_model',
        'status',
        'scheduled_at',
        'sent_at',
        'is_opened',
        'opened_at',
        'open_count',
        'is_clicked',
        'clicked_at',
        'external_message_id',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at'  => 'datetime',
            'sent_at'       => 'datetime',
            'opened_at'     => 'datetime',
            'clicked_at'    => 'datetime',
            'ai_generated'  => 'boolean',
            'is_opened'     => 'boolean',
            'is_clicked'    => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reminderStep()
    {
        return $this->belongsTo(ReminderStep::class);
    }

    public function messageEvents()
    {
        return $this->hasMany(MessageEvent::class);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
