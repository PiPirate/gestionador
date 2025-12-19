<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Operaciones / Liquidaciones</p>
                <h1 class="text-2xl font-bold text-gray-900">Liquidaciones</h1>
                <p class="text-sm text-gray-600 mt-1">Pago de rendimientos a inversores</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center gap-2 border rounded-md px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">⬇️ Exportar</button>
                <button class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">➕ Nueva Liquidación</button>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            <div class="flex gap-3 text-sm">
                <button class="px-3 py-2 rounded-md border border-green-600 text-green-700 font-semibold bg-green-50">Pendientes</button>
                <button class="px-3 py-2 rounded-md border border-gray-200 text-gray-600">Procesadas</button>
                <button class="px-3 py-2 rounded-md border border-gray-200 text-gray-600">Todas</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <x-modules.card title="Pendientes">
                <div class="text-3xl font-bold text-gray-900">{{ $summary['pending'] }}</div>
                <p class="text-xs text-gray-500 mt-2">— Este mes</p>
            </x-modules.card>
            <x-modules.card title="Procesadas (Mes)">
                <div class="text-3xl font-bold text-gray-900">{{ $summary['processed'] }}</div>
                <p class="text-xs text-green-600 mt-2">▲ 25%</p>
            </x-modules.card>
            <x-modules.card title="Total Pagado (Mes)">
                <div class="text-3xl font-bold text-gray-900">$ {{ number_format($summary['total_paid'], 0, ',', '.') }}</div>
                <p class="text-xs text-green-600 mt-2">▲ 15.2%</p>
            </x-modules.card>
            <x-modules.card title="Próxima Fecha">
                <div class="text-3xl font-bold text-gray-900">{{ $summary['next_date'] }}</div>
                <p class="text-xs text-gray-500 mt-2">⌛ 15 días</p>
            </x-modules.card>
        </div>

        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Liquidaciones Pendientes</h3>
                <button class="inline-flex items-center gap-2 bg-green-600 text-white px-3 py-2 rounded-md text-xs shadow-sm hover:bg-green-700">⚙️ Procesar Todas</button>
            </div>

            <div class="grid grid-cols-8 text-xs font-semibold text-gray-500 pb-2">
                <span>Liquidación</span>
                <span>Inversor</span>
                <span>Inversión</span>
                <span>% Mensual</span>
                <span>Período</span>
                <span>Ganancias</span>
                <span>Total a pagar</span>
                <span>Estado</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($pending as $row)
                    <div class="grid grid-cols-8 py-3 text-sm items-center">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $row['code'] }}</p>
                            <p class="text-xs text-gray-500">Vence: {{ $row['due'] }}</p>
                        </div>
                        <span class="text-gray-700">{{ $row['investor'] }}</span>
                        <span class="text-gray-700">US$ {{ $row['amount_usd'] }}</span>
                        <span class="text-green-700 font-semibold">{{ number_format($row['monthly'], 1) }}%</span>
                        <span class="text-gray-700">{{ $row['period'] }}</span>
                        <span class="text-gray-900 font-semibold">$ {{ number_format($row['gain'], 0, ',', '.') }}</span>
                        <span class="text-gray-900 font-semibold">$ {{ number_format($row['total'], 0, ',', '.') }}</span>
                        <span>
                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $row['status'] === 'Pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ $row['status'] }}
                            </span>
                        </span>
                    </div>
                @endforeach
            </div>
        </x-modules.card>
    </x-modules.shell>
</x-app-layout>
