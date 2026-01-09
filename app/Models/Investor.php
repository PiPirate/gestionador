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
        return $this->investments->sum(fn (Investment $investment) => $investment->dailyGainCop());
    }

    public function totalDaysInvested(): int
    {
        return $this->investments->sum(fn (Investment $investment) => $investment->daysInvested());
    }
}
