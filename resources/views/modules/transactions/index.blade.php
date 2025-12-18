<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Operaciones / Transacciones USD</p>
                <h1 class="text-2xl font-bold text-gray-900">Transacciones USD</h1>
                <p class="text-sm text-gray-600 mt-1">Registro de compras y ventas de dólares</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center gap-2 bg-blue-700 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-blue-800">🛒 Nueva Compra</button>
                <button class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">💳 Nueva Venta</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <x-modules.card title="USD Comprados (Mes)">
                <div class="text-3xl font-bold text-gray-900">US${{ $summary['bought'] }}</div>
                <p class="text-xs text-green-600 mt-2">▲ 15.2%</p>
            </x-modules.card>
            <x-modules.card title="USD Vendidos (Mes)">
                <div class="text-3xl font-bold text-gray-900">US${{ $summary['sold'] }}</div>
                <p class="text-xs text-green-600 mt-2">▲ 8.5%</p>
            </x-modules.card>
            <x-modules.card title="Ganancia Neta (Mes)">
                <div class="text-3xl font-bold text-gray-900">${{ number_format($summary['net_profit'], 0, ',', '.') }}</div>
                <p class="text-xs text-green-600 mt-2">▲ 12.5%</p>
            </x-modules.card>
            <x-modules.card title="Inventario USD">
                <div class="text-3xl font-bold text-gray-900">US${{ $summary['inventory'] }}</div>
                <p class="text-xs text-red-500 mt-2">▼ 3.2%</p>
            </x-modules.card>
        </div>

        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Historial de Transacciones</h3>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <button class="px-2 py-1 rounded-md border border-gray-200 hover:bg-gray-50">🔎 Filtrar</button>
                    <button class="px-2 py-1 rounded-md border border-gray-200 hover:bg-gray-50">⬇️ Exportar</button>
                </div>
            </div>

            <div class="grid grid-cols-8 text-xs font-semibold text-gray-500 pb-2">
                <span>Fecha</span>
                <span>Tipo</span>
                <span>Monto USD</span>
                <span>Tasa</span>
                <span>Monto COP</span>
                <span>Contraparte</span>
                <span>Método</span>
                <span>Ganancia</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($history as $tx)
                    <div class="grid grid-cols-8 py-3 text-sm items-center">
                        <span class="text-gray-700">{{ $tx['date'] }}</span>
                        <span>
                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full
                                {{ $tx['type'] === 'Compra' ? 'bg-amber-100 text-amber-700' : ($tx['type'] === 'Venta' ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700') }}">
                                {{ $tx['type'] }}
                            </span>
                        </span>
                        <span class="font-semibold text-gray-900">US${{ $tx['amount'] }}</span>
                        <span class="text-gray-700">$ {{ number_format($tx['rate'], 0, ',', '.') }}</span>
                        <span class="text-gray-900 font-semibold">$ {{ number_format($tx['cop'], 0, ',', '.') }}</span>
                        <span class="text-gray-700">{{ $tx['counterparty'] }}</span>
                        <span class="text-gray-700">{{ $tx['method'] }}</span>
                        <span class="{{ $tx['profit'] > 0 ? 'text-green-700' : 'text-gray-600' }} font-semibold">
                            {{ $tx['profit'] > 0 ? '$ ' . number_format($tx['profit'], 0, ',', '.') : '$ -' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </x-modules.card>
    </x-modules.shell>
</x-app-layout>
