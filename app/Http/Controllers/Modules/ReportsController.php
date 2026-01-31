<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\Investment;
use App\Models\Transaction;
use App\Models\Liquidation;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $transactions = Transaction::query();
        $liquidations = Liquidation::query();
        $cashMovements = CashMovement::query();

        if ($start) {
            $transactions->whereDate('transacted_at', '>=', $start);
            $liquidations->whereDate('period_start', '>=', $start);
            $cashMovements->whereDate('date', '>=', $start);
        }

        if ($end) {
            $transactions->whereDate('transacted_at', '<=', $end);
            $liquidations->whereDate('period_end', '<=', $end);
            $cashMovements->whereDate('date', '<=', $end);
        }

        $metrics = [
            'transactions' => [
                'count' => $transactions->count(),
                'profit_cop' => $transactions->sum('profit_cop'),
                'volume_usd' => $transactions->sum('amount_usd'),
            ],
            'investments' => [
                'count' => Investment::count(),
                'capital_cop' => Investment::sum('amount_cop'),
                'avg_rate' => Investment::all()->avg(fn (Investment $investment) => $investment->effectiveMonthlyRate()),
            ],
            'liquidations' => [
                'pending' => (clone $liquidations)->where('status', 'pendiente')->count(),
                'processed' => (clone $liquidations)->where('status', 'procesada')->count(),
                'paid_cop' => (clone $liquidations)->where('status', 'procesada')->sum('total_cop'),
            ],
            'cash' => [
                'income_cop' => (clone $cashMovements)->where('type', 'ingreso')->sum('amount_cop'),
                'expense_cop' => (clone $cashMovements)->where('type', 'egreso')->sum('amount_cop'),
            ],
        ];

        $topInvestors = Investor::withSum('investments', 'amount_cop')
            ->orderByDesc('investments_sum_amount_cop')
            ->take(5)
            ->get();

        $recentTransactions = Transaction::orderByDesc('transacted_at')->limit(10)->get();

        return view('modules.reports.index', compact('metrics', 'topInvestors', 'recentTransactions', 'start', 'end'));
    }

    public function exportTransactions(Request $request): StreamedResponse
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $transactions = Transaction::query();
        if ($start) {
            $transactions->whereDate('transacted_at', '>=', $start);
        }
        if ($end) {
            $transactions->whereDate('transacted_at', '<=', $end);
        }

        $filename = 'transactions-report.csv';

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Fecha', 'Tipo', 'USD', 'COP', 'Ganancia', 'Counterparty', 'Método', 'Referencia']);

            $transactions->orderBy('transacted_at')->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $tx) {
                    fputcsv($handle, [
                        optional($tx->transacted_at)->format('Y-m-d'),
                        $tx->type,
                        $tx->amount_usd,
                        $tx->amount_cop,
                        $tx->profit_cop,
                        $tx->counterparty,
                        $tx->method,
                        $tx->reference,
                    ]);
                }
            });

            fclose($handle);
        }, $filename);
    }
}
