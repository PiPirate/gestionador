<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm text-gray-400 uppercase tracking-[0.25em]">Dashboard / Operaciones</p>
                <h1 class="text-3xl font-extrabold text-slate-900 flex items-center gap-3">
                    Caja y Balance
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">Tailwind UI</span>
                </h1>
                <p class="text-sm text-gray-600 mt-1">Control de efectivo, cuentas y saldos en COP y USD.</p>
            </div>
            <div class="flex items-center gap-3">
                <button data-modal-target="movement-create" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-4 py-2 rounded-lg text-sm shadow-lg shadow-emerald-200 hover:from-emerald-600 hover:to-emerald-700">
                    <span class="text-lg">➕</span> Nuevo Movimiento
                </button>
                <button data-modal-target="account-create" class="inline-flex items-center gap-2 border border-slate-200 rounded-lg px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 shadow-sm">
                    <span class="text-lg">🏦</span> Nueva Cuenta
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-modules.card class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white border-0 shadow-xl" title="Ingresos del Mes (COP)">
                <div class="text-3xl font-black drop-shadow-sm">$ {{ number_format($summary['cop']['income'], 0, ',', '.') }}</div>
                <p class="text-xs text-emerald-50/80 mt-2">Entradas en pesos desde el 1 del mes.</p>
            </x-modules.card>
            <x-modules.card class="bg-gradient-to-br from-rose-500 to-rose-600 text-white border-0 shadow-xl" title="Egresos del Mes (COP)">
                <div class="text-3xl font-black drop-shadow-sm">$ {{ number_format($summary['cop']['expense'], 0, ',', '.') }}</div>
                <p class="text-xs text-rose-50/80 mt-2">Pagos, compras y salidas.</p>
            </x-modules.card>
            <x-modules.card class="bg-gradient-to-br from-sky-500 to-sky-600 text-white border-0 shadow-xl" title="Ingresos del Mes (USD)">
                <div class="text-3xl font-black drop-shadow-sm">US$ {{ number_format($summary['usd']['income'], 2) }}</div>
                <p class="text-xs text-sky-50/80 mt-2">Entradas reportadas en dólares.</p>
            </x-modules.card>
            <x-modules.card class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white border-0 shadow-xl" title="Egresos del Mes (USD)">
                <div class="text-3xl font-black drop-shadow-sm">US$ {{ number_format($summary['usd']['expense'], 2) }}</div>
                <p class="text-xs text-indigo-50/80 mt-2">Salidas en dólares acumuladas.</p>
            </x-modules.card>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <x-modules.card title="Saldo Neto en COP" class="ring-1 ring-emerald-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-slate-900">$ {{ number_format($summary['cop']['net'], 0, ',', '.') }}</div>
                        <p class="text-xs text-gray-500 mt-2">Diferencia entre ingresos y egresos en pesos.</p>
                    </div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 text-xl">$</span>
                </div>
            </x-modules.card>
            <x-modules.card title="Saldo Neto en USD" class="ring-1 ring-sky-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-slate-900">US$ {{ number_format($summary['usd']['net'], 2) }}</div>
                        <p class="text-xs text-gray-500 mt-2">Diferencia entre ingresos y egresos en dólares.</p>
                    </div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-sky-100 text-sky-700 text-xl">US</span>
                </div>
            </x-modules.card>
        </div>

        <x-modules.card class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Movimientos Recientes</h3>
                <p class="text-sm text-gray-500">Saldo recalculado automáticamente</p>
            </div>

            <div class="grid grid-cols-10 text-xs font-semibold text-gray-500 pb-2">
                <span>Fecha</span>
                <span>Tipo</span>
                <span>Descripción</span>
                <span>Cuenta</span>
                <span class="text-right">Monto COP</span>
                <span class="text-right">Monto USD</span>
                <span class="text-right">Saldo COP</span>
                <span class="text-right">Saldo USD</span>
                <span>Referencia</span>
                <span class="text-right">Acciones</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($movements as $movement)
                    <div class="grid grid-cols-10 py-3 text-sm items-center">
                        <span class="text-gray-700">{{ optional($movement->date)->format('d/m/Y') }}</span>
                        <span>
                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $movement->type === 'ingreso' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($movement->type) }}
                            </span>
                        </span>
                        <span class="text-gray-700">{{ $movement->description }}</span>
                        <span class="text-gray-700">{{ optional($movement->account)->name ?? 'Sin cuenta' }}</span>
                        <span class="text-right font-semibold {{ $movement->type === 'egreso' ? 'text-red-600' : 'text-gray-900' }}">
                            $ {{ number_format($movement->amount_cop, 0, ',', '.') }}
                        </span>
                        <span class="text-right font-semibold {{ $movement->type === 'egreso' ? 'text-red-600' : 'text-gray-900' }}">
                            US$ {{ number_format($movement->amount_usd, 2) }}
                        </span>
                        <span class="text-right text-gray-900 font-semibold">$ {{ number_format($movement->balance_cop ?? 0, 0, ',', '.') }}</span>
                        <span class="text-right text-gray-900 font-semibold">US$ {{ number_format($movement->balance_usd ?? 0, 2) }}</span>
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
                <span class="text-right">Saldo COP</span>
                <span class="text-right">Saldo USD</span>
                <span>Última actualización</span>
                <span class="text-right">Acciones</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($accounts as $account)
                    <div class="grid grid-cols-6 py-3 text-sm items-center">
                        <span class="text-gray-900 font-semibold">{{ $account->name }}</span>
                        <span class="text-gray-700">{{ $account->type }}</span>
                        <span class="text-right text-gray-900 font-semibold">$ {{ number_format($account->balance_cop, 0, ',', '.') }}</span>
                        <span class="text-right text-gray-900 font-semibold">US$ {{ number_format($account->balance_usd, 2) }}</span>
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
            <div class="grid grid-cols-2 gap-3">
                <x-text-input name="amount_cop" type="number" step="0.01" placeholder="Monto COP" class="w-full" />
                <x-text-input name="amount_usd" type="number" step="0.01" placeholder="Monto USD" class="w-full" />
            </div>
            <p class="text-xs text-gray-500">Ingresa al menos uno de los montos. El signo se calcula con el tipo Ingreso/Egreso.</p>
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
            <div class="grid grid-cols-2 gap-3">
                <x-text-input name="amount_cop" id="movement-amount-cop" type="number" step="0.01" placeholder="Monto COP" class="w-full" />
                <x-text-input name="amount_usd" id="movement-amount-usd" type="number" step="0.01" placeholder="Monto USD" class="w-full" />
            </div>
            <p class="text-xs text-gray-500">Ingresa al menos uno de los montos. El signo se calcula con el tipo Ingreso/Egreso.</p>
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
