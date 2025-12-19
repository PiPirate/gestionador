<x-app-layout>
    @php
        use App\Support\Currency;
    @endphp
    <x-modules.shell>
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Dashboard / Inicio</p>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-sm text-gray-600 mt-1">Resumen de operaciones e inversiones</p>
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-700">Mostrando:</label>
                    <select class="border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option>{{ now()->format('F Y') }}</option>
                    </select>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">
                        🔄 Actualizar
                    </a>
                </div>
            </div>

            <div class="space-y-3">
                @if ($cards['pending_liquidations']['count'] > 0)
                    <x-modules.card>
                        <div class="flex items-start gap-3">
                            <div class="text-yellow-600 text-xl">⚠️</div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Liquidaciones pendientes</p>
                                <p class="text-sm text-gray-600">{{ $cards['pending_liquidations']['count'] }} inversores requieren liquidación este mes. Total a pagar: {{ Currency::format($cards['pending_liquidations']['total'], 'cop') }}</p>
                            </div>
                        </div>
                    </x-modules.card>
                @endif
                <x-modules.card>
                    <div class="flex items-start gap-3">
                        <div class="text-blue-600 text-xl">ℹ️</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Capital disponible</p>
                            <p class="text-sm text-gray-600">Tienes {{ Currency::format($cards['available_capital']['usd'], 'usd') }} disponibles para nuevas operaciones. Margen de ganancia estimado: {{ Currency::format($cards['available_capital']['estimated_margin'], 'cop') }}</p>
                        </div>
                    </div>
                </x-modules.card>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-modules.card title="Capital Total">
                    <div class="text-3xl font-bold text-gray-900">{{ Currency::format($metrics['capital_usd'], 'usd') }}</div>
                    <p class="text-xs text-green-600 mt-2">Comparativo mensual</p>
                </x-modules.card>
                <x-modules.card title="Valor en moneda local">
                    <div class="text-3xl font-bold text-gray-900">{{ Currency::format($metrics['capital_cop'], 'cop') }}</div>
                    <p class="text-xs text-gray-600 mt-2">Basado en operaciones registradas</p>
                </x-modules.card>
                <x-modules.card title="Ganancias del Mes">
                    <div class="text-3xl font-bold text-gray-900">{{ Currency::format($metrics['monthly_gain'], 'cop') }}</div>
                    <ul class="text-sm text-gray-600 mt-2 space-y-1">
                        <li>Para inversores: {{ Currency::format($metrics['monthly_gain'] * 0.7, 'cop') }}</li>
                        <li>Ganancia neta: {{ Currency::format($metrics['monthly_gain'] * 0.3, 'cop') }}</li>
                    </ul>
                </x-modules.card>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-modules.card title="Inversores Activos">
                    <div class="text-2xl font-bold text-gray-900">{{ $metrics['investors_active'] }}</div>
                    <p class="text-xs text-green-600 mt-2">Nuevos este mes: {{ $cards['investor_growth'] }}</p>
                </x-modules.card>
                <x-modules.card title="Inversión Promedio">
                    <div class="text-2xl font-bold text-gray-900">{{ Currency::format($metrics['avg_investment'], 'usd') }}</div>
                    <p class="text-xs text-gray-600 mt-2">Basado en inversiones activas</p>
                </x-modules.card>
                <x-modules.card title="Rendimiento Promedio">
                    <div class="text-2xl font-bold text-green-700">{{ number_format($metrics['avg_return'], 2) }}%</div>
                    <p class="text-xs text-gray-600 mt-2">Tasa promedio mensual</p>
                </x-modules.card>
                <x-modules.card title="Operaciones del Mes">
                    <div class="text-2xl font-bold text-gray-900">{{ $metrics['operations_month'] }}</div>
                    <p class="text-xs text-green-600 mt-2">Histórico total</p>
                </x-modules.card>
            </div>

            <x-modules.card>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Usuarios del Sistema</h3>
                        <p class="text-xs text-gray-500">Control de accesos</p>
                    </div>
                    <button data-modal-target="user-create" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">
                        ➕ Agregar Usuario
                    </button>
                </div>

                <div class="divide-y divide-gray-100">
                    <div class="grid grid-cols-5 text-xs font-semibold text-gray-500 pb-2">
                        <span>Usuario</span>
                        <span>Rol</span>
                        <span>Email</span>
                        <span>Último acceso</span>
                        <span class="text-right">Estado</span>
                    </div>
                    @forelse($users as $user)
                        <div class="grid grid-cols-5 py-3 text-sm items-center">
                            <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                            <div class="text-gray-700">{{ $user->role }}</div>
                            <div class="text-gray-700">{{ $user->email }}</div>
                            <div class="text-gray-700">{{ optional($user->last_access_at)->format('d/m/Y H:i') ?? 'Sin registro' }}</div>
                            <div class="text-right flex items-center justify-end gap-2">
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $user->status === 'Activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">{{ $user->status }}</span>
                                <button class="text-blue-600 text-xs" data-modal-target="user-edit" data-user='@json($user)'>Editar</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-4">No hay usuarios registrados.</p>
                    @endforelse
                </div>
            </x-modules.card>
        </section>
    </x-modules.shell>

    <div id="modal-user-create" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold mb-4">Agregar Usuario</h3>
            <form method="POST" action="{{ route('settings.users.store') }}" class="space-y-3">
                @csrf
                <x-text-input name="name" placeholder="Nombre" class="w-full" required />
                <x-text-input name="email" type="email" placeholder="Email" class="w-full" required />
                <x-text-input name="password" type="password" placeholder="Contraseña" class="w-full" required />
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="role" placeholder="Rol" class="w-full" required />
                    <select name="status" class="border rounded-md px-3 py-2 w-full">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-user-edit" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold mb-4">Editar Usuario</h3>
            <form method="POST" id="user-edit-form" class="space-y-3">
                @csrf
                @method('PUT')
                <x-text-input name="name" id="edit-name" placeholder="Nombre" class="w-full" required />
                <x-text-input name="email" id="edit-email" type="email" placeholder="Email" class="w-full" required />
                <x-text-input name="password" id="edit-password" type="password" placeholder="Nueva contraseña (opcional)" class="w-full" />
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="role" id="edit-role" placeholder="Rol" class="w-full" required />
                    <select name="status" id="edit-status" class="border rounded-md px-3 py-2 w-full">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
