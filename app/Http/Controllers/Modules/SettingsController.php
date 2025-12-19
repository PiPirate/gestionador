<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'two_factor' => (bool) ($settings['two_factor'] ?? false),
            'timeout' => $settings['timeout'] ?? '30',
            'audit_log' => true,
            'attempts' => $settings['attempts'] ?? 5,
        ];

        $notifications = [
            'liquidations_reminder' => $settings['liquidations_reminder'] ?? '3',
            'new_operation' => (bool) ($settings['notify_new_operation'] ?? true),
        ];

        $users = User::orderBy('name')->get();

        return view('modules.settings.index', compact('rates', 'security', 'notifications', 'users'));
    }

    public function updateRates(Request $request)
    {
        $data = $request->validate([
            'rate_buy' => 'required|numeric',
            'rate_sell' => 'required|numeric',
            'min_margin' => 'required|numeric',
            'min_return' => 'required|numeric',
            'max_return' => 'required|numeric',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        AuditLogger::log('Actualizar tasas', null, $data);

        return redirect()->route('settings.index')->with('status', 'Tasas actualizadas');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'status' => 'required|string',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => $data['status'],
            'last_access_at' => now(),
        ]);

        AuditLogger::log('Crear usuario', $user, $data);

        return redirect()->route('settings.index')->with('status', 'Usuario creado');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string',
            'status' => 'required|string',
        ]);

        $changes = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
        ];

        if (!empty($data['password'])) {
            $changes['password'] = Hash::make($data['password']);
        }

        $user->update($changes);
        AuditLogger::log('Actualizar usuario', $user, $changes);

        return redirect()->route('settings.index')->with('status', 'Usuario actualizado');
    }
}
