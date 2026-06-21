<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'loggable_id',
        'loggable_type',
        'action',
        'description',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loggable()
    {
        return $this->morphTo();
    }

    // ── Static Helper — log karne ke liye ──────────────────

    public static function record(
        int $userId,
        string $action,
        string $description,
        Model $model = null,
        array $changes = []
    ): self {
        return self::create([
            'user_id'       => $userId,
            'action'        => $action,
            'description'   => $description,
            'loggable_id'   => $model?->id,
            'loggable_type' => $model ? get_class($model) : null,
            'changes'       => $changes,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);
    }
}
