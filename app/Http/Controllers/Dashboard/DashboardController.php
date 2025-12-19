<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\Liquidation;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $capitalUsd = Investment::sum('amount_usd');
        $capitalCop = Investment::sum('gains_cop') + Transaction::where('type', 'venta')->sum('amount_cop');
        $monthlyGain = 8450000; // fallback demo value

        $metrics = [
            'capital_usd' => $capitalUsd,
            'capital_cop' => $capitalCop,
            'monthly_gain' => $monthlyGain,
            'investors_active' => Investor::count(),
            'avg_investment' => Investment::avg('amount_usd'),
            'avg_return' => Investment::avg('monthly_rate'),
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
                'usd' => 15,
                'estimated_margin' => 3850000,
            ],
            'investor_growth' => 3,
        ];

        return view('modules.dashboard.index', compact('metrics', 'cards'));
    }
}
