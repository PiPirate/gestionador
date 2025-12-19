<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');

        $rates = [
            'buy' => $settings['rate_buy'] ?? 0,
            'sell' => $settings['rate_sell'] ?? 0,
            'min_margin' => $settings['min_margin'] ?? 0,
            'min_return' => $settings['min_return'] ?? 0,
            'max_return' => $settings['max_return'] ?? 0,
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
