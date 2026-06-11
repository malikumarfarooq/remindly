<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'whatsapp',
        'company',
        'contact_person',
        'language',
        'preferred_channel',
        'currency',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'tax_number',
        'website',
        'risk_level',
        'risk_score',
        'avg_days_late',
        'total_invoices_count',
        'total_invoiced',
        'total_outstanding',
        'total_paid',
        'last_payment_at',
        'member_since',
        'preferred_send_time',
        'preferred_send_day',
        'email_unsubscribed',
        'sms_unsubscribed',
        'whatsapp_unsubscribed',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_payment_at'        => 'datetime',
            'member_since'           => 'datetime',
            'email_unsubscribed'     => 'boolean',
            'sms_unsubscribed'       => 'boolean',
            'whatsapp_unsubscribed'  => 'boolean',
            'is_active'              => 'boolean',
            'risk_score'             => 'decimal:2',
            'total_invoiced'         => 'decimal:2',
            'total_outstanding'      => 'decimal:2',
            'total_paid'             => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'client_tag');
    }

    // ── Helpers ────────────────────────────────────────────

    public function isHighRisk(): bool
    {
        return $this->risk_level === 'high';
    }

    public function hasOutstanding(): bool
    {
        return $this->total_outstanding > 0;
    }
}
