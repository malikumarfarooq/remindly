<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'notes',
        'terms',
        'default_due_days',
        'line_items',
        'total_amount',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'line_items'   => 'array',
            'total_amount' => 'decimal:2',
            'is_active'    => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'template_id');
    }
}
