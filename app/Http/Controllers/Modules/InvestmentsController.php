<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\ProfitRule;
use App\Services\AuditLogger;
use App\Services\InvestmentLifecycleService;
use App\Services\ProfitRuleCalculator;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class InvestmentsController extends Controller
{
    public function index(Request $request)
    {
        app(InvestmentLifecycleService::class)->closeExpiredInvestments();
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
        } else {
            $query->where('status', '!=', 'cerrada');
        }

        $investments = $query->orderByDesc('start_date')->get();

        $avgReturn = $investments->isNotEmpty()
            ? $investments->avg(fn (Investment $investment) => $investment->effectiveMonthlyRate())
            : 0;

        $summary = [
            'total_cop' => Investment::where('status', 'activa')->sum('amount_cop'),
            'avg_return' => round($avgReturn, 2),
            'accumulated' => $investments->sum(fn (Investment $investment) => $investment->dailyGainCop()),
            'monthly_projection' => $investments->sum(fn (Investment $investment) => $investment->totalProjectedGainCop()),
        ];

        $activeProfitRule = ProfitRule::where('is_active', true)->first();
        $investors = Investor::orderBy('name')->get();
        $continuableInvestments = Investment::with('investor')
            ->where('status', '!=', 'cerrada')
            ->orderByDesc('start_date')
            ->get();

        return view('modules.investments.index', compact('investments', 'summary', 'investors', 'continuableInvestments', 'activeProfitRule'));
    }

    public function store(Request $request)
    {
        $rules = [
            'investor_id' => 'required|exists:investors,id',
            'continuation_id' => 'nullable|exists:investments,id',
            'amount_cop' => 'required_without:continuation_id|numeric|min:0',
            'start_date' => 'required_without:continuation_id|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:pendiente,activa,cerrada',
        ];

        if (!$request->filled('continuation_id')) {
            $rules['end_date'] .= '|after_or_equal:start_date';
        }

        $data = $request->validate($rules);

        if (!empty($data['continuation_id'])) {
            $investment = Investment::findOrFail($data['continuation_id']);
            $investor = Investor::findOrFail($data['investor_id']);

            if ($investment->investor_id !== (int) $data['investor_id']) {
                return back()->withErrors(['continuation_id' => 'La inversión seleccionada no pertenece al inversor.']);
            }

            if ($investment->status === 'cerrada') {
                return back()->withErrors(['continuation_id' => 'No puedes renovar una inversión cerrada.']);
            }

            if (!empty($data['end_date']) && $investment->end_date && $investment->end_date->greaterThan(Carbon::parse($data['end_date']))) {
                return back()->withErrors(['end_date' => 'La fecha de finalización debe ser igual o posterior a la actual.']);
            }

            $previousEndDate = $investment->end_date?->copy();
            $nextStartDate = $previousEndDate?->copy()->addDay() ?? Carbon::now();
            $nextEndDate = $data['end_date'] ?? $investment->end_date?->copy()->addMonthNoOverflow()->toDateString();
            $nextEndDate = $nextEndDate ?? Carbon::now()->endOfMonth()->toDateString();

            $investment->status = 'cerrada';
            $investment->closed_at = $previousEndDate ?? Carbon::now();
            if (!$investment->end_date && $nextStartDate) {
                $investment->end_date = $nextStartDate->copy()->subDay()->toDateString();
            }
            $investment->save();

            $code = $this->generateCode($investor);

            $profitRule = ProfitRule::where('is_active', true)->first();
            if (!$profitRule) {
                return back()->withErrors(['profit_rule' => 'No hay una regla de rentabilidad activa.']);
            }

            $monthlyProfit = ProfitRuleCalculator::calcMonthlyProfit($investment->amount_cop, $profitRule->tiers_json ?? []);
            $monthReference = $nextEndDate ? Carbon::parse($nextEndDate) : Carbon::parse($nextStartDate);
            $monthDays = $monthReference->daysInMonth;
            $dailyInterest = $monthDays > 0 ? $monthlyProfit / $monthDays : 0;
            $effectiveRate = $investment->amount_cop > 0 ? ($monthlyProfit / $investment->amount_cop) * 100 : 0;

            $newInvestment = Investment::create([
                'investor_id' => $investor->id,
                'profit_rule_id' => $profitRule->id,
                'code' => $code,
                'amount_cop' => $investment->amount_cop,
                'monthly_rate' => $effectiveRate,
                'tiers_snapshot' => $profitRule->tiers_json,
                'monthly_profit_snapshot' => $monthlyProfit,
                'daily_interest_snapshot' => $dailyInterest,
                'start_date' => $nextStartDate?->toDateString(),
                'end_date' => $nextEndDate,
                'status' => 'activa',
            ]);

            AuditLogger::log('Renovar inversión', $newInvestment, [
                'continuation_id' => $investment->id,
                'end_date' => $nextEndDate,
            ]);

            return redirect()->route('investments.index')->with('status', 'Inversión renovada');
        }

        $investor = Investor::findOrFail($data['investor_id']);
        $code = $this->generateCode($investor);
        $profitRule = ProfitRule::where('is_active', true)->first();
        if (!$profitRule) {
            return back()->withErrors(['profit_rule' => 'No hay una regla de rentabilidad activa.']);
        }

        $monthlyProfit = ProfitRuleCalculator::calcMonthlyProfit($data['amount_cop'], $profitRule->tiers_json ?? []);
        $monthReference = $data['end_date'] ? Carbon::parse($data['end_date']) : Carbon::parse($data['start_date']);
        $monthDays = $monthReference->daysInMonth;
        $dailyInterest = $monthDays > 0 ? $monthlyProfit / $monthDays : 0;
        $effectiveRate = $data['amount_cop'] > 0 ? ($monthlyProfit / $data['amount_cop']) * 100 : 0;

        $investment = Investment::create([
            'investor_id' => $investor->id,
            'profit_rule_id' => $profitRule->id,
            'code' => $code,
            'amount_cop' => $data['amount_cop'],
            'monthly_rate' => $effectiveRate,
            'tiers_snapshot' => $profitRule->tiers_json,
            'monthly_profit_snapshot' => $monthlyProfit,
            'daily_interest_snapshot' => $dailyInterest,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'status' => $data['status'],
            'closed_at' => $data['status'] === 'cerrada' ? Carbon::now() : null,
        ]);
        AuditLogger::log('Crear inversión', $investment, $data);

        return redirect()->route('investments.index')->with('status', 'Inversión creada');
    }

    public function update(Request $request, Investment $investment)
    {
        $data = $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'amount_cop' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pendiente,activa,cerrada',
        ]);

        $investment->fill($data);

        if ($investment->tiers_snapshot && $investment->amount_cop > 0) {
            $monthlyProfit = ProfitRuleCalculator::calcMonthlyProfit($investment->amount_cop, $investment->tiers_snapshot);
            $monthReference = $investment->end_date ?: $investment->start_date;
            $monthDays = $monthReference ? $monthReference->daysInMonth : 0;
            $dailyInterest = $monthDays > 0 ? $monthlyProfit / $monthDays : 0;
            $effectiveRate = $investment->amount_cop > 0 ? ($monthlyProfit / $investment->amount_cop) * 100 : 0;

            $investment->monthly_rate = $effectiveRate;
            $investment->monthly_profit_snapshot = $monthlyProfit;
            $investment->daily_interest_snapshot = $dailyInterest;
        }

        if ($investment->status === 'cerrada' && !$investment->closed_at) {
            $investment->closed_at = Carbon::now();
        }

        if ($investment->status === 'cerrada' && !$investment->end_date) {
            $investment->end_date = Carbon::now()->toDateString();
        }

        $investment->save();
        AuditLogger::log('Actualizar inversión', $investment, $data);

        return redirect()->route('investments.index')->with('status', 'Inversión actualizada');
    }

    public function destroy(Investment $investment)
    {
        $investment->delete();
        AuditLogger::log('Eliminar inversión', $investment, ['id' => $investment->id]);

        return redirect()->route('investments.index')->with('status', 'Inversión eliminada');
    }

    private function generateCode(Investor $investor): string
    {
        $sequence = $investor->investments()->count() + 1;

        return $investor->id . $sequence;
    }
}
