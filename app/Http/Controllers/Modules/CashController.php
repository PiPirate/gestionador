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

        $income = (clone $movementsQuery)->where('type', 'ingreso')->where('date', '>=', $monthStart)->sum('amount_cop');
        $expenses = (clone $movementsQuery)->where('type', 'egreso')->where('date', '>=', $monthStart)->sum('amount_cop');

        $summary = [
            'income' => $income,
            'expenses' => $expenses,
            'net' => $income - $expenses,
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
            'amount_cop' => 'required|numeric',
            'reference' => 'nullable|string|max:255',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        $movement = CashMovement::create([
            'date' => $data['date'],
            'type' => $data['type'],
            'description' => $data['description'],
            'amount_cop' => $data['amount_cop'],
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
            'amount_cop' => 'required|numeric',
            'reference' => 'nullable|string|max:255',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        $movement->update($data);
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
        $balances = [];
        CashMovement::orderBy('account_id')->orderBy('date')->orderBy('id')->each(function (CashMovement $movement) use (&$balances) {
            $key = $movement->account_id ?? 'global';
            $sign = $movement->type === 'egreso' ? -1 : 1;
            $balances[$key] = ($balances[$key] ?? 0) + ($movement->amount_cop * $sign);
            $movement->balance_cop = $balances[$key];
            $movement->save();
        });

        Account::each(function (Account $account) use ($balances) {
            $account->update([
                'balance_cop' => $balances[$account->id] ?? 0,
                'last_synced_at' => now(),
            ]);
        });
    }
}
