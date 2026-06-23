<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active'  => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function steps()
    {
        return $this->hasMany(ReminderStep::class);
    }

    public function stepsOrdered()
    {
        return $this->hasMany(ReminderStep::class)->orderBy('days_offset');
    }

    public function notificationSettings()
    {
        return $this->hasMany(NotificationSetting::class, 'default_sequence_id');
    }
}
