<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'status',
        'closed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function liquidations(): HasMany
    {
        return $this->hasMany(Liquidation::class);
    }

    public function withdrawnGainCop(): float
    {
        if ($this->relationLoaded('liquidations')) {
            return (float) $this->liquidations->sum('withdrawn_gain_cop');
        }

        return (float) $this->liquidations()->sum('withdrawn_gain_cop');
    }

    public function withdrawnCapitalCop(): float
    {
        if ($this->relationLoaded('liquidations')) {
            return (float) $this->liquidations->sum('withdrawn_capital_cop');
        }

        return (float) $this->liquidations()->sum('withdrawn_capital_cop');
    }

    public function availableGainCop(?Carbon $asOf = null): float
    {
        return max(0, $this->accumulatedGainCop($asOf) - $this->withdrawnGainCop());
    }

    public function availableCapitalCop(): float
    {
        return max(0, $this->amount_cop - $this->withdrawnCapitalCop());
    }

    public function dailyGainCop(): float
    {
        $monthlyInterest = $this->monthlyInterestCop();
        if ($monthlyInterest <= 0) {
            return 0.0;
        }

        $monthDays = $this->monthDays();
        if ($monthDays <= 0) {
            return 0.0;
        }

        return $monthlyInterest / $monthDays;
    }

    public function accumulatedGainCop(?Carbon $asOf = null): float
    {
        if (!$this->start_date || $this->amount_cop <= 0) {
            return 0.0;
        }

        $end = $this->resolvedEndDate($asOf);
        $monthDays = $this->monthDays();
        if ($monthDays <= 0) {
            return 0.0;
        }

        $daysElapsed = max(0, $this->start_date->diffInDays($end, false) + 1);
        $daysElapsed = min($daysElapsed, $this->totalInvestmentDays());

        return $this->dailyGainCop() * $daysElapsed;
    }

    public function monthlyEstimatedGainCop(): float
    {
        return $this->totalProjectedGainCop();
    }

    public function totalProjectedGainCop(): float
    {
        $monthlyInterest = $this->monthlyInterestCop();
        $monthDays = $this->monthDays();
        $activeDays = $this->totalInvestmentDays();

        if ($monthlyInterest <= 0 || $monthDays <= 0 || $activeDays <= 0) {
            return 0.0;
        }

        return ($monthlyInterest / $monthDays) * $activeDays;
    }

    public function daysInvested(?Carbon $asOf = null): int
    {
        if (!$this->start_date) {
            return 0;
        }

        $end = $this->resolvedEndDate($asOf);

        return max(0, $this->start_date->diffInDays($end, false) + 1);
    }

    public function totalInvestmentDays(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        return max(0, $this->start_date->diffInDays($this->end_date, false) + 1);
    }

    public function monthDays(): int
    {
        if ($this->end_date) {
            return $this->end_date->daysInMonth;
        }

        if ($this->start_date) {
            return $this->start_date->daysInMonth;
        }

        return 0;
    }

    public function monthlyInterestCop(): float
    {
        if ($this->amount_cop <= 0) {
            return 0.0;
        }

        return $this->amount_cop * ($this->monthly_rate / 100);
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
