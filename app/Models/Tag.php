<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'color'];

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_tag');
    }
}
