<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company_name',
        'business_type',
        'avatar',
        'timezone',
        'currency',
        'language',
        'plan',
        'plan_expires_at',
        'stripe_customer_id',
        'paddle_customer_id',
        'payment_link_slug',
        'tax_number',
        'website',
        'business_address',
        'city',
        'country',
        'postal_code',
        'invoice_footer_notes',
        'invoice_prefix',
        'two_factor_enabled',
        'referral_code',
        'referred_by',
        'last_login_at',
        'last_login_ip',
        'is_active',
        'is_suspended',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'plan_expires_at'   => 'datetime',
            'last_login_at'     => 'datetime',
            'two_factor_enabled' => 'boolean',
            'is_active'         => 'boolean',
            'is_suspended'      => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function reminderSequences()
    {
        return $this->hasMany(ReminderSequence::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function notificationSetting()
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class, 'owner_user_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function isOnPlan(string $plan): bool
    {
        return $this->plan === $plan;
    }

    public function isPro(): bool
    {
        return in_array($this->plan, ['pro', 'business']);
    }
}
