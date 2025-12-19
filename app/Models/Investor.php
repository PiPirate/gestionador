<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'since',
        'status',
        'capital_usd',
        'monthly_rate',
        'gains_cop',
    ];

    protected $casts = [
        'since' => 'date',
    ];

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function liquidations(): HasMany
    {
        return $this->hasMany(Liquidation::class);
    }
}
