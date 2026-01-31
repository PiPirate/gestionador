<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\ProfitRule;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProfitRulesController extends Controller
{
    /**
     * @return array<int, array{upTo: float|int|null, rate: float|int}>
     */
    private function parseTiers(array $data): array
    {
        $tiers = [];
        if (!empty($data['tiers_json'])) {
            $decoded = json_decode($data['tiers_json'], true);
            if (!is_array($decoded)) {
                throw ValidationException::withMessages(['tiers_json' => 'El JSON de tramos no es válido.']);
            }
            $tiers = $decoded;
        } else {
            $upTos = $data['tiers_up_to'] ?? [];
            $rates = $data['tiers_rate'] ?? [];
            foreach ($rates as $index => $rate) {
                $upTo = $upTos[$index] ?? null;
                $tiers[] = [
                    'upTo' => $upTo === '' ? null : $upTo,
                    'rate' => (float) $rate,
                ];
            }
        }

        if (empty($tiers)) {
            throw ValidationException::withMessages(['tiers_json' => 'Debes definir al menos un tramo.']);
        }

        return $tiers;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tiers_json' => 'nullable|string',
            'tiers_up_to' => 'nullable|array',
            'tiers_up_to.*' => 'nullable|numeric|min:0',
            'tiers_rate' => 'nullable|array',
            'tiers_rate.*' => 'nullable|numeric|min:0',
        ]);

        $tiers = $this->parseTiers($data);

        $rule = ProfitRule::create([
            'tiers_json' => $tiers,
            'is_active' => false,
        ]);

        AuditLogger::log('Crear regla de rentabilidad', $rule, ['tiers' => $tiers]);

        return redirect()->route('settings.index')->with('status', 'Regla de rentabilidad creada');
    }

    public function activate(ProfitRule $profitRule)
    {
        DB::transaction(function () use ($profitRule) {
            ProfitRule::where('is_active', true)->update(['is_active' => false]);
            $profitRule->update(['is_active' => true]);
        });

        AuditLogger::log('Activar regla de rentabilidad', $profitRule, ['id' => $profitRule->id]);

        return redirect()->route('settings.index')->with('status', 'Regla de rentabilidad activada');
    }

    public function deactivate(ProfitRule $profitRule)
    {
        if (ProfitRule::where('is_active', true)->count() <= 1 && $profitRule->is_active) {
            return redirect()->route('settings.index')->withErrors([
                'profit_rule' => 'Debe existir al menos una regla activa.',
            ]);
        }

        $profitRule->update(['is_active' => false]);

        AuditLogger::log('Desactivar regla de rentabilidad', $profitRule, ['id' => $profitRule->id]);

        return redirect()->route('settings.index')->with('status', 'Regla de rentabilidad desactivada');
    }

    public function update(Request $request, ProfitRule $profitRule)
    {
        $data = $request->validate([
            'tiers_json' => 'nullable|string',
            'tiers_up_to' => 'nullable|array',
            'tiers_up_to.*' => 'nullable|numeric|min:0',
            'tiers_rate' => 'nullable|array',
            'tiers_rate.*' => 'nullable|numeric|min:0',
        ]);

        $tiers = $this->parseTiers($data);

        $profitRule->update([
            'tiers_json' => $tiers,
        ]);

        $profitRule->investments()
            ->select(['id', 'amount_cop', 'start_date', 'end_date'])
            ->chunkById(200, function ($investments) use ($tiers) {
                foreach ($investments as $investment) {
                    $monthlyProfit = \App\Services\ProfitRuleCalculator::calcMonthlyProfit(
                        (float) $investment->amount_cop,
                        $tiers
                    );
                    $monthReference = $investment->end_date ?? $investment->start_date;
                    $monthDays = $monthReference ? $monthReference->daysInMonth : 0;
                    $dailyInterest = $monthDays > 0 ? $monthlyProfit / $monthDays : 0;
                    $effectiveRate = $investment->amount_cop > 0
                        ? ($monthlyProfit / $investment->amount_cop) * 100
                        : 0;

                    $investment->update([
                        'tiers_snapshot' => $tiers,
                        'monthly_profit_snapshot' => $monthlyProfit,
                        'daily_interest_snapshot' => $dailyInterest,
                        'monthly_rate' => $effectiveRate,
                    ]);
                }
            });

        AuditLogger::log('Actualizar regla de rentabilidad', $profitRule, ['tiers' => $tiers]);

        return redirect()->route('settings.index')->with('status', 'Regla de rentabilidad actualizada');
    }

    public function destroy(ProfitRule $profitRule)
    {
        if (ProfitRule::count() <= 1) {
            return redirect()->route('settings.index')->withErrors([
                'profit_rule' => 'No puedes eliminar la única regla existente.',
            ]);
        }

        if ($profitRule->is_active) {
            $nextRule = ProfitRule::where('id', '!=', $profitRule->id)
                ->orderByDesc('created_at')
                ->first();
            if ($nextRule) {
                $nextRule->update(['is_active' => true]);
            }
        }

        $profitRule->delete();

        AuditLogger::log('Eliminar regla de rentabilidad', $profitRule, ['id' => $profitRule->id]);

        return redirect()->route('settings.index')->with('status', 'Regla de rentabilidad eliminada');
    }
}
