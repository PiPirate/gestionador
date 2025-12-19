<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CashMovement;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CashController extends Controller
{
    public function index(Request $request)
    {
        $movementsQuery = CashMovement::with('account')->orderByDesc('date')->orderByDesc('id');
        $movements = $movementsQuery->get();

        $monthStart = Carbon::now()->startOfMonth();

        $incomeCop = (clone $movementsQuery)->where('type', 'ingreso')->where('date', '>=', $monthStart)->sum('amount_cop');
        $expenseCop = (clone $movementsQuery)->where('type', 'egreso')->where('date', '>=', $monthStart)->sum('amount_cop');

        $incomeUsd = (clone $movementsQuery)->where('type', 'ingreso')->where('date', '>=', $monthStart)->sum('amount_usd');
        $expenseUsd = (clone $movementsQuery)->where('type', 'egreso')->where('date', '>=', $monthStart)->sum('amount_usd');

        $summary = [
            'cop' => [
                'income' => $incomeCop,
                'expense' => $expenseCop,
                'net' => $incomeCop - $expenseCop,
            ],
            'usd' => [
                'income' => $incomeUsd,
                'expense' => $expenseUsd,
                'net' => $incomeUsd - $expenseUsd,
            ],
        ];

        $accounts = Account::orderBy('name')->get();

        return view('modules.cash.index', compact('summary', 'movements', 'accounts'));
    }

    public function storeMovement(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:ingreso,egreso',
            'description' => 'required|string|max:255',
            'amount_cop' => 'nullable|numeric',
            'amount_usd' => 'nullable|numeric',
            'reference' => 'nullable|string|max:255',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        if (empty($data['amount_cop']) && empty($data['amount_usd'])) {
            return back()->withErrors(['amount_cop' => 'Debes registrar un monto en COP o en USD'])->withInput();
        }

        $movement = CashMovement::create([
            'date' => $data['date'],
            'type' => $data['type'],
            'description' => $data['description'],
            'amount_cop' => $data['amount_cop'] ?? 0,
            'amount_usd' => $data['amount_usd'] ?? 0,
            'reference' => $data['reference'] ?? null,
            'account_id' => $data['account_id'] ?? null,
        ]);

        $this->recalculateBalances();
        AuditLogger::log('Crear movimiento de caja', $movement, $data);

        return redirect()->route('cash.index')->with('status', 'Movimiento registrado');
    }

    public function updateMovement(Request $request, CashMovement $movement)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:ingreso,egreso',
            'description' => 'required|string|max:255',
            'amount_cop' => 'nullable|numeric',
            'amount_usd' => 'nullable|numeric',
            'reference' => 'nullable|string|max:255',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        if (empty($data['amount_cop']) && empty($data['amount_usd'])) {
            return back()->withErrors(['amount_cop' => 'Debes registrar un monto en COP o en USD'])->withInput();
        }

        $movement->update([
            'date' => $data['date'],
            'type' => $data['type'],
            'description' => $data['description'],
            'amount_cop' => $data['amount_cop'] ?? 0,
            'amount_usd' => $data['amount_usd'] ?? 0,
            'reference' => $data['reference'] ?? null,
            'account_id' => $data['account_id'] ?? null,
        ]);
        $this->recalculateBalances();
        AuditLogger::log('Actualizar movimiento de caja', $movement, $data);

        return redirect()->route('cash.index')->with('status', 'Movimiento actualizado');
    }

    public function destroyMovement(CashMovement $movement)
    {
        $movement->delete();
        $this->recalculateBalances();
        AuditLogger::log('Eliminar movimiento de caja', $movement, ['id' => $movement->id]);

        return redirect()->route('cash.index')->with('status', 'Movimiento eliminado');
    }

    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'balance_cop' => 'required|numeric',
            'balance_usd' => 'required|numeric',
        ]);

        $account = Account::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'balance_cop' => $data['balance_cop'],
            'balance_usd' => $data['balance_usd'],
            'last_synced_at' => now(),
        ]);

        AuditLogger::log('Crear cuenta', $account, $data);

        return redirect()->route('cash.index')->with('status', 'Cuenta creada');
    }

    public function updateAccount(Request $request, Account $account)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'balance_cop' => 'required|numeric',
            'balance_usd' => 'required|numeric',
        ]);

        $account->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'balance_cop' => $data['balance_cop'],
            'balance_usd' => $data['balance_usd'],
            'last_synced_at' => now(),
        ]);

        AuditLogger::log('Actualizar cuenta', $account, $data);

        return redirect()->route('cash.index')->with('status', 'Cuenta actualizada');
    }

    public function destroyAccount(Account $account)
    {
        $account->delete();
        AuditLogger::log('Eliminar cuenta', $account, ['id' => $account->id]);

        return redirect()->route('cash.index')->with('status', 'Cuenta eliminada');
    }

    private function recalculateBalances(): void
    {
        $balancesCop = [];
        $balancesUsd = [];

        CashMovement::orderBy('account_id')->orderBy('date')->orderBy('id')->each(function (CashMovement $movement) use (&$balancesCop, &$balancesUsd) {
            $key = $movement->account_id ?? 'global';
            $sign = $movement->type === 'egreso' ? -1 : 1;

            $balancesCop[$key] = ($balancesCop[$key] ?? 0) + (($movement->amount_cop ?? 0) * $sign);
            $balancesUsd[$key] = ($balancesUsd[$key] ?? 0) + (($movement->amount_usd ?? 0) * $sign);

            $movement->balance_cop = $balancesCop[$key];
            $movement->balance_usd = $balancesUsd[$key];
            $movement->save();
        });

        Account::each(function (Account $account) use ($balancesCop, $balancesUsd) {
            $account->update([
                'balance_cop' => $balancesCop[$account->id] ?? 0,
                'balance_usd' => $balancesUsd[$account->id] ?? 0,
                'last_synced_at' => now(),
            ]);
        });
    }
}
