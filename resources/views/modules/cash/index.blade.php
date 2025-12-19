<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Operaciones / Caja / Balance</p>
                <h1 class="text-2xl font-bold text-gray-900">Caja y Balance</h1>
                <p class="text-sm text-gray-600 mt-1">Control de efectivo y cuentas</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center gap-2 border rounded-md px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">📄 Estado de Cuenta</button>
                <button class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">➕ Nuevo Movimiento</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <x-modules.card title="Ingresos del Mes">
                <div class="text-3xl font-bold text-gray-900">$ {{ number_format($summary['income'], 0, ',', '.') }}</div>
                <p class="text-xs text-gray-600 mt-2">Ventas USD: $ {{ number_format($summary['income_breakdown']['ventas'], 0, ',', '.') }} · Inversiones: $ {{ number_format($summary['income_breakdown']['inversiones'], 0, ',', '.') }}</p>
            </x-modules.card>
            <x-modules.card title="Egresos del Mes">
                <div class="text-3xl font-bold text-gray-900">$ {{ number_format($summary['expenses'], 0, ',', '.') }}</div>
                <p class="text-xs text-gray-600 mt-2">Compras USD: $ {{ number_format($summary['expenses_breakdown']['compras'], 0, ',', '.') }} · Liquidaciones: $ {{ number_format($summary['expenses_breakdown']['liquidaciones'], 0, ',', '.') }}</p>
            </x-modules.card>
            <x-modules.card title="Saldo Neto">
                <div class="text-3xl font-bold text-gray-900">$ {{ number_format($summary['net'], 0, ',', '.') }}</div>
                <p class="text-xs text-gray-600 mt-2">Flujo neto del mes</p>
            </x-modules.card>
        </div>

        <x-modules.card class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Movimientos Recientes</h3>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <button class="px-2 py-1 rounded-md border border-gray-200 hover:bg-gray-50">🔎 Filtrar</button>
                    <button class="px-2 py-1 rounded-md border border-gray-200 hover:bg-gray-50">⬇️ Exportar</button>
                </div>
            </div>

            <div class="grid grid-cols-6 text-xs font-semibold text-gray-500 pb-2">
                <span>Fecha</span>
                <span>Tipo</span>
                <span>Descripción</span>
                <span class="text-right">Monto COP</span>
                <span class="text-right">Saldo COP</span>
                <span>Referencia</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($movements as $movement)
                    <div class="grid grid-cols-6 py-3 text-sm items-center">
                        <span class="text-gray-700">{{ $movement['date'] }}</span>
                        <span>
                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $movement['type'] === 'Ingreso' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $movement['type'] }}
                            </span>
                        </span>
                        <span class="text-gray-700">{{ $movement['description'] }}</span>
                        <span class="text-right font-semibold {{ $movement['cop'] < 0 ? 'text-red-600' : 'text-gray-900' }}">
                            $ {{ number_format($movement['cop'], 0, ',', '.') }}
                        </span>
                        <span class="text-right text-gray-900 font-semibold">$ {{ number_format($movement['balance'], 0, ',', '.') }}</span>
                        <span class="text-gray-600">{{ $movement['reference'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-modules.card>

        <x-modules.card>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Balance por Cuentas</h3>
            <div class="grid grid-cols-5 text-xs font-semibold text-gray-500 pb-2">
                <span>Cuenta</span>
                <span>Tipo</span>
                <span class="text-right">Saldo COP</span>
                <span class="text-right">Saldo USD</span>
                <span>Última actualización</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($accounts as $account)
                    <div class="grid grid-cols-5 py-3 text-sm items-center">
                        <span class="text-gray-900 font-semibold">{{ $account['name'] }}</span>
                        <span class="text-gray-700">{{ $account['type'] }}</span>
                        <span class="text-right text-gray-900 font-semibold">$ {{ number_format($account['cop'], 0, ',', '.') }}</span>
                        <span class="text-right text-gray-900 font-semibold">US$ {{ $account['usd'] }}</span>
                        <span class="text-gray-600">{{ $account['updated_at'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-modules.card>
    </x-modules.shell>
</x-app-layout>
