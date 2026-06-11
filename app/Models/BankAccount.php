<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_title',
        'account_number',
        'iban',
        'swift_bic',
        'routing_number',
        'currency',
        'country',
        'is_default',
        'show_on_invoice',
    ];

    protected function casts(): array
    {
        return [
            'is_default'      => 'boolean',
            'show_on_invoice' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
