<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_used',
        'input_data',
        'output_data',
        'tokens_used',
        'cost_usd',
        'was_used',
    ];

    protected function casts(): array
    {
        return [
            'input_data'  => 'array',
            'output_data' => 'array',
            'cost_usd'    => 'decimal:6',
            'was_used'    => 'boolean',
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
}
