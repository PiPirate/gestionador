<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    public function index()
    {
        $summary = [
            'bought' => 25,
            'sold' => 19,
            'net_profit' => 2330000,
            'inventory' => 15,
        ];

        $history = [
            [
                'date' => '15/03/2024',
                'type' => 'Compra',
                'amount' => 5,
                'rate' => 3810,
                'cop' => 19050000,
                'counterparty' => 'Cambios Colombia S.A.',
                'method' => 'Transferencia',
                'profit' => 0,
            ],
            [
                'date' => '14/03/2024',
                'type' => 'Venta',
                'amount' => 3,
                'rate' => 3880,
                'cop' => 12416000,
                'counterparty' => 'Importadora Andina',
                'method' => 'Efectivo',
                'profit' => 224000,
            ],
            [
                'date' => '13/03/2024',
                'type' => 'Compra',
                'amount' => 3,
                'rate' => 3805,
                'cop' => 9512500,
                'counterparty' => 'Particular (Carlos M.)',
                'method' => 'PSE',
                'profit' => 0,
            ],
            [
                'date' => '12/03/2024',
                'type' => 'Venta',
                'amount' => 5,
                'rate' => 3875,
                'cop' => 18600000,
                'counterparty' => 'Exportadora Caribe',
                'method' => 'Transferencia',
                'profit' => 360000,
            ],
            [
                'date' => '10/03/2024',
                'type' => 'Inversión',
                'amount' => 4,
                'rate' => 3820,
                'cop' => 13370000,
                'counterparty' => 'Ana Gómez (Inversor)',
                'method' => 'Transferencia',
                'profit' => 0,
            ],
        ];

        return view('modules.transactions.index', compact('summary', 'history'));
    }
}
