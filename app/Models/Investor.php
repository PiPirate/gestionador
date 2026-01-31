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

    public function capital_in_circulation(): float
    {
        // Si ya viene cargada la relación (como en index: with('investments')),
        // evitamos otra consulta
        if ($this->relationLoaded('investments')) {
            return (float) $this->investments
                ->filter(fn ($inv) => strtolower((string) $inv->status) === 'activa')
                ->sum('amount_cop');
        }

        // Si NO está cargada, lo calculamos por query
        return (float) $this->investments()
            ->whereIn('status', ['activa', 'Activa'])
            ->sum('amount_cop');
    }


    public function totalWithdrawnCop(): float
    {
        if ($this->relationLoaded('liquidations')) {
            return (float) $this->liquidations->sum('withdrawn_capital_cop');
        }

        return (float) $this->liquidations()->sum('withdrawn_capital_cop');
    }

    public function totalGainsCop(): float
    {
        if ($this->relationLoaded('investments')) {
            return (float) $this->investments->sum(fn (Investment $investment) => $investment->availableGainCop());
        }

        return (float) $this->investments()->get()->sum(fn (Investment $investment) => $investment->availableGainCop());
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
