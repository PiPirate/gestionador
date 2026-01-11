<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Inversores / Lista</p>
                <h1 class="text-2xl font-bold text-gray-900">Inversores</h1>
                <p class="text-sm text-gray-600 mt-1">Gestión de inversores y sus inversiones</p>
            </div>
            <div class="flex items-center gap-3">
                <select class="border rounded-md px-3 py-2 text-sm" data-table-sort data-sort-column="0">
                    <option value="asc">Ordenar A → Z</option>
                    <option value="desc">Ordenar Z → A</option>
                </select>
                <button data-modal-target="investor-create"
                    class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    <span>Nuevo Inversor</span>
                </button>
            </div>
        </div>
        <x-modules.card id="investors-table" data-table-root>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Lista de Inversores</h3>
                <p class="text-sm text-gray-500">Mostrando {{ $investors->count() }} inversores</p>
            </div>
            <div class="grid grid-cols-6 text-xs font-semibold text-gray-500 pb-2" data-table-header>
                <button type="button" class="text-left" data-sortable data-sort-column="0">
                    Inversor <span data-sort-arrow></span>
                </button>
                <button type="button" class="text-left" data-sortable data-sort-column="1">
                    Documento <span data-sort-arrow></span>
                </button>
                <button type="button" class="text-left" data-sortable data-sort-column="2">
                    Contacto <span data-sort-arrow></span>
                </button>
                <button type="button" class="text-right" data-sortable data-sort-column="3">
                    Capital Invertido <span data-sort-arrow></span>
                </button>
                <button type="button" class="text-right" data-sortable data-sort-column="4">
                    Ganancias Acum. <span data-sort-arrow></span>
                </button>
                <span class="text-right">Estado</span>
            </div>
            <div class="divide-y divide-gray-100" data-table-body>
                @forelse ($investors as $investor)
                    <div class="grid grid-cols-6 py-3 text-sm items-center" data-row data-index="{{ $loop->index }}">
                        <div data-cell>
                            <a href="{{ route('investors.show', $investor) }}"
                                class="font-semibold text-gray-900 hover:underline">{{ $investor->name }}</a>
                            <p class="text-xs text-gray-500">Inversor desde
                                {{ optional($investor->created_at)->format('d/m/Y') }}</p>
                        </div>
                        <span class="text-gray-700" data-cell>{{ $investor->document }}</span>
                        <div class="text-gray-700" data-cell>
                            <p>{{ $investor->email ?? 'Sin email' }}</p>
                            <p class="text-xs text-gray-500">{{ $investor->phone ?? 'Sin teléfono' }}</p>
                        </div>
                        <span class="text-right font-semibold text-gray-900" data-cell>
                            {{ \App\Support\Currency::format($investor->totalInvestedCop(), 'cop') }}</span>
                        <span class="text-right font-semibold text-gray-900" data-cell>
                            {{ \App\Support\Currency::format($investor->totalGainsCop(), 'cop') }}</span>
                        <div class="text-right space-x-2">
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ strtolower($investor->status) === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">{{ $investor->status }}</span>
                            <button data-modal-target="investor-edit" data-investor='@json($investor)'
                                class="text-blue-600 text-xs">Editar</button>
                            <form method="POST" action="{{ route('investors.destroy', $investor) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-xs"
                                    onclick="return confirm('¿Eliminar inversor?')">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">No hay inversores registrados.</p>
                @endforelse
            </div>
        </x-modules.card>
    </x-modules.shell>
    <div id="modal-investor-create" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold mb-4">Nuevo Inversor</h3>
            <form method="POST" action="{{ route('investors.store') }}" class="space-y-3">
                @csrf
                <x-text-input name="name" placeholder="Nombre" class="w-full" required />
                <x-text-input name="document" placeholder="Documento" class="w-full" required />
                <x-text-input name="email" type="email" placeholder="Email" class="w-full" />
                <x-text-input name="phone" placeholder="Teléfono" class="w-full" />
                <x-text-input name="monthly_rate" type="number" step="0.01" placeholder="% mensual"
                    class="w-full" />
                <select name="status" class="border rounded-md px-3 py-2 w-full">
                    <option value="Activo">Activo</option>
                    <option value="Pendiente">Pendiente</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-investor-edit" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold mb-4">Editar Inversor</h3>
            <form method="POST" id="investor-edit-form" class="space-y-3" data-table-update data-table-target="#investors-table">
                @csrf
                @method('PUT')
                <x-text-input name="name" id="investor-name" placeholder="Nombre" class="w-full" required />
                <x-text-input name="document" id="investor-document" placeholder="Documento" class="w-full" required />
                <x-text-input name="email" id="investor-email" type="email" placeholder="Email" class="w-full" />
                <x-text-input name="phone" id="investor-phone" placeholder="Teléfono" class="w-full" />
                <x-text-input name="monthly_rate" id="investor-monthly" type="number" step="0.01"
                    placeholder="% mensual" class="w-full" />
                <select name="status" id="investor-status" class="border rounded-md px-3 py-2 w-full">
                    <option value="Activo">Activo</option>
                    <option value="Pendiente">Pendiente</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
