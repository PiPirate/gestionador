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
        $capitalCop = Investment::sum('amount_cop');
        $capitalLocal = Investment::sum('gains_cop') + Transaction::where('type', 'venta')->sum('amount_cop');
        $monthlyGain = Transaction::sum('profit_cop');
        $investments = Investment::all();

        $metrics = [
            'capital_cop' => $capitalCop,
            'capital_local' => $capitalLocal,
            'monthly_gain' => $monthlyGain,
            'investors_active' => Investor::count(),
            'avg_investment' => Investment::avg('amount_cop') ?? 0,
            'avg_return' => $investments->isNotEmpty()
                ? $investments->avg(fn (Investment $investment) => $investment->effectiveMonthlyRate())
                : 0,
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
                'cop' => max(0, $capitalCop - Investment::where('status', 'activa')->sum('amount_cop')),
                'estimated_margin' => $monthlyGain,
            ],
            'investor_growth' => max(0, Investor::whereMonth('created_at', now()->month)->count()),
        ];

        $users = User::orderBy('name')->get();

        return view('modules.dashboard.index', compact('metrics', 'cards', 'users'));
    }
}
