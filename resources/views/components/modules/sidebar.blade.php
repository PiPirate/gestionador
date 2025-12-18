@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => '🏠'],
        ['label' => 'Inversores', 'route' => 'investors.index', 'icon' => '👥'],
        ['label' => 'Inversiones', 'route' => 'investments.index', 'icon' => '📈'],
        ['label' => 'Transacciones USD', 'route' => 'transactions.index', 'icon' => '🔀'],
        ['label' => 'Caja / Balance', 'route' => 'cash.index', 'icon' => '💰'],
        ['label' => 'Liquidaciones', 'route' => 'liquidations.index', 'icon' => '🧾'],
        ['label' => 'Reportes', 'route' => 'reports.index', 'icon' => '📑'],
        ['label' => 'Auditoría', 'route' => 'audit.index', 'icon' => '🛡️'],
        ['label' => 'Configuración', 'route' => 'settings.index', 'icon' => '⚙️'],
    ];
@endphp

<aside class="w-64 bg-white border-r border-gray-200 hidden sm:flex flex-col">
    <div class="h-16 px-6 flex items-center border-b border-gray-100">
        <span class="text-blue-700 font-semibold text-lg">$ DolarManager</span>
    </div>

    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1">
            @foreach ($items as $item)
                @php
                    $active = request()->routeIs($item['route']);
                @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-4 py-2 text-sm font-medium rounded-md transition
                        {{ $active ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-700 hover:bg-gray-50' }}">
                        <span>{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="border-t border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                MA
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">María Andrade</p>
                <p class="text-xs text-gray-500">Socio · Administrador</p>
            </div>
        </div>
    </div>
</aside>
