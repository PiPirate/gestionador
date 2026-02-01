<?php

namespace App\Services;

class ProfitRuleCalculator
{
    /**
     * @param array<int, array{upTo?: float|int|null, up_to?: float|int|null, rate?: float|int}> $tiers
     */
    public static function calcMonthlyProfit(float $amount, array $tiers): float
    {
        if ($amount <= 0) {
            return 0.0;
        }

        $normalized = collect($tiers)
            ->map(function (array $tier) {
                $upTo = $tier['upTo'] ?? $tier['up_to'] ?? null;
                $rate = (float) ($tier['rate'] ?? 0);
                return [
                    'up_to' => $upTo === null ? null : (float) $upTo,
                    'rate' => max(0, $rate),
                ];
            })
            ->sortBy(function (array $tier) {
                return $tier['up_to'] ?? INF;
            })
            ->values();

        $remaining = $amount;
        $previousCap = 0.0;
        $profit = 0.0;

        foreach ($normalized as $tier) {
            $cap = $tier['up_to'] === null ? INF : max((float) $tier['up_to'], $previousCap);
            $chunk = min($remaining, $cap - $previousCap);

            if ($chunk > 0) {
                $profit += $chunk * $tier['rate'];
                $remaining -= $chunk;
            }

            $previousCap = $cap;

            if ($remaining <= 0) {
                break;
            }
        }

        return round($profit, 2);
    }
}
