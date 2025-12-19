<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\CashMovement;
use App\Models\Investor;
use App\Models\Investment;
use App\Models\Liquidation;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;

class ModuleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'María Andrade',
            'email' => 'maria@dolarmanager.com',
        ]);

        $investors = collect([
            [
                'name' => 'Juan Pérez',
                'document' => '10.234.567-8',
                'email' => 'juan.perez@email.com',
                'phone' => '+57 300 123 4567',
                'since' => '2023-02-15',
                'status' => 'activo',
                'capital_usd' => 5,
                'monthly_rate' => 4.5,
                'gains_cop' => 2025000,
            ],
            [
                'name' => 'Ana Gómez',
                'document' => '20.345.678-9',
                'email' => 'ana.gomez@email.com',
                'phone' => '+57 310 234 6789',
                'since' => '2023-05-22',
                'status' => 'activo',
                'capital_usd' => 4,
                'monthly_rate' => 4.0,
                'gains_cop' => 1120000,
            ],
            [
                'name' => 'Sofía Ramírez',
                'document' => '30.456.789-0',
                'email' => 'sofia.ramirez@email.com',
                'phone' => '+57 320 345 6789',
                'since' => '2022-11-10',
                'status' => 'pendiente_liquidar',
                'capital_usd' => 8,
                'monthly_rate' => 4.8,
                'gains_cop' => 3840000,
            ],
        ])->map(fn ($data) => Investor::create($data));

        $investments = [
            [
                'investor_id' => $investors[0]->id,
                'code' => 'INV-2024-001',
                'amount_usd' => 5,
                'monthly_rate' => 4.5,
                'start_date' => '2024-02-15',
                'gains_cop' => 2025000,
                'next_liquidation_date' => '2024-04-15',
                'status' => 'activa',
            ],
            [
                'investor_id' => $investors[1]->id,
                'code' => 'INV-2024-002',
                'amount_usd' => 4,
                'monthly_rate' => 4.0,
                'start_date' => '2024-09-22',
                'gains_cop' => 1120000,
                'next_liquidation_date' => '2024-04-22',
                'status' => 'activa',
            ],
            [
                'investor_id' => $investors[2]->id,
                'code' => 'INV-2024-003',
                'amount_usd' => 8,
                'monthly_rate' => 4.8,
                'start_date' => '2024-02-10',
                'gains_cop' => 1536000,
                'next_liquidation_date' => '2024-03-10',
                'status' => 'activa',
            ],
        ];
        foreach ($investments as $data) {
            Investment::create($data);
        }

        $transactions = [
            [
                'type' => 'compra',
                'amount_usd' => 5,
                'rate' => 3810,
                'amount_cop' => 19050000,
                'counterparty' => 'Cambios Colombia S.A.',
                'method' => 'Transferencia',
                'profit_cop' => 0,
                'transacted_at' => '2024-03-15',
                'reference' => 'TRX-458720',
            ],
            [
                'type' => 'venta',
                'amount_usd' => 3,
                'rate' => 3880,
                'amount_cop' => 12416000,
                'counterparty' => 'Importadora Andina',
                'method' => 'Efectivo',
                'profit_cop' => 224000,
                'transacted_at' => '2024-03-14',
                'reference' => 'TRX-458721',
            ],
            [
                'type' => 'compra',
                'amount_usd' => 3,
                'rate' => 3805,
                'amount_cop' => 9512500,
                'counterparty' => 'Particular (Carlos M.)',
                'method' => 'PSE',
                'profit_cop' => 0,
                'transacted_at' => '2024-03-13',
                'reference' => 'TRX-458722',
            ],
            [
                'type' => 'venta',
                'amount_usd' => 5,
                'rate' => 3875,
                'amount_cop' => 18600000,
                'counterparty' => 'Exportadora Caribe',
                'method' => 'Transferencia',
                'profit_cop' => 360000,
                'transacted_at' => '2024-03-12',
                'reference' => 'TRX-458723',
            ],
            [
                'type' => 'inversion',
                'amount_usd' => 4,
                'rate' => 3820,
                'amount_cop' => 13370000,
                'counterparty' => 'Ana Gómez (Inversor)',
                'method' => 'Transferencia',
                'profit_cop' => 0,
                'transacted_at' => '2024-03-10',
                'reference' => 'INV-2024-002',
            ],
        ];
        foreach ($transactions as $data) {
            Transaction::create($data);
        }

        $cashMovements = [
            [
                'date' => '2024-03-15',
                'type' => 'ingreso',
                'description' => 'Venta USD a Importadora Andina',
                'amount_cop' => 12416000,
                'amount_usd' => 0,
                'balance_cop' => 42560000,
                'balance_usd' => 0,
                'reference' => 'TRX-458720',
            ],
            [
                'date' => '2024-03-14',
                'type' => 'egreso',
                'description' => 'Liquidación a Sofía Ramírez',
                'amount_cop' => -4224000,
                'amount_usd' => 0,
                'balance_cop' => 30144000,
                'balance_usd' => 0,
                'reference' => 'LIQ-2024-003',
            ],
            [
                'date' => '2024-03-13',
                'type' => 'ingreso',
                'description' => 'Inversión de Ana Gómez',
                'amount_cop' => 13370000,
                'amount_usd' => 0,
                'balance_cop' => 34368000,
                'balance_usd' => 0,
                'reference' => 'INV-2024-002',
            ],
            [
                'date' => '2024-03-12',
                'type' => 'egreso',
                'description' => 'Compra USD a Cambios Colombia',
                'amount_cop' => -19050000,
                'amount_usd' => 0,
                'balance_cop' => 20998000,
                'balance_usd' => 0,
                'reference' => 'TRX-458712',
            ],
        ];
        foreach ($cashMovements as $movement) {
            CashMovement::create($movement);
        }

        $accounts = [
            ['name' => 'Bancolombia Ahorros', 'type' => 'Bancaria', 'balance_cop' => 24560000, 'balance_usd' => 8, 'last_synced_at' => '2024-03-15 10:00:00'],
            ['name' => 'Davivienda Corriente', 'type' => 'Bancaria', 'balance_cop' => 18000000, 'balance_usd' => 2, 'last_synced_at' => '2024-03-14 10:00:00'],
            ['name' => 'Caja Fisica Principal', 'type' => 'Efectivo', 'balance_cop' => 8450000, 'balance_usd' => 5, 'last_synced_at' => '2024-03-15 10:00:00'],
        ];
        foreach ($accounts as $account) {
            Account::create($account);
        }

        $liquidations = [
            [
                'investor_id' => $investors[0]->id,
                'code' => 'LIQ-2024-001',
                'amount_usd' => 5,
                'monthly_rate' => 4.5,
                'period_start' => '2024-02-15',
                'period_end' => '2024-03-15',
                'gain_cop' => 857250,
                'total_cop' => 857250,
                'status' => 'pendiente',
                'due_date' => '2024-04-15',
            ],
            [
                'investor_id' => $investors[1]->id,
                'code' => 'LIQ-2024-002',
                'amount_usd' => 4,
                'monthly_rate' => 4.0,
                'period_start' => '2024-02-22',
                'period_end' => '2024-03-22',
                'gain_cop' => 532000,
                'total_cop' => 532000,
                'status' => 'pendiente',
                'due_date' => '2024-04-22',
            ],
            [
                'investor_id' => $investors[2]->id,
                'code' => 'LIQ-2024-003',
                'amount_usd' => 8,
                'monthly_rate' => 4.8,
                'period_start' => '2024-02-10',
                'period_end' => '2024-03-10',
                'gain_cop' => 1536000,
                'total_cop' => 1536000,
                'status' => 'procesada',
                'due_date' => '2024-04-10',
            ],
        ];
        foreach ($liquidations as $liquidation) {
            Liquidation::create($liquidation);
        }

        $settings = [
            'trm_today' => 3850,
            'rate_buy' => 3820,
            'rate_sell' => 3880,
            'min_margin' => 50,
            'min_return' => 3.5,
            'max_return' => 5.0,
        ];
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $notifications = [
            ['title' => 'Liquidación pendiente', 'body' => 'Tienes 3 liquidaciones pendientes este mes.'],
            ['title' => 'Nuevo inversor', 'body' => 'Se registró el inversor Carlos Ríos con capital inicial US$2.'],
            ['title' => 'Alerta de tasa', 'body' => 'La tasa de venta superó el umbral configurado.'],
        ];

        foreach ($notifications as $data) {
            DatabaseNotification::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'type' => 'app',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => $data,
            ]);
        }
    }
}
