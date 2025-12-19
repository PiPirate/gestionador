<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Transacciones / Flujo</p>
                <h1 class="text-2xl font-bold text-gray-900">Transacciones</h1>
                <p class="text-sm text-gray-600 mt-1">Historial de compras y ventas</p>
            </div>
            <div class="flex items-center gap-3">
                <button data-modal-target="transaction-create" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">➕ Nueva Transacción</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <x-modules.card title="USD Comprados">
                <div class="text-2xl font-bold text-gray-900">{{ \App\Support\Currency::format($summary['bought'], 'usd') }}</div>
            </x-modules.card>
            <x-modules.card title="USD Vendidos">
                <div class="text-2xl font-bold text-gray-900">{{ \App\Support\Currency::format($summary['sold'], 'usd') }}</div>
            </x-modules.card>
            <x-modules.card title="Ganancia Neta">
                <div class="text-2xl font-bold text-gray-900">{{ \App\Support\Currency::format($summary['net_profit'], 'cop') }}</div>
            </x-modules.card>
            <x-modules.card title="Inventario">
                <div class="text-2xl font-bold text-gray-900">{{ \App\Support\Currency::format($summary['inventory'], 'usd') }}</div>
            </x-modules.card>
        </div>
        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Historial</h3>
                <p class="text-sm text-gray-500">{{ $history->count() }} movimientos</p>
            </div>
            <div class="grid grid-cols-7 text-xs font-semibold text-gray-500 pb-2">
                <span>Referencia</span>
                <span>Tipo</span>
                <span>Monto ({{ strtoupper(\App\Support\Currency::current()) }})</span>
                <span>Tasa</span>
                <span>Contraparte</span>
                <span>Fecha</span>
                <span class="text-right">Acciones</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($history as $tx)
                    <div class="grid grid-cols-7 py-3 text-sm items-center">
                        <span class="font-semibold text-gray-900">{{ $tx->reference ?? 'N/A' }}</span>
                        <span class="text-gray-700 capitalize">{{ $tx->type }}</span>
                        <span class="text-gray-900 font-semibold">{{ \App\Support\Currency::format($tx->amount_usd, 'usd') }}</span>
                        <span class="text-gray-700">${{ number_format($tx->rate, 2) }}</span>
                        <span class="text-gray-700">{{ $tx->counterparty }}</span>
                        <span class="text-gray-700">{{ optional($tx->transacted_at)->format('d/m/Y') }}</span>
                        <div class="text-right">
                            <button data-modal-target="transaction-edit" data-transaction='@json($tx)' class="text-blue-600 text-xs">Editar</button>
                            <form method="POST" action="{{ route('transactions.destroy', $tx) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-xs" onclick="return confirm('¿Eliminar transacción?')">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">No hay transacciones registradas.</p>
                @endforelse
            </div>
        </x-modules.card>
    </x-modules.shell>
    <div id="modal-transaction-create" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
            <h3 class="text-lg font-semibold mb-4">Nueva Transacción</h3>
            <form method="POST" action="{{ route('transactions.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <select name="type" class="border rounded-md px-3 py-2 w-full" required>
                        <option value="compra">Compra</option>
                        <option value="venta">Venta</option>
                    </select>
                    <x-text-input name="reference" placeholder="Referencia" class="w-full" />
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <x-text-input name="amount_usd" type="number" step="0.01" placeholder="Monto USD" class="w-full" required />
                    <x-text-input name="rate" type="number" step="0.01" placeholder="Tasa" class="w-full" required />
                    <x-text-input name="amount_cop" type="number" step="0.01" placeholder="Monto COP" class="w-full" required />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="counterparty" placeholder="Contraparte" class="w-full" required />
                    <x-text-input name="method" placeholder="Método" class="w-full" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="profit_cop" type="number" step="0.01" placeholder="Ganancia COP" class="w-full" />
                    <x-text-input name="transacted_at" type="date" class="w-full" required />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-transaction-edit" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
            <h3 class="text-lg font-semibold mb-4">Editar Transacción</h3>
            <form method="POST" id="transaction-edit-form" class="space-y-3">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <select name="type" id="tx-type" class="border rounded-md px-3 py-2 w-full" required>
                        <option value="compra">Compra</option>
                        <option value="venta">Venta</option>
                    </select>
                    <x-text-input name="reference" id="tx-reference" placeholder="Referencia" class="w-full" />
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <x-text-input name="amount_usd" id="tx-amount" type="number" step="0.01" placeholder="Monto USD" class="w-full" required />
                    <x-text-input name="rate" id="tx-rate" type="number" step="0.01" placeholder="Tasa" class="w-full" required />
                    <x-text-input name="amount_cop" id="tx-cop" type="number" step="0.01" placeholder="Monto COP" class="w-full" required />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="counterparty" id="tx-counterparty" placeholder="Contraparte" class="w-full" required />
                    <x-text-input name="method" id="tx-method" placeholder="Método" class="w-full" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="profit_cop" id="tx-profit" type="number" step="0.01" placeholder="Ganancia COP" class="w-full" />
                    <x-text-input name="transacted_at" id="tx-date" type="date" class="w-full" required />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
