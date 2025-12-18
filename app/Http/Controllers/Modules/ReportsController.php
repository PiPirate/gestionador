<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    public function index()
    {
        $reports = [
            ['title' => 'Resumen mensual', 'description' => 'Ingresos, egresos y ganancias consolidadas.'],
            ['title' => 'Rendimiento de inversores', 'description' => 'Rentabilidad por inversor y comparativo histórico.'],
            ['title' => 'Movimientos detallados', 'description' => 'Exportable a CSV y PDF con filtros dinámicos.'],
        ];

        return view('modules.reports.index', compact('reports'));
    }
}
