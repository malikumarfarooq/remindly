<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'reminder_sequence_id',
        'offset_days',
        'offset_from',
        'label',
        'tone',
        'channel',
        'subject_template',
        'body_template',
        'ai_generate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'ai_generate' => 'boolean',
            'is_active'   => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function sequence()
    {
        return $this->belongsTo(ReminderSequence::class, 'reminder_sequence_id');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
