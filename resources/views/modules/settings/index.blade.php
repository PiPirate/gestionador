<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Administración / Configuración</p>
                <h1 class="text-2xl font-bold text-gray-900">Configuración</h1>
                <p class="text-sm text-gray-600 mt-1">Ajustes del sistema y preferencias</p>
            </div>
            <button
                class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">
                <x-heroicon-o-bookmark class="w-5 h-5" />
                <span>Guardar Cambios</span>
            </button>

        </div>

        <div class="space-y-4">
            <x-modules.card title="Tasas y Comisiones">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Tasa de Compra USD</span>
                        <input type="number" class="w-28 border rounded-md px-2 py-1 text-right"
                            value="{{ $rates['buy'] }}">
                    </label>
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Tasa de Venta USD</span>
                        <input type="number" class="w-28 border rounded-md px-2 py-1 text-right"
                            value="{{ $rates['sell'] }}">
                    </label>
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Margen Mínimo por USD</span>
                        <input type="number" class="w-28 border rounded-md px-2 py-1 text-right"
                            value="{{ $rates['min_margin'] }}">
                    </label>
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>% Rendimiento Mínimo</span>
                        <input type="number" step="0.1" class="w-28 border rounded-md px-2 py-1 text-right"
                            value="{{ $rates['min_return'] }}">
                    </label>
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>% Rendimiento Máximo</span>
                        <input type="number" step="0.1" class="w-28 border rounded-md px-2 py-1 text-right"
                            value="{{ $rates['max_return'] }}">
                    </label>
                </div>
            </x-modules.card>

            <x-modules.card title="Seguridad y Acceso">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Autenticación de Dos Factores</span>
                        <input type="checkbox" {{ $security['two_factor'] ? 'checked' : '' }}
                            class="h-4 w-4 text-green-600 rounded border-gray-300">
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Tiempo de Inactividad</span>
                        <select class="border rounded-md px-2 py-1">
                            <option>{{ $security['timeout'] }}</option>
                            <option>15 minutos</option>
                            <option>60 minutos</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Registro de Auditoría</span>
                        <input type="checkbox" {{ $security['audit_log'] ? 'checked' : '' }}
                            class="h-4 w-4 text-green-600 rounded border-gray-300">
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Límite de Intentos de Login</span>
                        <input type="number" class="w-20 border rounded-md px-2 py-1 text-right"
                            value="{{ $security['attempts'] }}">
                    </div>
                </div>
            </x-modules.card>

            <x-modules.card title="Notificaciones">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Recordatorio de Liquidaciones</span>
                        <select class="border rounded-md px-2 py-1">
                            <option>{{ $notifications['liquidations_reminder'] }}</option>
                            <option>5 días antes</option>
                            <option>7 días antes</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Notificar nuevas operaciones</span>
                        <input type="checkbox" {{ $notifications['new_operation'] ? 'checked' : '' }}
                            class="h-4 w-4 text-green-600 rounded border-gray-300">
                    </div>
                </div>
            </x-modules.card>

            <x-modules.card title="Reglas de rentabilidad">
                <div class="space-y-4 text-sm">
                    <div class="rounded-md border border-gray-100 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                        <p class="font-semibold text-gray-700">Regla activa</p>
                        <p>
                            @if ($activeProfitRule)
                                Regla #{{ $activeProfitRule->id }} · {{ $activeProfitRule->created_at?->format('d/m/Y') }}
                            @else
                                No hay regla activa
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Define los tramos para calcular la ganancia mensual. Puedes crear una regla nueva desde JSON o manualmente.</p>
                    </div>
                    <form method="POST" action="{{ route('settings.profit-rules.store') }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-500">Tramos (manual)</label>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <input name="tiers_up_to[]" type="number" placeholder="Hasta" class="w-full border rounded-md px-2 py-1">
                                        <input name="tiers_rate[]" type="number" step="0.0001" placeholder="Rate" class="w-32 border rounded-md px-2 py-1">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input name="tiers_up_to[]" type="number" placeholder="Hasta" class="w-full border rounded-md px-2 py-1" value="1000000">
                                        <input name="tiers_rate[]" type="number" step="0.0001" placeholder="Rate" class="w-32 border rounded-md px-2 py-1" value="0.12">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input name="tiers_up_to[]" type="number" placeholder="Hasta" class="w-full border rounded-md px-2 py-1" value="5000000">
                                        <input name="tiers_rate[]" type="number" step="0.0001" placeholder="Rate" class="w-32 border rounded-md px-2 py-1" value="0.08">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input name="tiers_up_to[]" type="number" placeholder="Hasta" class="w-full border rounded-md px-2 py-1" value="10000000">
                                        <input name="tiers_rate[]" type="number" step="0.0001" placeholder="Rate" class="w-32 border rounded-md px-2 py-1" value="0.06">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input name="tiers_up_to[]" type="text" placeholder="Infinity" class="w-full border rounded-md px-2 py-1" value="">
                                        <input name="tiers_rate[]" type="number" step="0.0001" placeholder="Rate" class="w-32 border rounded-md px-2 py-1" value="0.05">
                                    </div>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Deja “Hasta” vacío para el último tramo sin límite.</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">JSON de tramos (opcional)</label>
                                <textarea name="tiers_json" rows="8" class="w-full border rounded-md px-2 py-1 text-xs"
                                    placeholder='[{"upTo":1000000,"rate":0.12},{"upTo":5000000,"rate":0.08},{"upTo":10000000,"rate":0.06},{"upTo":null,"rate":0.05}]'></textarea>
                                <p class="text-[11px] text-gray-400 mt-1">Si completas JSON, tendrá prioridad sobre la entrada manual.</p>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 bg-green-600 text-white px-3 py-2 rounded-md text-xs shadow-sm hover:bg-green-700">
                                <x-heroicon-o-plus class="w-4 h-4" />
                                <span>Crear regla</span>
                            </button>
                        </div>
                    </form>
                    <div class="divide-y divide-gray-100">
                        @forelse ($profitRules as $rule)
                            <div class="flex flex-col gap-3 py-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Regla #{{ $rule->id }}</p>
                                        <p class="text-xs text-gray-500">Creada: {{ $rule->created_at?->format('d/m/Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">Tramos: {{ is_array($rule->tiers_json) ? count($rule->tiers_json) : 0 }}</p>
                                        @if ($rule->investments_count > 0)
                                            <p class="text-xs text-gray-400">Usada en inversiones.</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($rule->is_active)
                                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Activa</span>
                                            @if ($profitRules->where('is_active', true)->count() > 1)
                                                <form method="POST" action="{{ route('settings.profit-rules.deactivate', $rule) }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-gray-600">Desactivar</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">Desactivar</span>
                                            @endif
                                        @else
                                            <form method="POST" action="{{ route('settings.profit-rules.activate', $rule) }}">
                                                @csrf
                                                <button type="submit" class="text-xs text-blue-600">Activar</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('settings.profit-rules.store') }}">
                                            @csrf
                                            <input type="hidden" name="tiers_json" value='@json($rule->tiers_json)'>
                                            <button type="submit" class="text-xs text-gray-600">Duplicar</button>
                                        </form>
                                        @if ($profitRules->count() > 1)
                                            <form method="POST" action="{{ route('settings.profit-rules.destroy', $rule) }}" onsubmit="return confirm('¿Eliminar regla?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600">Eliminar</button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">Eliminar</span>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('settings.profit-rules.update', $rule) }}" class="space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <label class="text-xs text-gray-500">Editar JSON de tramos</label>
                                    <textarea name="tiers_json" rows="4" class="w-full border rounded-md px-2 py-1 text-xs">{{ json_encode($rule->tiers_json, JSON_UNESCAPED_SLASHES) }}</textarea>
                                    <div class="flex justify-end">
                                        <button type="submit" class="text-xs text-blue-600">Guardar cambios</button>
                                    </div>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No hay reglas registradas.</p>
                        @endforelse
                    </div>
                </div>
            </x-modules.card>

            <x-modules.card title="Usuarios del Sistema">
                <div class="flex items-center justify-between mb-3">
                    <button
                        class="inline-flex items-center gap-2 bg-green-600 text-white px-3 py-2 rounded-md text-xs shadow-sm hover:bg-green-700">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        <span>Agregar Usuario</span>
                    </button>
                </div>
                <div class="grid grid-cols-5 text-xs font-semibold text-gray-500 pb-2">
                    <span>Usuario</span>
                    <span>Rol</span>
                    <span>Email</span>
                    <span>Último acceso</span>
                    <span>Estado</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($users as $user)
                        <div class="grid grid-cols-5 py-3 text-sm items-center">
                            <span class="text-gray-900 font-semibold">{{ $user['name'] }}</span>
                            <span>
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">{{ $user['role'] }}</span>
                            </span>
                            <span class="text-gray-700">{{ $user['email'] }}</span>
                            <span class="text-gray-700">{{ $user['last_access'] }}</span>
                            <span class="text-green-700 font-semibold">{{ $user['status'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-modules.card>
        </div>
    </x-modules.shell>
</x-app-layout>
