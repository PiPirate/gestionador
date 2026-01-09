<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'code',
        'amount_cop',
        'monthly_rate',
        'start_date',
        'end_date',
        'gains_cop',
        'next_liquidation_date',
        'status',
        'closed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_liquidation_date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function dailyGainCop(?Carbon $asOf = null): float
    {
        if (!$this->start_date || $this->amount_cop <= 0) {
            return 0.0;
        }

        $end = $this->resolvedEndDate($asOf);
        $daysElapsed = max(0, $this->start_date->diffInDays($end, false));
        $dailyRate = ($this->monthly_rate / 100) / 30;

        return $this->amount_cop * $dailyRate * $daysElapsed;
    }

    public function monthlyEstimatedGainCop(): float
    {
        if ($this->amount_cop <= 0) {
            return 0.0;
        }

        return $this->amount_cop * ($this->monthly_rate / 100);
    }

    public function daysInvested(?Carbon $asOf = null): int
    {
        if (!$this->start_date) {
            return 0;
        }

        $end = $this->resolvedEndDate($asOf);

        return max(0, $this->start_date->diffInDays($end, false));
    }

    private function resolvedEndDate(?Carbon $asOf = null): Carbon
    {
        $asOf = $asOf?->copy() ?? now();

        if ($this->status === 'cerrada') {
            if ($this->closed_at) {
                return $this->closed_at;
            }

            if ($this->end_date) {
                return $this->end_date;
            }
        }

        if ($this->end_date && $this->end_date->lessThan($asOf)) {
            return $this->end_date;
        }

        return $asOf;
    }
}
