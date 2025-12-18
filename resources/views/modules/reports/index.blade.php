<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Reportes</p>
                <h1 class="text-2xl font-bold text-gray-900">Reportes</h1>
                <p class="text-sm text-gray-600 mt-1">Exporta y consulta la información consolidada.</p>
            </div>
            <button class="inline-flex items-center gap-2 bg-blue-700 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-blue-800">⬇️ Exportar todo</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($reports as $report)
                <x-modules.card :title="$report['title']">
                    <p class="text-sm text-gray-600">{{ $report['description'] }}</p>
                    <div class="mt-3">
                        <button class="text-sm text-blue-700 font-semibold hover:underline">Ver detalle</button>
                    </div>
                </x-modules.card>
            @endforeach
        </div>
    </x-modules.shell>
</x-app-layout>
