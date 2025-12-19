<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Investor;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class InvestmentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Investment::with('investor');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->q . '%')
                    ->orWhereHas('investor', function ($iq) use ($request) {
                        $iq->where('name', 'like', '%' . $request->q . '%');
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'todas') {
            $query->where('status', $request->status);
        }

        $investments = $query->orderByDesc('start_date')->get();

        $summary = [
            'total_usd' => $investments->sum('amount_usd'),
            'avg_return' => round($investments->avg('monthly_rate'), 2),
            'accumulated' => $investments->sum('gains_cop'),
            'next_liquidations' => $investments->where('status', 'pendiente')->count(),
        ];

        $investors = Investor::orderBy('name')->get();

        return view('modules.investments.index', compact('investments', 'summary', 'investors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'code' => 'required|string|max:50',
            'amount_usd' => 'required|numeric',
            'monthly_rate' => 'required|numeric',
            'start_date' => 'required|date',
            'gains_cop' => 'nullable|numeric',
            'next_liquidation_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $investment = Investment::create($data);
        AuditLogger::log('Crear inversión', $investment, $data);

        return redirect()->route('investments.index')->with('status', 'Inversión creada');
    }

    public function update(Request $request, Investment $investment)
    {
        $data = $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'code' => 'required|string|max:50',
            'amount_usd' => 'required|numeric',
            'monthly_rate' => 'required|numeric',
            'start_date' => 'required|date',
            'gains_cop' => 'nullable|numeric',
            'next_liquidation_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $investment->update($data);
        AuditLogger::log('Actualizar inversión', $investment, $data);

        return redirect()->route('investments.index')->with('status', 'Inversión actualizada');
    }
}
