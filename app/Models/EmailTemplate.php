<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'subject',
        'body_html',
        'body_text',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
