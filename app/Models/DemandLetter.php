<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandLetter extends Model
{
    protected $fillable = [
        'invoice_id',
        'client_id',
        'user_id',
        'status',
        'subject',
        'body',
        'ai_generated',
        'ai_model',
        'pdf_path',
        'sent_at',
        'sent_via',
        'legal_notes',
    ];

    protected function casts(): array
    {
        return [
            'ai_generated' => 'boolean',
            'sent_at'      => 'datetime',
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

    // ── Helpers ────────────────────────────────────────────

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isEscalated(): bool
    {
        return $this->status === 'legal_escalated';
    }
}
