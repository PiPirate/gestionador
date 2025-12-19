<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Transaction;

class ReportsController extends Controller
{
    public function index()
    {
        $reports = [
            [
                'title' => 'Resumen mensual',
                'description' => 'Ingresos, egresos y ganancias consolidadas.',
                'figure' => Transaction::sum('profit_cop'),
            ],
            [
                'title' => 'Rendimiento de inversores',
                'description' => 'Rentabilidad por inversor y comparativo histórico.',
                'figure' => Investment::avg('monthly_rate'),
            ],
            [
                'title' => 'Movimientos detallados',
                'description' => 'Exportable a CSV y PDF con filtros dinámicos.',
                'figure' => Transaction::count(),
            ],
        ];

        return view('modules.reports.index', compact('reports'));
    }
}
