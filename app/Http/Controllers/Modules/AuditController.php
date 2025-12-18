<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

class AuditController extends Controller
{
    public function index()
    {
        $logs = [
            ['action' => 'Inicio de sesión', 'user' => 'María Andrade', 'time' => '15/03/2024 14:30'],
            ['action' => 'Actualizó tasa de compra', 'user' => 'Carlos Ríos', 'time' => '15/03/2024 12:05'],
            ['action' => 'Creó nueva inversión', 'user' => 'Laura Martínez', 'time' => '14/03/2024 09:20'],
        ];

        return view('modules.audit.index', compact('logs'));
    }
}
