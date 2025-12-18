<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        $rates = [
            'buy' => 3820,
            'sell' => 3880,
            'min_margin' => 50,
            'min_return' => 3.5,
            'max_return' => 5.0,
        ];

        $security = [
            'two_factor' => true,
            'timeout' => '30 minutos',
            'audit_log' => true,
            'attempts' => 5,
        ];

        $notifications = [
            'liquidations_reminder' => '3 días antes',
            'new_operation' => true,
        ];

        $users = [
            ['name' => 'María Andrade', 'role' => 'Administrador', 'email' => 'maria@dolarmanager.com', 'last_access' => '15/03/2024 14:30', 'status' => 'Activo'],
            ['name' => 'Carlos Ríos', 'role' => 'Operador', 'email' => 'carlos@dolarmanager.com', 'last_access' => '15/03/2024 11:15', 'status' => 'Activo'],
            ['name' => 'Laura Martínez', 'role' => 'Consulta', 'email' => 'laura@dolarmanager.com', 'last_access' => '14/03/2024 09:20', 'status' => 'Activo'],
        ];

        return view('modules.settings.index', compact('rates', 'security', 'notifications', 'users'));
    }
}
