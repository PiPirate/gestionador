<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CashMovement;
use Illuminate\Http\Request;

class CashController extends Controller
{
    public function index(Request $request)
    {
        $income = CashMovement::where('type', 'ingreso')->sum('amount_cop');
        $expenses = abs(CashMovement::where('type', 'egreso')->sum('amount_cop'));

        $summary = [
            'income' => $income,
            'income_breakdown' => [
                'ventas' => $income * 0.715,
                'inversiones' => $income * 0.285,
            ],
            'expenses' => $expenses,
            'expenses_breakdown' => [
                'compras' => $expenses * 0.83,
                'liquidaciones' => $expenses * 0.17,
            ],
            'net' => $income - $expenses,
        ];

        $movements = CashMovement::orderByDesc('date')->get();
        $accounts = Account::orderBy('name')->get();

        return view('modules.cash.index', compact('summary', 'movements', 'accounts'));
    }
}
