@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
        ['label' => 'Inversores', 'route' => 'investors.index', 'icon' => 'users'],
        ['label' => 'Inversiones', 'route' => 'investments.index', 'icon' => 'chart-bar'],
        ['label' => 'Transacciones USD', 'route' => 'transactions.index', 'icon' => 'arrows-right-left'],
        ['label' => 'Caja / Balance', 'route' => 'cash.index', 'icon' => 'banknotes'],
        ['label' => 'Liquidaciones', 'route' => 'liquidations.index', 'icon' => 'document-text'],
        ['label' => 'Reportes', 'route' => 'reports.index', 'icon' => 'document-chart-bar'],
        ['label' => 'Auditoría', 'route' => 'audit.index', 'icon' => 'shield-check'],
        ['label' => 'Configuración', 'route' => 'settings.index', 'icon' => 'cog-6-tooth'],
    ];
@endphp

<aside class="w-64 bg-white border-r border-gray-200 hidden sm:flex flex-col">
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1">
            @foreach ($items as $item)
                            @php
                                $active = request()->routeIs($item['route']);
                            @endphp
                            <li>
                                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-4 py-2 text-sm font-medium rounded-md transition
                   {{ $active ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-700 hover:bg-gray-50' }}">

                                    <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-5 h-5 shrink-0" />

                                    <span>{{ $item['label'] }}</span>
                                </a>

                            </li>
            @endforeach
        </ul>
    </nav>
</aside>