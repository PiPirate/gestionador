<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

class CashController extends Controller
{
    public function index()
    {
        $summary = [
            'income' => 42560000,
            'income_breakdown' => [
                'ventas' => 30416000,
                'inversiones' => 12144000,
            ],
            'expenses' => 34110000,
            'expenses_breakdown' => [
                'compras' => 28562500,
                'liquidaciones' => 5547500,
            ],
            'net' => 8450000,
        ];

        $movements = [
            [
                'date' => '15/03/2024',
                'type' => 'Ingreso',
                'description' => 'Venta USD a Importadora Andina',
                'cop' => 12416000,
                'balance' => 42560000,
                'reference' => 'TRX-458720',
            ],
            [
                'date' => '14/03/2024',
                'type' => 'Egreso',
                'description' => 'Liquidación a Sofía Ramírez',
                'cop' => -4224000,
                'balance' => 30144000,
                'reference' => 'LIQ-2024-003',
            ],
            [
                'date' => '13/03/2024',
                'type' => 'Ingreso',
                'description' => 'Inversión de Ana Gómez',
                'cop' => 13370000,
                'balance' => 34368000,
                'reference' => 'INV-2024-002',
            ],
            [
                'date' => '12/03/2024',
                'type' => 'Egreso',
                'description' => 'Compra USD a Cambios Colombia',
                'cop' => -19050000,
                'balance' => 20998000,
                'reference' => 'TRX-458712',
            ],
        ];

        $accounts = [
            ['name' => 'Bancolombia Ahorros', 'type' => 'Bancaria', 'cop' => 24560000, 'usd' => 8, 'updated_at' => '15/03/2024'],
            ['name' => 'Davivienda Corriente', 'type' => 'Bancaria', 'cop' => 18000000, 'usd' => 2, 'updated_at' => '14/03/2024'],
            ['name' => 'Caja Fisica Principal', 'type' => 'Efectivo', 'cop' => 8450000, 'usd' => 5, 'updated_at' => '15/03/2024'],
        ];

        return view('modules.cash.index', compact('summary', 'movements', 'accounts'));
    }
}
