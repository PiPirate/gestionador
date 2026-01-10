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

    // CAMBIO: ganancia diaria real (aprox) = capital * (tasa mensual / 30)
    public function dailyGainCop(?Carbon $asOf = null): float
    {
        if ($this->amount_cop <= 0) {
            return 0.0;
        }

        // Solo gana si está activa
        if ($this->status !== 'activa') {
            return 0.0;
        }

        if (!$this->start_date) {
            return 0.0;
        }

        $asOf = ($asOf?->copy() ?? Carbon::today())->startOfDay();

        // Si aún no inicia, no gana
        if ($asOf->lt($this->start_date->copy()->startOfDay())) {
            return 0.0;
        }

        // Si tiene end_date y ya pasó, no gana después de esa fecha
        if ($this->end_date && $asOf->gt($this->end_date->copy()->startOfDay())) {
            return 0.0;
        }

        $monthlyRate = ($this->monthly_rate / 100);

        $dailyRate = $monthlyRate / 30;

        return (float) $this->amount_cop * $dailyRate;
    }

    public function accumulatedGainCop(?Carbon $asOf = null): float
    {
        if (!$this->start_date || $this->amount_cop <= 0) {
            return 0.0;
        }

        $end = $this->resolvedEndDate($asOf);

        $totalDays = $this->totalInvestmentDays();
        if ($totalDays <= 0) {
            return 0.0;
        }

        $daysElapsed = max(0, $this->start_date->diffInDays($end, false) + 1);
        $daysElapsed = min($daysElapsed, $totalDays);

        // CAMBIO: acumulado = ganancia diaria real * días transcurridos (tope por totalDays)
        return $this->dailyGainCop($end) * $daysElapsed;
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

        return max(0, $this->start_date->diffInDays($end, false) + 1);
    }

    public function totalInvestmentDays(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        return max(0, $this->start_date->diffInDays($this->end_date, false) + 1);
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
