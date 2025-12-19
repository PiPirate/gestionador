<x-app-layout>
    @php
        use App\Support\Currency;
    @endphp
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Reportes</p>
                <h1 class="text-2xl font-bold text-gray-900">Reportes</h1>
                <p class="text-sm text-gray-600 mt-1">Exporta y consulta la información consolidada.</p>
            </div>
            <form method="GET" action="{{ route('reports.export.transactions') }}" class="flex items-center gap-2">
                <input type="hidden" name="start" value="{{ $start }}">
                <input type="hidden" name="end" value="{{ $end }}">
                <button class="inline-flex items-center gap-2 bg-blue-700 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-blue-800">⬇️ Exportar transacciones</button>
            </form>
        </div>
        <form method="GET" action="{{ route('reports.index') }}" class="bg-white border rounded-lg p-4 mb-4 flex flex-wrap items-end gap-3 shadow-sm">
            <div>
                <label class="text-xs text-gray-600">Desde</label>
                <input type="date" name="start" value="{{ $start }}" class="border rounded-md px-3 py-2">
            </div>
            <div>
                <label class="text-xs text-gray-600">Hasta</label>
                <input type="date" name="end" value="{{ $end }}" class="border rounded-md px-3 py-2">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded-md text-sm">Filtrar</button>
        </form>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <x-modules.card title="Transacciones">
                <p class="text-3xl font-bold text-gray-900">{{ $metrics['transactions']['count'] }}</p>
                <p class="text-xs text-gray-600">Volumen: {{ \App\Support\Currency::format($metrics['transactions']['volume_usd'], 'usd') }}</p>
                <p class="text-xs text-green-700">Ganancia: {{ \App\Support\Currency::format($metrics['transactions']['profit_cop'], 'cop') }}</p>
            </x-modules.card>
            <x-modules.card title="Inversiones">
                <p class="text-3xl font-bold text-gray-900">{{ $metrics['investments']['count'] }}</p>
                <p class="text-xs text-gray-600">Capital: {{ \App\Support\Currency::format($metrics['investments']['capital_usd'], 'usd') }}</p>
                <p class="text-xs text-blue-700">Tasa promedio: {{ number_format($metrics['investments']['avg_rate'], 2) }}%</p>
            </x-modules.card>
            <x-modules.card title="Liquidaciones">
                <p class="text-3xl font-bold text-gray-900">{{ $metrics['liquidations']['pending'] }} pend.</p>
                <p class="text-xs text-gray-600">Procesadas: {{ $metrics['liquidations']['processed'] }}</p>
                <p class="text-xs text-green-700">Pagado: {{ \App\Support\Currency::format($metrics['liquidations']['paid_cop'], 'cop') }}</p>
            </x-modules.card>
            <x-modules.card title="Caja">
                <p class="text-3xl font-bold text-gray-900">{{ \App\Support\Currency::format($metrics['cash']['income_cop'] - $metrics['cash']['expense_cop'], 'cop') }}</p>
                <p class="text-xs text-green-700">Ingresos: {{ \App\Support\Currency::format($metrics['cash']['income_cop'], 'cop') }}</p>
                <p class="text-xs text-red-600">Egresos: {{ \App\Support\Currency::format($metrics['cash']['expense_cop'], 'cop') }}</p>
            </x-modules.card>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <x-modules.card title="Top inversores por capital">
                <div class="divide-y divide-gray-100">
                    @forelse ($topInvestors as $investor)
                        <div class="py-3 flex items-center justify-between text-sm">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $investor->name }}</p>
                                <p class="text-xs text-gray-500">Documento: {{ $investor->document }}</p>
                            </div>
                            <p class="text-gray-900 font-semibold">{{ \App\Support\Currency::format($investor->investments_sum_amount_usd, 'usd') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-2">Sin inversores registrados.</p>
                    @endforelse
                </div>
            </x-modules.card>
            <x-modules.card title="Transacciones recientes">
                <div class="grid grid-cols-5 text-xs font-semibold text-gray-500 pb-2">
                    <span>Fecha</span>
                    <span>Tipo</span>
                    <span class="text-right">Monto</span>
                    <span class="text-right">Valor local</span>
                    <span class="text-right">Ganancia</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($recentTransactions as $tx)
                        <div class="grid grid-cols-5 py-2 text-sm items-center">
                            <span>{{ optional($tx->transacted_at)->format('d/m/Y') }}</span>
                            <span class="text-gray-700">{{ ucfirst($tx->type) }}</span>
                            <span class="text-right">{{ \App\Support\Currency::format($tx->amount_usd, 'usd') }}</span>
                            <span class="text-right">{{ \App\Support\Currency::format($tx->amount_cop, 'cop') }}</span>
                            <span class="text-right text-green-700 font-semibold">{{ \App\Support\Currency::format($tx->profit_cop, 'cop') }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-2">No hay transacciones registradas.</p>
                    @endforelse
                </div>
            </x-modules.card>
        </div>
    </x-modules.shell>
</x-app-layout>
