<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Investor;
use App\Services\AuditLogger;
use App\Services\InvestmentLifecycleService;
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
        }

        $investments = $query->orderByDesc('start_date')->get();

        $summary = [
            'total_cop' => Investment::where('status', 'activa')->sum('amount_cop'),
            'avg_return' => round(Investment::avg('monthly_rate') ?? 0, 2),
            'accumulated' => $investments->sum(fn (Investment $investment) => $investment->accumulatedGainCop()),
            'next_liquidations' => $investments->where('status', 'pendiente')->count(),
        ];

        $investors = Investor::orderBy('name')->get();

        return view('modules.investments.index', compact('investments', 'summary', 'investors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'amount_cop' => 'required|numeric|min:0',
            'monthly_rate' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pendiente,activa,cerrada',
        ]);

        $investor = Investor::findOrFail($data['investor_id']);
        $code = $this->generateCode($investor);

        $investment = Investment::create([
            'investor_id' => $investor->id,
            'code' => $code,
            'amount_cop' => $data['amount_cop'],
            'monthly_rate' => $data['monthly_rate'],
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
            'monthly_rate' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pendiente,activa,cerrada',
        ]);

        $investment->fill($data);

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
        if ($investment->status === 'cerrada') {
            return redirect()->route('investments.index')
                ->with('status', 'Las inversiones cerradas se mantienen en el historial.');
        }

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
