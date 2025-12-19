<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Operaciones / Caja / Balance</p>
                <h1 class="text-2xl font-bold text-gray-900">Caja y Balance</h1>
                <p class="text-sm text-gray-600 mt-1">Control de efectivo y cuentas</p>
            </div>
            <div class="flex items-center gap-3">
                <button data-modal-target="movement-create" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">➕ Nuevo Movimiento</button>
                <button data-modal-target="account-create" class="inline-flex items-center gap-2 border rounded-md px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">🏦 Nueva Cuenta</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <x-modules.card title="Ingresos del Mes">
                <div class="text-3xl font-bold text-gray-900">{{ \App\Support\Currency::format($summary['income'], 'cop') }}</div>
                <p class="text-xs text-gray-600 mt-2">Incluye todos los ingresos registrados desde el 1 del mes.</p>
            </x-modules.card>
            <x-modules.card title="Egresos del Mes">
                <div class="text-3xl font-bold text-gray-900">{{ \App\Support\Currency::format($summary['expenses'], 'cop') }}</div>
                <p class="text-xs text-gray-600 mt-2">Pagos, compras y salidas registradas.</p>
            </x-modules.card>
            <x-modules.card title="Saldo Neto">
                <div class="text-3xl font-bold text-gray-900">{{ \App\Support\Currency::format($summary['net'], 'cop') }}</div>
                <p class="text-xs text-gray-600 mt-2">Diferencia entre ingresos y egresos del mes.</p>
            </x-modules.card>
        </div>
        <x-modules.card class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Movimientos Recientes</h3>
                <p class="text-sm text-gray-500">Saldo recalculado automáticamente</p>
            </div>
            <div class="grid grid-cols-8 text-xs font-semibold text-gray-500 pb-2">
                <span>Fecha</span>
                <span>Tipo</span>
                <span>Descripción</span>
                <span>Cuenta</span>
                <span class="text-right">Monto ({{ strtoupper(\App\Support\Currency::current()) }})</span>
                <span class="text-right">Saldo ({{ strtoupper(\App\Support\Currency::current()) }})</span>
                <span>Referencia</span>
                <span class="text-right">Acciones</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($movements as $movement)
                    <div class="grid grid-cols-8 py-3 text-sm items-center">
                        <span class="text-gray-700">{{ optional($movement->date)->format('d/m/Y') }}</span>
                        <span>
                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $movement->type === 'ingreso' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($movement->type) }}
                            </span>
                        </span>
                        <span class="text-gray-700">{{ $movement->description }}</span>
                        <span class="text-gray-700">{{ optional($movement->account)->name ?? 'Sin cuenta' }}</span>
                        <span class="text-right font-semibold {{ $movement->type === 'egreso' ? 'text-red-600' : 'text-gray-900' }}">
                            {{ \App\Support\Currency::format($movement->amount_cop, 'cop') }}
                        </span>
                        <span class="text-right text-gray-900 font-semibold">{{ \App\Support\Currency::format($movement->balance_cop ?? 0, 'cop') }}</span>
                        <span class="text-gray-600">{{ $movement->reference ?? '—' }}</span>
                        <div class="text-right space-x-2">
                            <button data-modal-target="movement-edit" data-movement='@json($movement)' class="text-blue-600 text-xs">Editar</button>
                            <form method="POST" action="{{ route('cash.movements.destroy', $movement) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-xs" onclick="return confirm('¿Eliminar movimiento?')">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">No hay movimientos registrados.</p>
                @endforelse
            </div>
        </x-modules.card>
        <x-modules.card>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Balance por Cuentas</h3>
            <div class="grid grid-cols-6 text-xs font-semibold text-gray-500 pb-2">
                <span>Cuenta</span>
                <span>Tipo</span>
                <span class="text-right">Saldo (desde COP)</span>
                <span class="text-right">Saldo (desde USD)</span>
                <span>Última actualización</span>
                <span class="text-right">Acciones</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($accounts as $account)
                    <div class="grid grid-cols-6 py-3 text-sm items-center">
                        <span class="text-gray-900 font-semibold">{{ $account->name }}</span>
                        <span class="text-gray-700">{{ $account->type }}</span>
                        <span class="text-right text-gray-900 font-semibold">{{ \App\Support\Currency::format($account->balance_cop, 'cop') }}</span>
                        <span class="text-right text-gray-900 font-semibold">{{ \App\Support\Currency::format($account->balance_usd, 'usd') }}</span>
                        <span class="text-gray-600">{{ optional($account->last_synced_at)->format('d/m/Y H:i') ?? '—' }}</span>
                        <div class="text-right space-x-2">
                            <button data-modal-target="account-edit" data-account='@json($account)' class="text-blue-600 text-xs">Editar</button>
                            <form method="POST" action="{{ route('cash.accounts.destroy', $account) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-xs" onclick="return confirm('¿Eliminar cuenta?')">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">No hay cuentas registradas.</p>
                @endforelse
            </div>
        </x-modules.card>
    </x-modules.shell>
