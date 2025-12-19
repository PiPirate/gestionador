<x-app-layout>
    @php
        use App\Support\Currency;
    @endphp
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Inversores / Lista</p>
                <h1 class="text-2xl font-bold text-gray-900">Inversores</h1>
                <p class="text-sm text-gray-600 mt-1">Gestión de inversores y sus capitales</p>
            </div>
            <div class="flex items-center gap-3">
                <button data-modal-target="investor-create" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">➕ Nuevo Inversor</button>
            </div>
        </div>
        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Lista de Inversores</h3>
                <p class="text-sm text-gray-500">Mostrando {{ $investors->count() }} inversores</p>
            </div>
            <div class="grid grid-cols-7 text-xs font-semibold text-gray-500 pb-2">
                <span>Inversor</span>
                <span>Documento</span>
                <span>Contacto</span>
                <span class="text-right">Capital Invertido</span>
                <span class="text-right">% Mensual</span>
                <span class="text-right">Ganancias Acum.</span>
                <span class="text-right">Estado</span>
            </div>
            @forelse ($investors as $investor)
                <div class="grid grid-cols-7 py-3 text-sm items-center">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $investor->name }}</p>
                        <p class="text-xs text-gray-500">Inversor desde {{ optional($investor->created_at)->format('d/m/Y') }}</p>
                    </div>
                    <span class="text-gray-700">{{ $investor->document }}</span>
                    <div class="text-gray-700">
                        <p>{{ $investor->email ?? 'Sin email' }}</p>
                        <p class="text-xs text-gray-500">{{ $investor->phone ?? 'Sin teléfono' }}</p>
                    </div>
                    <span class="text-right font-semibold text-gray-900">{{ \App\Support\Currency::format($investor->capital_usd, 'usd') }}</span>
                    <span class="text-right text-green-700 font-semibold">{{ number_format($investor->monthly_rate, 2) }}%</span>
                    <span class="text-right font-semibold text-gray-900">{{ \App\Support\Currency::format($investor->gains_cop ?? 0, 'cop') }}</span>
                    <div class="text-right space-x-2">
                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $investor->status === 'Pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">{{ $investor->status }}</span>
                        <button data-modal-target="investor-edit" data-investor='@json($investor)' class="text-blue-600 text-xs">Editar</button>
                        <form method="POST" action="{{ route('investors.destroy', $investor) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 text-xs" onclick="return confirm('¿Eliminar inversor?')">Eliminar</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 py-4">No hay inversores registrados.</p>
            @endforelse
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
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="capital_usd" type="number" step="0.01" placeholder="Capital (USD)" class="w-full" />
                    <x-text-input name="monthly_rate" type="number" step="0.01" placeholder="% mensual" class="w-full" />
                </div>
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
            <form method="POST" id="investor-edit-form" class="space-y-3">
                @csrf
                @method('PUT')
                <x-text-input name="name" id="investor-name" placeholder="Nombre" class="w-full" required />
                <x-text-input name="document" id="investor-document" placeholder="Documento" class="w-full" required />
                <x-text-input name="email" id="investor-email" type="email" placeholder="Email" class="w-full" />
                <x-text-input name="phone" id="investor-phone" placeholder="Teléfono" class="w-full" />
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="capital_usd" id="investor-capital" type="number" step="0.01" placeholder="Capital (USD)" class="w-full" />
                    <x-text-input name="monthly_rate" id="investor-monthly" type="number" step="0.01" placeholder="% mensual" class="w-full" />
                </div>
                <select name="status" id="investor-status" class="border rounded-md px-3 py-2 w-full">
                    <option value="Activo">Activo</option>
                    <option value="Pendiente">Pendiente</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
