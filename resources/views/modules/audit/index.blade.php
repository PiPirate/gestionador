<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Auditoría</p>
                <h1 class="text-2xl font-bold text-gray-900">Auditoría</h1>
                <p class="text-sm text-gray-600 mt-1">Registro histórico de acciones del sistema.</p>
            </div>
            <button class="inline-flex items-center gap-2 border rounded-md px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">⬇️ Exportar</button>
        </div>

        <x-modules.card>
            <div class="grid grid-cols-3 text-xs font-semibold text-gray-500 pb-2">
                <span>Acción</span>
                <span>Usuario</span>
                <span>Fecha</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($logs as $log)
                    <div class="grid grid-cols-3 py-3 text-sm items-center">
                        <span class="text-gray-900 font-semibold">{{ $log['action'] }}</span>
                        <span class="text-gray-700">{{ $log['user'] }}</span>
                        <span class="text-gray-600">{{ $log['time'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-modules.card>
    </x-modules.shell>
</x-app-layout>
