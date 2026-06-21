<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'details',
        'unit',
        'quantity',
        'unit_price',
        'tax_rate',
        'discount_percent',
        'total',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity'         => 'decimal:2',
            'unit_price'       => 'decimal:2',
            'tax_rate'         => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'total'            => 'decimal:2',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
