<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Liquidation;

class LiquidationsController extends Controller
{
    public function index()
    {
        $pendingQuery = Liquidation::with('investor');

        $pending = (clone $pendingQuery)->where('status', 'pendiente')->get();
        $processedCount = Liquidation::where('status', 'procesada')->count();

        $summary = [
            'pending' => $pending->count(),
            'processed' => $processedCount,
            'total_paid' => Liquidation::where('status', 'procesada')->sum('total_cop'),
            'next_date' => optional($pending->sortBy('due_date')->first())->due_date?->format('d/m') ?? 'N/A',
        ];

        return view('modules.liquidations.index', compact('summary', 'pending'));
    }
}
