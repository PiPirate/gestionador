<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

class InvestorsController extends Controller
{
    public function index()
    {
        $investors = [
            [
                'name' => 'Juan Pérez',
                'document' => '10.234.567-8',
                'contact' => 'juan.perez@email.com',
                'phone' => '+57 300 123 4567',
                'since' => '15/02/2023',
                'capital' => 5,
                'monthly' => 4.5,
                'gains' => 2025000,
                'status' => 'Activo',
            ],
            [
                'name' => 'Ana Gómez',
                'document' => '20.345.678-9',
                'contact' => 'ana.gomez@email.com',
                'phone' => '+57 310 234 6789',
                'since' => '22/05/2023',
                'capital' => 4,
                'monthly' => 4.0,
                'gains' => 1120000,
                'status' => 'Activo',
            ],
            [
                'name' => 'Sofía Ramírez',
                'document' => '30.456.789-0',
                'contact' => 'sofia.ramirez@email.com',
                'phone' => '+57 320 345 6789',
                'since' => '10/11/2022',
                'capital' => 8,
                'monthly' => 4.8,
                'gains' => 3840000,
                'status' => 'Pendiente liquidar',
            ],
        ];

        return view('modules.investors.index', compact('investors'));
    }
}
