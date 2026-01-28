<?php

namespace App\Services;

use App\Models\Investment;
use Illuminate\Support\Carbon;

class InvestmentLifecycleService
{
    public function closeExpiredInvestments(): int
    {
        $today = Carbon::today();
        $expired = Investment::where('status', '!=', 'cerrada')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', $today)
            ->get();

        if ($expired->isEmpty()) {
            return 0;
        }

        $renewed = 0;

        foreach ($expired as $investment) {
            if (!$investment->end_date) {
                continue;
            }

            $nextEndDate = $investment->end_date->copy();
            while ($nextEndDate->lessThanOrEqualTo($today)) {
                $nextEndDate = $nextEndDate->addMonthNoOverflow();
            }

            $investment->end_date = $nextEndDate;
            $investment->save();
            $renewed++;
        }

        return $renewed;
    }
}
