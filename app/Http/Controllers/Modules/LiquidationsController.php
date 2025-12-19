<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\Liquidation;
use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LiquidationsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pendiente');

        $liquidationsQuery = Liquidation::with('investor')->orderByDesc('due_date');
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

        return view('modules.liquidations.index', compact('summary', 'liquidations', 'status', 'investors'));
    }

    public function store(Request $request)
    {
        $data = $this->validateLiquidation($request);
        $investor = Investor::findOrFail($data['investor_id']);

        $code = $this->generateCode($investor);
        [$gainCop, $totalCop] = $this->calculateAmounts($data['amount_usd'], $data['monthly_rate']);

        $liquidation = Liquidation::create([
            'investor_id' => $investor->id,
            'code' => $code,
            'amount_usd' => $data['amount_usd'],
            'monthly_rate' => $data['monthly_rate'],
            'period_start' => $data['period_start'],
            'period_end' => $data['period_end'],
            'gain_cop' => $gainCop,
            'total_cop' => $totalCop,
            'status' => $data['status'] ?? 'pendiente',
            'due_date' => $data['due_date'] ?? $data['period_end'],
        ]);

        AuditLogger::log('Crear liquidación', $liquidation, $data);

        return redirect()->route('liquidations.index')->with('status', 'Liquidación creada');
    }

    public function update(Request $request, Liquidation $liquidation)
    {
        $data = $this->validateLiquidation($request, true);
        [$gainCop, $totalCop] = $this->calculateAmounts($data['amount_usd'], $data['monthly_rate']);

        $liquidation->update([
            'investor_id' => $data['investor_id'],
            'amount_usd' => $data['amount_usd'],
            'monthly_rate' => $data['monthly_rate'],
            'period_start' => $data['period_start'],
            'period_end' => $data['period_end'],
            'gain_cop' => $gainCop,
            'total_cop' => $totalCop,
            'status' => $data['status'] ?? $liquidation->status,
            'due_date' => $data['due_date'] ?? $liquidation->due_date,
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

    private function validateLiquidation(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'amount_usd' => 'required|numeric|min:0',
            'monthly_rate' => 'required|numeric|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pendiente,procesada',
        ]);
    }

    private function generateCode(Investor $investor): string
    {
        $sequence = Liquidation::where('investor_id', $investor->id)->count() + 1;
        return 'L' . $investor->id . $sequence;
    }

    private function calculateAmounts(float $amountUsd, float $monthlyRate): array
    {
        $rateSell = (float) (Setting::where('key', 'rate_sell')->value('value') ?? 4000);
        $gainUsd = $amountUsd * ($monthlyRate / 100);
        $gainCop = $gainUsd * $rateSell;
        $totalCop = ($amountUsd + $gainUsd) * $rateSell;

        return [$gainCop, $totalCop];
    }
}
