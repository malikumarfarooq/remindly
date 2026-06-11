<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandingSetting extends Model
{
    protected $fillable = [
        'user_id',
        'logo_path',
        'primary_color',
        'secondary_color',
        'font_family',
        'invoice_template',
        'custom_domain',
        'email_signature',
        'invoice_footer',
        'show_payremind_branding',
    ];

    protected function casts(): array
    {
        return [
            'show_payremind_branding' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
