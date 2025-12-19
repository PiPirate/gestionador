<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\AuditLogger;
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
            'inventory' => max(0, Transaction::where('type', 'compra')->sum('amount_usd') - Transaction::where('type', 'venta')->sum('amount_usd')),
        ];

        return view('modules.transactions.index', compact('summary', 'history'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'amount_usd' => 'required|numeric',
            'rate' => 'required|numeric',
            'amount_cop' => 'required|numeric',
            'counterparty' => 'required|string|max:255',
            'method' => 'nullable|string|max:255',
            'profit_cop' => 'nullable|numeric',
            'transacted_at' => 'required|date',
            'reference' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create($data);
        AuditLogger::log('Crear transacción', $transaction, $data);

        return redirect()->route('transactions.index')->with('status', 'Transacción creada');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'amount_usd' => 'required|numeric',
            'rate' => 'required|numeric',
            'amount_cop' => 'required|numeric',
            'counterparty' => 'required|string|max:255',
            'method' => 'nullable|string|max:255',
            'profit_cop' => 'nullable|numeric',
            'transacted_at' => 'required|date',
            'reference' => 'nullable|string|max:255',
        ]);

        $transaction->update($data);
        AuditLogger::log('Actualizar transacción', $transaction, $data);

        return redirect()->route('transactions.index')->with('status', 'Transacción actualizada');
    }
}
