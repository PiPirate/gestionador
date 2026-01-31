<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Liquidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'investment_id',
        'code',
        'amount_usd',
        'monthly_rate',
        'period_start',
        'period_end',
        'gain_cop',
        'withdrawn_gain_cop',
        'withdrawn_capital_cop',
        'total_cop',
        'status',
        'due_date',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'due_date' => 'date',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }
}
