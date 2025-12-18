<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Inversiones / Activas</p>
                <h1 class="text-2xl font-bold text-gray-900">Inversiones</h1>
                <p class="text-sm text-gray-600 mt-1">Gestión detallada de todas las inversiones</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center gap-2 border rounded-md px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">⬇️ Exportar</button>
                <button class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">➕ Nueva Inversión</button>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            <div class="flex gap-3 text-sm">
                <button class="px-3 py-2 rounded-md border border-green-600 text-green-700 font-semibold bg-green-50">Activas</button>
                <button class="px-3 py-2 rounded-md border border-gray-200 text-gray-600">Cerradas</button>
                <button class="px-3 py-2 rounded-md border border-gray-200 text-gray-600">Liquidadas</button>
                <button class="px-3 py-2 rounded-md border border-gray-200 text-gray-600">Todas</button>
            </div>
            <div class="flex items-center gap-3 flex-1">
                <input type="text" placeholder="Buscar por inversor o referencia" class="flex-1 min-w-[200px] border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                <select class="border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option>Estado: Activas</option>
                </select>
                <select class="border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option>Ordenar por</option>
                </select>
                <button class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-blue-700">🔎 Filtrar</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <x-modules.card title="Total Invertido (Activo)">
                <div class="text-3xl font-bold text-gray-900">US${{ $summary['total_usd'] }}</div>
                <p class="text-xs text-green-600 mt-2">▲ 15.2%</p>
            </x-modules.card>
            <x-modules.card title="Rendimiento Promedio">
                <div class="text-3xl font-bold text-green-700">{{ $summary['avg_return'] }}%</div>
                <p class="text-xs text-green-600 mt-2">▲ 0.2%</p>
            </x-modules.card>
            <x-modules.card title="Ganancias Acumuladas">
                <div class="text-3xl font-bold text-gray-900">${{ number_format($summary['accumulated'], 0, ',', '.') }}</div>
                <p class="text-xs text-green-600 mt-2">▲ 12.5%</p>
            </x-modules.card>
            <x-modules.card title="Próximas Liquidaciones">
                <div class="text-3xl font-bold text-gray-900">{{ $summary['next_liquidations'] }}</div>
                <p class="text-xs text-gray-500 mt-2">— Este mes</p>
            </x-modules.card>
        </div>

        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Inversiones Activas</h3>
                <p class="text-sm text-gray-500">Mostrando {{ count($investments) }} de 15 inversiones</p>
            </div>

            <div class="grid grid-cols-8 text-xs font-semibold text-gray-500 pb-2">
                <span>Inversión</span>
                <span>Inversor</span>
                <span>Monto USD</span>
                <span>% Mensual</span>
                <span>Fecha Inicio</span>
                <span>Ganancias Acum.</span>
                <span>Próx. Liquidación</span>
                <span>Estado</span>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach ($investments as $investment)
                    <div class="grid grid-cols-8 py-3 text-sm items-center">
                        <span class="font-semibold text-gray-900">{{ $investment['code'] }}</span>
                        <span class="text-gray-700">{{ $investment['investor'] }}</span>
                        <span class="text-gray-900 font-semibold">US${{ $investment['amount_usd'] }}</span>
                        <span class="text-green-700 font-semibold">{{ number_format($investment['monthly'], 1) }}%</span>
                        <span class="text-gray-700">{{ $investment['start'] }}</span>
                        <span class="text-gray-900 font-semibold">${{ number_format($investment['gains'], 0, ',', '.') }}</span>
                        <span class="text-gray-700">{{ $investment['next'] }}</span>
                        <span>
                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">{{ $investment['status'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </x-modules.card>
    </x-modules.shell>
</x-app-layout>
