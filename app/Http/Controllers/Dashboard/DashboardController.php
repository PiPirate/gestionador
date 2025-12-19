<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\Liquidation;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $capitalUsd = Investment::sum('amount_usd');
        $capitalCop = Investment::sum('gains_cop') + Transaction::where('type', 'venta')->sum('amount_cop');
        $monthlyGain = Transaction::sum('profit_cop');

        $metrics = [
            'capital_usd' => $capitalUsd,
            'capital_cop' => $capitalCop,
            'monthly_gain' => $monthlyGain,
            'investors_active' => Investor::count(),
            'avg_investment' => Investment::avg('amount_usd') ?? 0,
            'avg_return' => Investment::avg('monthly_rate') ?? 0,
            'operations_month' => Transaction::count(),
        ];

        $pendingLiquidations = Liquidation::where('status', 'pendiente')->count();
        $pendingTotal = Liquidation::where('status', 'pendiente')->sum('total_cop');

        $cards = [
            'pending_liquidations' => [
                'count' => $pendingLiquidations,
                'total' => $pendingTotal,
            ],
            'available_capital' => [
                'usd' => max(0, $capitalUsd - Investment::where('status', 'activa')->sum('amount_usd')),
                'estimated_margin' => $monthlyGain,
            ],
            'investor_growth' => max(0, Investor::whereMonth('created_at', now()->month)->count()),
        ];

        $users = User::orderBy('name')->get();

        return view('modules.dashboard.index', compact('metrics', 'cards', 'users'));
    }
}
