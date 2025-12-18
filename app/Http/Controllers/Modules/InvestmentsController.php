<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

class InvestmentsController extends Controller
{
    public function index()
    {
        $summary = [
            'total_usd' => 19,
            'avg_return' => 4.3,
            'accumulated' => 7697500,
            'next_liquidations' => 5,
        ];

        $investments = [
            [
                'code' => 'INV-2024-001',
                'investor' => 'Juan Pérez',
                'amount_usd' => 5,
                'monthly' => 4.5,
                'start' => '15/02/2024',
                'gains' => 2025000,
                'next' => '15/04/2024',
                'status' => 'Activa',
            ],
            [
                'code' => 'INV-2024-002',
                'investor' => 'Ana Gómez',
                'amount_usd' => 4,
                'monthly' => 4.0,
                'start' => '22/09/2024',
                'gains' => 1120000,
                'next' => '22/04/2024',
                'status' => 'Activa',
            ],
            [
                'code' => 'INV-2024-003',
                'investor' => 'Sofía Ramírez',
                'amount_usd' => 8,
                'monthly' => 4.8,
                'start' => '10/02/2024',
                'gains' => 1536000,
                'next' => '10/03/2024',
                'status' => 'Activa',
            ],
        ];

        return view('modules.investments.index', compact('summary', 'investments'));
    }
}
