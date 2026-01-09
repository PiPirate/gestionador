<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Inversiones</p>
                <h1 class="text-2xl font-bold text-gray-900">Inversiones</h1>
                <p class="text-sm text-gray-600 mt-1">Gestión detallada de todas las inversiones</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <select name="status" class="border rounded-md px-3 py-2 text-sm">
                        <option value="todas" {{ request('status', 'todas') === 'todas' ? 'selected' : '' }}>Todas</option>
                        <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="activa" {{ request('status') === 'activa' ? 'selected' : '' }}>Activas</option>
                        <option value="cerrada" {{ request('status') === 'cerrada' ? 'selected' : '' }}>Cerradas</option>
                    </select>
                    <button class="px-3 py-2 text-sm border rounded-md">Filtrar</button>
                </form>
                <button data-modal-target="investment-create"
                    class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    <span>Nueva Inversión</span>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <x-modules.card title="Total Invertido (Activo)">
                <div class="text-3xl font-bold text-gray-900">
                    {{ \App\Support\Currency::format($summary['total_cop'], 'cop') }}</div>
                <p class="text-xs text-green-600 mt-2">▲ Evolución</p>
            </x-modules.card>
            <x-modules.card title="Rendimiento Promedio">
                <div class="text-3xl font-bold text-green-700">{{ number_format($summary['avg_return'], 2) }}%</div>
                <p class="text-xs text-green-600 mt-2">Promedio ponderado</p>
            </x-modules.card>
            <x-modules.card title="Ganancias Progresivas">
                <div class="text-3xl font-bold text-gray-900">
                    {{ \App\Support\Currency::format($summary['accumulated'], 'cop') }}</div>
                <p class="text-xs text-green-600 mt-2">Calculadas al día</p>
            </x-modules.card>
            <x-modules.card title="Inversiones Pendientes">
                <div class="text-3xl font-bold text-gray-900">{{ $summary['next_liquidations'] }}</div>
                <p class="text-xs text-gray-500 mt-2">Por gestionar</p>
            </x-modules.card>
        </div>
        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Listado de inversiones</h3>
                <p class="text-sm text-gray-500">Mostrando {{ $investments->count() }} inversiones</p>
            </div>
            <div class="grid grid-cols-10 text-xs font-semibold text-gray-500 pb-2">
                <span>Inversión</span>
                <span>Inversor</span>
                <span>Monto</span>
                <span>% Mensual</span>
                <span>Fecha Inicio</span>
                <span>Fecha Fin</span>
                <span>Ganancia diaria</span>
                <span>Proyección mes</span>
                <span>Estado</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($investments as $investment)
                    <div class="grid grid-cols-10 py-3 text-sm items-center">
                        <span class="font-semibold text-gray-900">{{ $investment->code }}</span>
                        <span class="text-gray-700">{{ $investment->investor?->name }}</span>
                        <span
                            class="text-gray-900 font-semibold">{{ \App\Support\Currency::format($investment->amount_cop, 'cop') }}</span>
                        <span class="text-green-700 font-semibold">{{ number_format($investment->monthly_rate, 2) }}%</span>
                        <span class="text-gray-700">{{ optional($investment->start_date)->format('d/m/Y') }}</span>
                        <span
                            class="text-gray-700">{{ optional($investment->end_date)->format('d/m/Y') ?? '—' }}</span>
                        <span
                            class="text-gray-900 font-semibold">{{ \App\Support\Currency::format($investment->dailyGainCop(), 'cop') }}</span>
                        <span
                            class="text-gray-900 font-semibold">{{ \App\Support\Currency::format($investment->monthlyEstimatedGainCop(), 'cop') }}</span>
                        <span class="flex items-center justify-end gap-2">
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $investment->status === 'cerrada' ? 'bg-gray-100 text-gray-700' : ($investment->status === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">{{ ucfirst($investment->status) }}</span>
                            <button data-modal-target="investment-edit" data-investment='@json($investment)'
                                class="text-blue-600 text-xs">Editar</button>
                            @if ($investment->status !== 'cerrada')
                                <form method="POST" action="{{ route('investments.destroy', $investment) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 text-xs"
                                        onclick="return confirm('¿Eliminar inversión?')">Eliminar</button>
                                </form>
                            @endif
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">No hay inversiones registradas.</p>
                @endforelse
            </div>
        </x-modules.card>
    </x-modules.shell>
    <div id="modal-investment-create" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
            <h3 class="text-lg font-semibold mb-4">Nueva Inversión</h3>
            <form method="POST" action="{{ route('investments.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <select name="investor_id" class="border rounded-md px-3 py-2 w-full" required>
                        <option value="">Selecciona inversor</option>
                        @foreach ($investors as $investor)
                            <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                        @endforeach
                    </select>
                    <div>
                        <p class="text-xs text-gray-500">El código se genera automáticamente</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="amount_cop" type="number" step="0.01" placeholder="Monto (COP)" class="w-full"
                        required />
                    <x-text-input name="monthly_rate" type="number" step="0.01" placeholder="% mensual" class="w-full"
                        required />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Fechas de inversión</p>
                    <p class="text-xs text-gray-400">Inicio y finalización del periodo de inversión.</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Fecha de inicio</label>
                        <x-text-input name="start_date" type="date" class="w-full" required />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Fecha de finalización</label>
                        <x-text-input name="end_date" type="date" class="w-full" />
                    </div>
                </div>
                <select name="status" class="border rounded-md px-3 py-2 w-full">
                    <option value="pendiente">Pendiente</option>
                    <option value="activa">Activa</option>
                    <option value="cerrada">Cerrada</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-investment-edit" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
            <h3 class="text-lg font-semibold mb-4">Editar Inversión</h3>
            <form method="POST" id="investment-edit-form" class="space-y-3">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <select name="investor_id" id="investment-investor" class="border rounded-md px-3 py-2 w-full"
                        required>
                        @foreach ($investors as $investor)
                            <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                        @endforeach
                    </select>
                    <div>
                        <label class="text-xs text-gray-500">Código asignado</label>
                        <x-text-input name="code" id="investment-code" placeholder="Código" class="w-full" disabled />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="amount_cop" id="investment-amount" type="number" step="0.01"
                        placeholder="Monto (COP)" class="w-full" required />
                    <x-text-input name="monthly_rate" id="investment-rate" type="number" step="0.01"
                        placeholder="% mensual" class="w-full" required />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Fechas de inversión</p>
                    <p class="text-xs text-gray-400">Inicio y finalización del periodo de inversión.</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Fecha de inicio</label>
                        <x-text-input name="start_date" id="investment-start" type="date" class="w-full" required />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Fecha de finalización</label>
                        <x-text-input name="end_date" id="investment-end" type="date" class="w-full" />
                    </div>
                </div>
                <div class="rounded-md border border-gray-100 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                    <p class="font-semibold text-gray-700">Registro de edición</p>
                    <p>Última edición: <span id="investment-updated-at">—</span></p>
                    <p>Editado por: <span id="investment-updated-by">No registrado</span></p>
                </div>
                <select name="status" id="investment-status" class="border rounded-md px-3 py-2 w-full">
                    <option value="pendiente">Pendiente</option>
                    <option value="activa">Activa</option>
                    <option value="cerrada">Cerrada</option>
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
