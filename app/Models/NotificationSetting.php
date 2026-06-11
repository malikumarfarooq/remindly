<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_reminders',
        'whatsapp_reminders',
        'sms_fallback',
        'ai_tone_selection',
        'optimal_send_time',
        'auto_escalation',
        'default_send_time',
        'default_send_day',
        'default_language',
        'default_sequence_id',
    ];

    protected function casts(): array
    {
        return [
            'email_reminders'    => 'boolean',
            'whatsapp_reminders' => 'boolean',
            'sms_fallback'       => 'boolean',
            'ai_tone_selection'  => 'boolean',
            'optimal_send_time'  => 'boolean',
            'auto_escalation'    => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function defaultSequence()
    {
        return $this->belongsTo(ReminderSequence::class, 'default_sequence_id');
    }
}