</x-app-layout>
<div id="modal-movement-create" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
        <h3 class="text-lg font-semibold mb-4">Nuevo Movimiento</h3>
        <form method="POST" action="{{ route('cash.movements.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <x-text-input name="date" type="date" class="w-full" required />
                <select name="type" class="border rounded-md px-3 py-2 w-full">
                    <option value="ingreso">Ingreso</option>
                    <option value="egreso">Egreso</option>
                </select>
            </div>
            <x-text-input name="description" placeholder="Descripción" class="w-full" required />
            <x-text-input name="amount_cop" type="number" step="0.01" placeholder="Monto COP" class="w-full" required />
            <x-text-input name="reference" placeholder="Referencia (opcional)" class="w-full" />
            <select name="account_id" class="border rounded-md px-3 py-2 w-full">
                <option value="">Sin cuenta</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->type }})</option>
                @endforeach
            </select>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md">Guardar</button>
            </div>
        </form>
    </div>
</div>
<div id="modal-movement-edit" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
        <h3 class="text-lg font-semibold mb-4">Editar Movimiento</h3>
        <form method="POST" id="movement-edit-form" class="space-y-3">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <x-text-input name="date" id="movement-date" type="date" class="w-full" required />
                <select name="type" id="movement-type" class="border rounded-md px-3 py-2 w-full">
                    <option value="ingreso">Ingreso</option>
                    <option value="egreso">Egreso</option>
                </select>
            </div>
            <x-text-input name="description" id="movement-description" placeholder="Descripción" class="w-full" required />
            <x-text-input name="amount_cop" id="movement-amount" type="number" step="0.01" placeholder="Monto COP" class="w-full" required />
            <x-text-input name="reference" id="movement-reference" placeholder="Referencia (opcional)" class="w-full" />
            <select name="account_id" id="movement-account" class="border rounded-md px-3 py-2 w-full">
                <option value="">Sin cuenta</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->type }})</option>
                @endforeach
            </select>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md">Actualizar</button>
            </div>
        </form>
    </div>
</div>
<div id="modal-account-create" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
        <h3 class="text-lg font-semibold mb-4">Nueva Cuenta</h3>
        <form method="POST" action="{{ route('cash.accounts.store') }}" class="space-y-3">
            @csrf
            <x-text-input name="name" placeholder="Nombre de la cuenta" class="w-full" required />
            <select name="type" class="border rounded-md px-3 py-2 w-full">
                <option value="bancaria">Bancaria</option>
                <option value="efectivo">Efectivo</option>
                <option value="wallet">Wallet</option>
            </select>
            <div class="grid grid-cols-2 gap-3">
                <x-text-input name="balance_cop" type="number" step="0.01" placeholder="Saldo COP" class="w-full" required />
                <x-text-input name="balance_usd" type="number" step="0.01" placeholder="Saldo USD" class="w-full" required />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md">Guardar</button>
            </div>
        </form>
    </div>
</div>
<div id="modal-account-edit" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
        <h3 class="text-lg font-semibold mb-4">Editar Cuenta</h3>
        <form method="POST" id="account-edit-form" class="space-y-3">
            @csrf
            @method('PUT')
            <x-text-input name="name" id="account-name" placeholder="Nombre de la cuenta" class="w-full" required />
            <select name="type" id="account-type" class="border rounded-md px-3 py-2 w-full">
                <option value="bancaria">Bancaria</option>
                <option value="efectivo">Efectivo</option>
                <option value="wallet">Wallet</option>
            </select>
            <div class="grid grid-cols-2 gap-3">
                <x-text-input name="balance_cop" id="account-balance-cop" type="number" step="0.01" placeholder="Saldo COP" class="w-full" required />
                <x-text-input name="balance_usd" id="account-balance-usd" type="number" step="0.01" placeholder="Saldo USD" class="w-full" required />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md">Actualizar</button>
            </div>
        </form>
    </div>
</div>
