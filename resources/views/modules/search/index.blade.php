<x-app-layout>
    @php
        use App\Support\Currency;
    @endphp
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Búsqueda</p>
                <h1 class="text-2xl font-bold text-gray-900">Resultados</h1>
                <p class="text-sm text-gray-600 mt-1">Mostrando resultados para: <strong>{{ $q }}</strong></p>
            </div>
            <form method="GET" action="{{ route('search') }}" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar..." class="border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                <button class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-blue-700">Buscar</button>
            </form>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <x-modules.card title="Inversores">
                @forelse ($investors as $investor)
                    <div class="py-2 border-b border-gray-100 last:border-0">
                        <p class="font-semibold text-gray-900">{{ $investor->name }}</p>
                        <p class="text-xs text-gray-500">{{ $investor->document }} · {{ $investor->email }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">Sin resultados.</p>
                @endforelse
            </x-modules.card>
            <x-modules.card title="Inversiones">
                @forelse ($investments as $inv)
                    <div class="py-2 border-b border-gray-100 last:border-0">
                        <p class="font-semibold text-gray-900">{{ $inv->code }}</p>
                        <p class="text-xs text-gray-500">{{ $inv->investor->name ?? '—' }} · {{ \App\Support\Currency::format($inv->amount_usd, 'usd') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">Sin resultados.</p>
                @endforelse
            </x-modules.card>
            <x-modules.card title="Transacciones">
                @forelse ($transactions as $tx)
                    <div class="py-2 border-b border-gray-100 last:border-0">
                        <p class="font-semibold text-gray-900">{{ ucfirst($tx->type) }} · {{ $tx->counterparty }}</p>
                        <p class="text-xs text-gray-500">{{ $tx->transacted_at?->format('d/m/Y') }} · Ref: {{ $tx->reference }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">Sin resultados.</p>
                @endforelse
            </x-modules.card>
        </div>
    </x-modules.shell>
</x-app-layout>
