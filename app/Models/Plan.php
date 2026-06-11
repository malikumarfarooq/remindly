<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price_monthly',
        'price_yearly',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
        'max_clients',
        'max_invoices_per_month',
        'max_team_members',
        'has_ai',
        'has_whatsapp',
        'has_custom_branding',
        'has_api_access',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly'         => 'decimal:2',
            'price_yearly'          => 'decimal:2',
            'has_ai'                => 'boolean',
            'has_whatsapp'          => 'boolean',
            'has_custom_branding'   => 'boolean',
            'has_api_access'        => 'boolean',
            'is_active'             => 'boolean',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
