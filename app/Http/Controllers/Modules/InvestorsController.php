<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\ProfitRule;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class InvestorsController extends Controller
{
    public function index(Request $request)
    {
        $query = Investor::query()->with('investments');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('document', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%')
                    ->orWhere('phone', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        $investors = $query->orderBy('name')->get();

        return view('modules.investors.index', compact('investors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'monthly_rate' => 'nullable|numeric',
            'status' => 'required|string',
        ]);

        $investor = Investor::create([
            'name' => $data['name'],
            'document' => $data['document'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'monthly_rate' => $data['monthly_rate'] ?? 0,
            'status' => $data['status'],
        ]);

        AuditLogger::log('Crear inversor', $investor, $data);

        return redirect()->route('investors.index')->with('status', 'Inversor creado');
    }

    public function update(Request $request, Investor $investor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'monthly_rate' => 'nullable|numeric',
            'status' => 'required|string',
        ]);

        $investor->update([
            'name' => $data['name'],
            'document' => $data['document'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'monthly_rate' => $data['monthly_rate'] ?? 0,
            'status' => $data['status'],
        ]);
        AuditLogger::log('Actualizar inversor', $investor, $data);

        return redirect()->route('investors.index')->with('status', 'Inversor actualizado');
    }

    public function destroy(Investor $investor)
    {
        $investor->delete();
        AuditLogger::log('Eliminar inversor', $investor, ['id' => $investor->id]);

        return redirect()->route('investors.index')->with('status', 'Inversor eliminado');
    }

    public function show(Investor $investor)
    {
        app(\App\Services\InvestmentLifecycleService::class)->closeExpiredInvestments();
        $investor->load('investments');
        $investments = $investor->investments->sortByDesc('start_date');
        $continuableInvestments = $investor->investments
            ->where('status', '!=', 'cerrada')
            ->sortByDesc('start_date');

        $summary = [
            'monthly_gains' => $investor->investments->sum(fn ($investment) => $investment->gainForMonthToDate()),
            'total_withdrawn' => $investor->totalWithdrawnCop(),
            'total_gains' => $investor->totalGainsCop(),
            'total_days' => $investor->totalDaysInvested(),
            'capital_in_circulation' => $investor->capital_in_circulation(),

        ];

        $activeProfitRule = ProfitRule::where('is_active', true)->first();
        $profitRules = ProfitRule::orderByDesc('created_at')->get();

        return view('modules.investors.show', compact('investor', 'investments', 'summary', 'continuableInvestments', 'activeProfitRule', 'profitRules'));
    }
}
