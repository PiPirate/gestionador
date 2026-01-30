<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\Investment;
use App\Models\Liquidation;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class LiquidationsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pendiente');

        $liquidationsQuery = Liquidation::with(['investor', 'investment'])->orderByDesc('due_date');
        if ($status !== 'todas') {
            $liquidationsQuery->where('status', $status);
        }

        $liquidations = $liquidationsQuery->get();

        $pending = Liquidation::where('status', 'pendiente')->with('investor')->orderBy('due_date')->get();
        $summary = [
            'pending' => $pending->count(),
            'processed' => Liquidation::where('status', 'procesada')->count(),
            'total_paid' => Liquidation::where('status', 'procesada')->sum('total_cop'),
            'next_date' => optional($pending->first())->due_date?->format('d/m') ?? 'N/A',
        ];

        $investors = Investor::orderBy('name')->get();
        $investments = Investment::with(['investor', 'liquidations'])
            ->orderByDesc('start_date')
            ->get();

        return view('modules.liquidations.index', compact('summary', 'liquidations', 'status', 'investors', 'investments'));
    }

    public function store(Request $request)
    {
        $data = $this->validateLiquidation($request);
        $investment = Investment::with('liquidations')->findOrFail($data['investment_id']);

        if ($investment->investor_id !== (int) $data['investor_id']) {
            throw ValidationException::withMessages([
                'investment_id' => 'La inversión seleccionada no pertenece al inversor.',
            ]);
        }

        [$gainCop, $capitalCop] = $this->resolveWithdrawals($investment, $data);
        $periodDate = $data['due_date'] ?? now()->toDateString();

        $code = $this->generateCode($investment->investor);

        $liquidation = Liquidation::create([
            'investor_id' => $investment->investor_id,
            'investment_id' => $investment->id,
            'code' => $code,
            'amount_usd' => 0,
            'monthly_rate' => $investment->monthly_rate ?? 0,
            'period_start' => $periodDate,
            'period_end' => $periodDate,
            'gain_cop' => $gainCop,
            'withdrawn_gain_cop' => $gainCop,
            'withdrawn_capital_cop' => $capitalCop,
            'total_cop' => $gainCop + $capitalCop,
            'status' => $data['status'] ?? 'pendiente',
            'due_date' => $data['due_date'] ?? $periodDate,
        ]);

        AuditLogger::log('Crear liquidación', $liquidation, $data);

        return redirect()->route('liquidations.index')->with('status', 'Liquidación creada');
    }

    public function update(Request $request, Liquidation $liquidation)
    {
        $data = $this->validateLiquidation($request);
        $investment = Investment::with('liquidations')->findOrFail($data['investment_id']);

        if ($investment->investor_id !== (int) $data['investor_id']) {
            throw ValidationException::withMessages([
                'investment_id' => 'La inversión seleccionada no pertenece al inversor.',
            ]);
        }

        [$gainCop, $capitalCop] = $this->resolveWithdrawals($investment, $data, $liquidation);
        $periodDate = $data['due_date'] ?? $liquidation->due_date ?? now()->toDateString();

        $liquidation->update([
            'investor_id' => $investment->investor_id,
            'investment_id' => $investment->id,
            'amount_usd' => 0,
            'monthly_rate' => $investment->monthly_rate ?? 0,
            'period_start' => $periodDate,
            'period_end' => $periodDate,
            'gain_cop' => $gainCop,
            'withdrawn_gain_cop' => $gainCop,
            'withdrawn_capital_cop' => $capitalCop,
            'total_cop' => $gainCop + $capitalCop,
            'status' => $data['status'] ?? $liquidation->status,
            'due_date' => $data['due_date'] ?? $periodDate,
        ]);

        AuditLogger::log('Actualizar liquidación', $liquidation, $data);

        return redirect()->route('liquidations.index')->with('status', 'Liquidación actualizada');
    }

    public function process(Request $request, Liquidation $liquidation)
    {
        $liquidation->update([
            'status' => 'procesada',
            'due_date' => $liquidation->due_date ?? Carbon::now(),
        ]);

        AuditLogger::log('Procesar liquidación', $liquidation, ['status' => 'procesada']);

        return redirect()->route('liquidations.index')->with('status', 'Liquidación procesada');
    }

    public function destroy(Liquidation $liquidation)
    {
        $liquidation->delete();
        AuditLogger::log('Eliminar liquidación', $liquidation, ['id' => $liquidation->id]);

        return redirect()->route('liquidations.index')->with('status', 'Liquidación eliminada');
    }

    private function validateLiquidation(Request $request): array
    {
        return $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'investment_id' => 'required|exists:investments,id',
            'withdraw_gain_cop' => 'nullable|numeric|min:0',
            'withdraw_capital_cop' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pendiente,procesada',
        ]);
    }

    private function generateCode(Investor $investor): string
    {
        $sequence = Liquidation::where('investor_id', $investor->id)->count() + 1;
        return 'L' . $investor->id . $sequence;
    }

    private function resolveWithdrawals(Investment $investment, array $data, ?Liquidation $liquidation = null): array
    {
        $gain = (float) ($data['withdraw_gain_cop'] ?? 0);
        $capital = (float) ($data['withdraw_capital_cop'] ?? 0);

        $availableGain = $investment->availableGainCop();
        $availableCapital = $investment->availableCapitalCop();

        if ($liquidation && $liquidation->investment_id === $investment->id) {
            $availableGain += (float) $liquidation->withdrawn_gain_cop;
            $availableCapital += (float) $liquidation->withdrawn_capital_cop;
        }

        if ($gain <= 0 && $capital <= 0) {
            throw ValidationException::withMessages([
                'withdraw_gain_cop' => 'Debes indicar un retiro de ganancias o capital.',
            ]);
        }

        if ($gain > $availableGain) {
            throw ValidationException::withMessages([
                'withdraw_gain_cop' => 'El retiro de ganancias supera el disponible.',
            ]);
        }

        if ($capital > $availableCapital) {
            throw ValidationException::withMessages([
                'withdraw_capital_cop' => 'El retiro de capital supera el disponible.',
            ]);
        }

        if ($capital > 0 && abs($capital - $availableCapital) > 0.01) {
            throw ValidationException::withMessages([
                'withdraw_capital_cop' => 'El retiro de capital debe ser por el total disponible.',
            ]);
        }

        return [round($gain, 2), round($capital, 2)];
    }
}
