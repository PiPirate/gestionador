<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use Illuminate\Http\Request;

class InvestmentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Investment::with('investor');

        if ($request->filled('state') && $request->state !== 'todas') {
            $query->where('status', $request->state);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->q . '%')
                    ->orWhereHas('investor', function ($iq) use ($request) {
                        $iq->where('name', 'like', '%' . $request->q . '%');
                    });
            });
        }

        $investments = $query->orderBy('code')->get();

        $summary = [
            'total_usd' => $investments->sum('amount_usd'),
            'avg_return' => round($investments->avg('monthly_rate'), 2),
            'accumulated' => $investments->sum('gains_cop'),
            'next_liquidations' => $investments->count(),
        ];

        return view('modules.investments.index', compact('summary', 'investments'));
    }
}
