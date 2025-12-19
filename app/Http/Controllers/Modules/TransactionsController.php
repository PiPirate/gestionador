<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('counterparty', 'like', '%' . $request->q . '%')
                    ->orWhere('reference', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('type') && $request->type !== 'todas') {
            $query->where('type', $request->type);
        }

        $history = $query->orderByDesc('transacted_at')->get();

        $summary = [
            'bought' => Transaction::where('type', 'compra')->sum('amount_usd'),
            'sold' => Transaction::where('type', 'venta')->sum('amount_usd'),
            'net_profit' => Transaction::sum('profit_cop'),
            'inventory' => 15,
        ];

        return view('modules.transactions.index', compact('summary', 'history'));
    }
}
