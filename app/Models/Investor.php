<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
        'monthly_rate',
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

    public function totalInvestedCop(): float
    {
        return $this->investments->sum('amount_cop');
    }

    public function totalWithdrawnCop(): float
    {
        return $this->investments
            ->where('status', 'cerrada')
            ->sum('amount_cop');
    }

    public function totalGainsCop(): float
    {
        return $this->investments
            ->where('status', 'cerrada')
            ->sum(fn (Investment $investment) => $investment->monthlyEstimatedGainCop());
    }

    public function totalDaysInvested(): int
    {
        if ($this->investments->isEmpty()) {
            return 0;
        }

        $startDate = $this->investments->min('start_date');
        $endDate = Carbon::today();

        if (!$startDate) {
            return 0;
        }

        $start = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $end = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        return max(0, $start->diffInDays($end, false) + 1);
    }
}
