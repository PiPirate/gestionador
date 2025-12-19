<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Inversiones / Activas</p>
                <h1 class="text-2xl font-bold text-gray-900">Inversiones</h1>
                <p class="text-sm text-gray-600 mt-1">Gestión detallada de todas las inversiones</p>
            </div>
            <div class="flex items-center gap-3">
                <button data-modal-target="investment-create" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">➕ Nueva Inversión</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <x-modules.card title="Total Invertido (Activo)">
                <div class="text-3xl font-bold text-gray-900">US${{ number_format($summary['total_usd'], 2) }}</div>
                <p class="text-xs text-green-600 mt-2">▲ Evolución</p>
            </x-modules.card>
            <x-modules.card title="Rendimiento Promedio">
                <div class="text-3xl font-bold text-green-700">{{ number_format($summary['avg_return'], 2) }}%</div>
                <p class="text-xs text-green-600 mt-2">Promedio ponderado</p>
            </x-modules.card>
            <x-modules.card title="Ganancias Acumuladas">
                <div class="text-3xl font-bold text-gray-900">${{ number_format($summary['accumulated'], 0, ',', '.') }}</div>
                <p class="text-xs text-green-600 mt-2">Histórico</p>
            </x-modules.card>
            <x-modules.card title="Próximas Liquidaciones">
                <div class="text-3xl font-bold text-gray-900">{{ $summary['next_liquidations'] }}</div>
                <p class="text-xs text-gray-500 mt-2">Este mes</p>
            </x-modules.card>
        </div>

        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Inversiones Activas</h3>
                <p class="text-sm text-gray-500">Mostrando {{ $investments->count() }} inversiones</p>
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
                @forelse ($investments as $investment)
                    <div class="grid grid-cols-8 py-3 text-sm items-center">
                        <span class="font-semibold text-gray-900">{{ $investment->code }}</span>
                        <span class="text-gray-700">{{ $investment->investor?->name }}</span>
                        <span class="text-gray-900 font-semibold">US${{ number_format($investment->amount_usd, 2) }}</span>
                        <span class="text-green-700 font-semibold">{{ number_format($investment->monthly_rate, 2) }}%</span>
                        <span class="text-gray-700">{{ optional($investment->start_date)->format('d/m/Y') }}</span>
                        <span class="text-gray-900 font-semibold">${{ number_format($investment->gains_cop, 0, ',', '.') }}</span>
                        <span class="text-gray-700">{{ optional($investment->next_liquidation_date)->format('d/m/Y') }}</span>
                        <span class="flex items-center justify-end gap-2">
                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">{{ $investment->status }}</span>
                            <button data-modal-target="investment-edit" data-investment='@json($investment)' class="text-blue-600 text-xs">Editar</button>
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
                <div class="grid grid-cols-2 gap-3">
                    <select name="investor_id" class="border rounded-md px-3 py-2 w-full" required>
                        <option value="">Selecciona inversor</option>
                        @foreach ($investors as $investor)
                            <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                        @endforeach
                    </select>
                    <x-text-input name="code" placeholder="Código" class="w-full" required />
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <x-text-input name="amount_usd" type="number" step="0.01" placeholder="Monto USD" class="w-full" required />
                    <x-text-input name="monthly_rate" type="number" step="0.01" placeholder="% mensual" class="w-full" required />
                    <x-text-input name="gains_cop" type="number" step="0.01" placeholder="Ganancias COP" class="w-full" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="start_date" type="date" class="w-full" required />
                    <x-text-input name="next_liquidation_date" type="date" class="w-full" />
                </div>
                <select name="status" class="border rounded-md px-3 py-2 w-full">
                    <option value="activa">Activa</option>
                    <option value="pendiente">Pendiente</option>
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
                    <select name="investor_id" id="investment-investor" class="border rounded-md px-3 py-2 w-full" required>
                        @foreach ($investors as $investor)
                            <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                        @endforeach
                    </select>
                    <x-text-input name="code" id="investment-code" placeholder="Código" class="w-full" required />
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <x-text-input name="amount_usd" id="investment-amount" type="number" step="0.01" placeholder="Monto USD" class="w-full" required />
                    <x-text-input name="monthly_rate" id="investment-rate" type="number" step="0.01" placeholder="% mensual" class="w-full" required />
                    <x-text-input name="gains_cop" id="investment-gains" type="number" step="0.01" placeholder="Ganancias COP" class="w-full" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="start_date" id="investment-start" type="date" class="w-full" required />
                    <x-text-input name="next_liquidation_date" id="investment-next" type="date" class="w-full" />
                </div>
                <select name="status" id="investment-status" class="border rounded-md px-3 py-2 w-full">
                    <option value="activa">Activa</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="cerrada">Cerrada</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
