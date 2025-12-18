<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

class LiquidationsController extends Controller
{
    public function index()
    {
        $summary = [
            'pending' => 3,
            'processed' => 5,
            'total_paid' => 6120000,
            'next_date' => '15/04',
        ];

        $pending = [
            [
                'code' => 'LIQ-2024-001',
                'investor' => 'Juan Pérez',
                'amount_usd' => 5,
                'monthly' => 4.5,
                'period' => '15/02 - 15/03',
                'gain' => 857250,
                'total' => 857250,
                'status' => 'Pendiente',
                'due' => '15/04/2024',
            ],
            [
                'code' => 'LIQ-2024-002',
                'investor' => 'Ana Gómez',
                'amount_usd' => 4,
                'monthly' => 4.0,
                'period' => '22/02 - 22/03',
                'gain' => 532000,
                'total' => 532000,
                'status' => 'Pendiente',
                'due' => '22/04/2024',
            ],
            [
                'code' => 'LIQ-2024-003',
                'investor' => 'Sofía Ramírez',
                'amount_usd' => 8,
                'monthly' => 4.8,
                'period' => '10/02 - 10/03',
                'gain' => 1536000,
                'total' => 1536000,
                'status' => 'Procesada',
                'due' => '10/04/2024',
            ],
        ];

        return view('modules.liquidations.index', compact('summary', 'pending'));
    }
}
