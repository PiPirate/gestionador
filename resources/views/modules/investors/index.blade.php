<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Inversores / Lista</p>
                <h1 class="text-2xl font-bold text-gray-900">Inversores</h1>
                <p class="text-sm text-gray-600 mt-1">Gestión de inversores y sus capitales</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center gap-2 border rounded-md px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">⬇️ Exportar</button>
                <button class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">➕ Nuevo Inversor</button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4">
            <div class="flex flex-wrap items-center gap-3">
                <input type="text" placeholder="Buscar por nombre, teléfono o documento" class="flex-1 min-w-[200px] border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                <select class="border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option>Estado: Todos</option>
                    <option>Activos</option>
                    <option>Pendiente</option>
                </select>
                <select class="border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option>Ordenar por</option>
                    <option>Nombre</option>
                    <option>Capital</option>
                </select>
                <button class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-blue-700">🔎 Filtrar</button>
            </div>
        </div>

        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Lista de Inversores</h3>
                <p class="text-sm text-gray-500">Mostrando {{ count($investors) }} inversores</p>
            </div>

            <div class="divide-y divide-gray-100">
                <div class="grid grid-cols-7 text-xs font-semibold text-gray-500 pb-2">
                    <span>Inversor</span>
                    <span>Documento</span>
                    <span>Contacto</span>
                    <span class="text-right">Capital Invertido</span>
                    <span class="text-right">% Mensual</span>
                    <span class="text-right">Ganancias Acum.</span>
                    <span class="text-right">Estado</span>
                </div>
                @foreach ($investors as $investor)
                    <div class="grid grid-cols-7 py-3 text-sm items-center">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $investor['name'] }}</p>
                            <p class="text-xs text-gray-500">Inversor desde {{ $investor['since'] }}</p>
                        </div>
                        <span class="text-gray-700">{{ $investor['document'] }}</span>
                        <div class="text-gray-700">
                            <p>{{ $investor['contact'] }}</p>
                            <p class="text-xs text-gray-500">{{ $investor['phone'] }}</p>
                        </div>
                        <span class="text-right font-semibold text-gray-900">US${{ $investor['capital'] }}</span>
                        <span class="text-right text-green-700 font-semibold">{{ number_format($investor['monthly'], 1) }}%</span>
                        <span class="text-right font-semibold text-gray-900">${{ number_format($investor['gains'], 0, ',', '.') }}</span>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $investor['status'] === 'Pendiente liquidar' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ $investor['status'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-modules.card>
    </x-modules.shell>
</x-app-layout>
