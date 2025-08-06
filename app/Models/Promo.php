<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'max_uses',
        'max_uses_scope',
        'current_uses',
        'expires_at',
        'constraints',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'constraints' => 'array',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'promo_code', 'code');
    }
}