<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'payment_id',
        'invoice_id',
        'user_id',
        'amount',
        'currency',
        'reason',
        'notes',
        'status',
        'gateway_refund_id',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'refunded_at' => 'datetime',
        ];
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
