<x-app-layout>
    @php
        use App\Support\Currency;
    @endphp
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Operaciones / Liquidaciones</p>
                <h1 class="text-2xl font-bold text-gray-900">Liquidaciones</h1>
                <p class="text-sm text-gray-600 mt-1">Pago de rendimientos a inversores</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('liquidations.index') }}" class="hidden"></form>
                <button data-modal-target="liquidation-create" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">➕ Nueva Liquidación</button>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            <div class="flex gap-3 text-sm">
                <a href="{{ route('liquidations.index', ['status' => 'pendiente']) }}" class="px-3 py-2 rounded-md border {{ $status === 'pendiente' ? 'border-green-600 text-green-700 font-semibold bg-green-50' : 'border-gray-200 text-gray-600' }}">Pendientes</a>
                <a href="{{ route('liquidations.index', ['status' => 'procesada']) }}" class="px-3 py-2 rounded-md border {{ $status === 'procesada' ? 'border-green-600 text-green-700 font-semibold bg-green-50' : 'border-gray-200 text-gray-600' }}">Procesadas</a>
                <a href="{{ route('liquidations.index', ['status' => 'todas']) }}" class="px-3 py-2 rounded-md border {{ $status === 'todas' ? 'border-green-600 text-green-700 font-semibold bg-green-50' : 'border-gray-200 text-gray-600' }}">Todas</a>
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
                <div class="text-3xl font-bold text-gray-900">{{ Currency::format($summary['total_paid'], 'cop') }}</div>
                <p class="text-xs text-green-600 mt-2">▲ 15.2%</p>
            </x-modules.card>
            <x-modules.card title="Próxima Fecha">
                <div class="text-3xl font-bold text-gray-900">{{ $summary['next_date'] }}</div>
                <p class="text-xs text-gray-500 mt-2">⌛ 15 días</p>
            </x-modules.card>
        </div>

        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Liquidaciones {{ $status === 'procesada' ? 'Procesadas' : 'Pendientes' }}</h3>
                <p class="text-xs text-gray-500">Gestiona pagos e intereses</p>
            </div>

            <div class="grid grid-cols-8 text-xs font-semibold text-gray-500 pb-2">
                <span>Liquidación</span>
                <span>Inversor</span>
                <span>Monto ({{ strtoupper(Currency::current()) }})</span>
                <span>% Mensual</span>
                <span>Período</span>
                <span>Ganancias</span>
                <span>Total a pagar</span>
                <span class="text-right">Acciones</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($liquidations as $row)
                    <div class="grid grid-cols-8 py-3 text-sm items-center">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $row->code }}</p>
                            <p class="text-xs text-gray-500">Vence: {{ optional($row->due_date)->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <span class="text-gray-700">{{ $row->investor->name ?? '—' }}</span>
                        <span class="text-gray-700">{{ Currency::format($row->amount_usd, 'usd') }}</span>
                        <span class="text-green-700 font-semibold">{{ number_format($row->monthly_rate, 1) }}%</span>
                        <span class="text-gray-700">{{ optional($row->period_start)->format('d/m') }} - {{ optional($row->period_end)->format('d/m/Y') }}</span>
                        <span class="text-gray-900 font-semibold">{{ Currency::format($row->gain_cop, 'cop') }}</span>
                        <span class="text-gray-900 font-semibold">{{ Currency::format($row->total_cop, 'cop') }}</span>
                        <div class="text-right space-x-2">
                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $row->status === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">{{ ucfirst($row->status) }}</span>
                            <button data-modal-target="liquidation-edit" data-liquidation='@json($row)' class="text-blue-600 text-xs">Editar</button>
                            @if($row->status !== 'procesada')
                                <form method="POST" action="{{ route('liquidations.process', $row) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-700 text-xs" onclick="return confirm('¿Marcar como procesada?')">Procesar</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('liquidations.destroy', $row) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-xs" onclick="return confirm('¿Eliminar liquidación?')">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">No hay liquidaciones registradas.</p>
                @endforelse
            </div>
        </x-modules.card>
    </x-modules.shell>
</x-app-layout>

<div id="modal-liquidation-create" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
        <h3 class="text-lg font-semibold mb-4">Nueva Liquidación</h3>
        <form method="POST" action="{{ route('liquidations.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-600">Inversor</label>
                    <select name="investor_id" class="border rounded-md px-3 py-2 w-full" required>
                        <option value="">Seleccione</option>
                        @foreach ($investors as $investor)
                            <option value="{{ $investor->id }}">{{ $investor->name }} ({{ $investor->monthly_rate }}%)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600">% mensual</label>
                    <x-text-input name="monthly_rate" type="number" step="0.01" class="w-full" required />
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-600">Monto USD</label>
                    <x-text-input name="amount_usd" type="number" step="0.01" class="w-full" required />
                </div>
                <div>
                    <label class="text-xs text-gray-600">Inicio</label>
                    <x-text-input name="period_start" type="date" class="w-full" required />
                </div>
                <div>
                    <label class="text-xs text-gray-600">Fin</label>
                    <x-text-input name="period_end" type="date" class="w-full" required />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-600">Fecha de pago</label>
                    <x-text-input name="due_date" type="date" class="w-full" />
                </div>
                <div>
                    <label class="text-xs text-gray-600">Estado</label>
                    <select name="status" class="border rounded-md px-3 py-2 w-full">
                        <option value="pendiente">Pendiente</option>
                        <option value="procesada">Procesada</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-liquidation-edit" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
        <h3 class="text-lg font-semibold mb-4">Editar Liquidación</h3>
        <form method="POST" id="liquidation-edit-form" class="space-y-3">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-600">Inversor</label>
                    <select name="investor_id" id="liquidation-investor" class="border rounded-md px-3 py-2 w-full" required>
                        @foreach ($investors as $investor)
                            <option value="{{ $investor->id }}">{{ $investor->name }} ({{ $investor->monthly_rate }}%)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600">% mensual</label>
                    <x-text-input name="monthly_rate" id="liquidation-rate" type="number" step="0.01" class="w-full" required />
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-600">Monto USD</label>
                    <x-text-input name="amount_usd" id="liquidation-amount" type="number" step="0.01" class="w-full" required />
                </div>
                <div>
                    <label class="text-xs text-gray-600">Inicio</label>
                    <x-text-input name="period_start" id="liquidation-start" type="date" class="w-full" required />
                </div>
                <div>
                    <label class="text-xs text-gray-600">Fin</label>
                    <x-text-input name="period_end" id="liquidation-end" type="date" class="w-full" required />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-600">Fecha de pago</label>
                    <x-text-input name="due_date" id="liquidation-due" type="date" class="w-full" />
                </div>
                <div>
                    <label class="text-xs text-gray-600">Estado</label>
                    <select name="status" id="liquidation-status" class="border rounded-md px-3 py-2 w-full">
                        <option value="pendiente">Pendiente</option>
                        <option value="procesada">Procesada</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md">Actualizar</button>
            </div>
        </form>
    </div>
</div>
