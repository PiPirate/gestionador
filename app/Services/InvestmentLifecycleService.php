<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\User;
use App\Notifications\InvestmentClosedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

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

        $users = User::query()->get();

        foreach ($expired as $investment) {
            $investment->status = 'cerrada';
            $investment->closed_at = $investment->closed_at ?? $today;
            $investment->end_date = $investment->end_date ?? $today;
            $investment->save();

            if ($users->isNotEmpty()) {
                Notification::send($users, new InvestmentClosedNotification($investment));
            }
        }

        return $expired->count();
    }
}
