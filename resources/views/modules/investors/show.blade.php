<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Inversores / {{ $investor->name }}</p>
                <h1 class="text-2xl font-bold text-gray-900">{{ $investor->name }}</h1>
                <p class="text-sm text-gray-600 mt-1">Resumen y detalle de inversiones asociadas</p>
            </div>
            <a href="{{ route('investors.index') }}" class="text-sm text-blue-600 hover:underline">Volver</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <x-modules.card title="Total invertido">
                <div class="text-2xl font-bold text-gray-900">
                    {{ \App\Support\Currency::format($summary['total_invested'], 'cop') }}</div>
                <p class="text-xs text-gray-500 mt-2">Capital histórico</p>
            </x-modules.card>
            <x-modules.card title="Total retirado">
                <div class="text-2xl font-bold text-gray-900">
                    {{ \App\Support\Currency::format($summary['total_withdrawn'], 'cop') }}</div>
                <p class="text-xs text-gray-500 mt-2">Inversiones cerradas</p>
            </x-modules.card>
            <x-modules.card title="Ganancias generadas">
                <div class="text-2xl font-bold text-green-700">
                    {{ \App\Support\Currency::format($summary['total_gains'], 'cop') }}</div>
                <p class="text-xs text-gray-500 mt-2">Acumuladas a la fecha</p>
            </x-modules.card>
            <x-modules.card title="Tiempo invertido">
                <div class="text-2xl font-bold text-gray-900">{{ $summary['total_days'] }} días</div>
                <p class="text-xs text-gray-500 mt-2">Suma de todas las inversiones</p>
            </x-modules.card>
        </div>
        <x-modules.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Inversiones del inversor</h3>
                <p class="text-sm text-gray-500">Total {{ $investments->count() }} inversiones</p>
            </div>
            <div class="overflow-x-auto">
                <div class="min-w-[800px]">
                    <div class="grid grid-cols-9 text-xs font-semibold text-gray-500 pb-2">
                        <span>Inversión</span>
                        <span>Monto</span>
                        <span>% Mensual</span>
                        <span>Inicio</span>
                        <span>Fin</span>
                        <span>Interés diario</span>
                        <span>Proyección total</span>
                        <span>Días</span>
                        <span>Estado</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                @forelse ($investments as $investment)
                    <div class="grid grid-cols-9 py-3 text-sm items-center">
                        <span class="font-semibold text-gray-900">{{ $investment->code }}</span>
                        <span class="text-gray-900 font-semibold">
                            {{ \App\Support\Currency::format($investment->amount_cop, 'cop') }}</span>
                        <span class="text-green-700 font-semibold">{{ number_format($investment->monthly_rate, 2) }}%</span>
                        <span class="text-gray-700">{{ optional($investment->start_date)->format('d/m/Y') }}</span>
                        <span class="text-gray-700">{{ optional($investment->end_date)->format('d/m/Y') ?? '—' }}</span>
                        <span class="text-gray-900 font-semibold">
                            {{ \App\Support\Currency::format($investment->dailyGainCop(), 'cop') }}</span>
                        <span class="text-gray-900 font-semibold">
                            {{ \App\Support\Currency::format($investment->totalProjectedGainCop(), 'cop') }}</span>
                        <span class="text-gray-700">{{ $investment->totalInvestmentDays() }}</span>
                        <span class="text-right">
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $investment->status === 'cerrada' ? 'bg-gray-100 text-gray-700' : ($investment->status === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">{{ ucfirst($investment->status) }}</span>
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">No hay inversiones asociadas.</p>
                @endforelse
                    </div>
                </div>
            </div>
        </x-modules.card>
    </x-modules.shell>
</x-app-layout>
