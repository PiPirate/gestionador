<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'code',
        'amount_usd',
        'monthly_rate',
        'start_date',
        'gains_cop',
        'next_liquidation_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'next_liquidation_date' => 'date',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }
}
