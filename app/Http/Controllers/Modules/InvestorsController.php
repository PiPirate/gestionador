<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\Investment;
use App\Services\AuditLogger;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class InvestorsController extends Controller
{
    public function index(Request $request)
    {
        $query = Investor::query();

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
            'capital_usd' => 'nullable|numeric',
            'monthly_rate' => 'nullable|numeric',
            'status' => 'required|string',
        ]);

        $investor = Investor::create([
            'name' => $data['name'],
            'document' => $data['document'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'capital_usd' => $data['capital_usd'] ?? 0,
            'monthly_rate' => $data['monthly_rate'] ?? 0,
            'status' => $data['status'],
        ]);

        AuditLogger::log('Crear inversor', $investor, $data);

        if (!empty($data['capital_usd']) && $data['capital_usd'] > 0) {
            $this->createInitialInvestment($investor, $data['capital_usd'], $data['monthly_rate'] ?? 0);
        }

        return redirect()->route('investors.index')->with('status', 'Inversor creado');
    }

    public function update(Request $request, Investor $investor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'capital_usd' => 'nullable|numeric',
            'monthly_rate' => 'nullable|numeric',
            'status' => 'required|string',
        ]);

        $investor->update([
            'name' => $data['name'],
            'document' => $data['document'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'capital_usd' => $data['capital_usd'] ?? 0,
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

    private function createInitialInvestment(Investor $investor, float $capitalUsd, float $monthlyRate): void
    {
        $sequence = $investor->investments()->count() + 1;
        $code = $investor->id . $sequence;

        $investment = Investment::create([
            'investor_id' => $investor->id,
            'code' => $code,
            'amount_usd' => $capitalUsd,
            'monthly_rate' => $monthlyRate,
            'start_date' => Carbon::now()->toDateString(),
            'gains_cop' => 0,
            'next_liquidation_date' => null,
            'status' => 'activa',
        ]);

        AuditLogger::log('Crear inversión inicial', $investment, ['investor_id' => $investor->id, 'code' => $code]);
    }
}
