<x-app-layout>
    <x-modules.shell>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Dashboard / Administración / Configuración</p>
                <h1 class="text-2xl font-bold text-gray-900">Configuración</h1>
                <p class="text-sm text-gray-600 mt-1">Ajustes del sistema y preferencias</p>
            </div>
            <button class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">💾 Guardar Cambios</button>
        </div>

        <div class="space-y-4">
            <x-modules.card title="Tasas y Comisiones">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Tasa de Compra USD</span>
                        <input type="number" class="w-28 border rounded-md px-2 py-1 text-right" value="{{ $rates['buy'] }}">
                    </label>
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Tasa de Venta USD</span>
                        <input type="number" class="w-28 border rounded-md px-2 py-1 text-right" value="{{ $rates['sell'] }}">
                    </label>
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Margen Mínimo por USD</span>
                        <input type="number" class="w-28 border rounded-md px-2 py-1 text-right" value="{{ $rates['min_margin'] }}">
                    </label>
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>% Rendimiento Mínimo</span>
                        <input type="number" step="0.1" class="w-28 border rounded-md px-2 py-1 text-right" value="{{ $rates['min_return'] }}">
                    </label>
                    <label class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>% Rendimiento Máximo</span>
                        <input type="number" step="0.1" class="w-28 border rounded-md px-2 py-1 text-right" value="{{ $rates['max_return'] }}">
                    </label>
                </div>
            </x-modules.card>

            <x-modules.card title="Seguridad y Acceso">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Autenticación de Dos Factores</span>
                        <input type="checkbox" {{ $security['two_factor'] ? 'checked' : '' }} class="h-4 w-4 text-green-600 rounded border-gray-300">
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
                        <input type="checkbox" {{ $security['audit_log'] ? 'checked' : '' }} class="h-4 w-4 text-green-600 rounded border-gray-300">
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 rounded-md px-3 py-2">
                        <span>Límite de Intentos de Login</span>
                        <input type="number" class="w-20 border rounded-md px-2 py-1 text-right" value="{{ $security['attempts'] }}">
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
                        <input type="checkbox" {{ $notifications['new_operation'] ? 'checked' : '' }} class="h-4 w-4 text-green-600 rounded border-gray-300">
                    </div>
                </div>
            </x-modules.card>

            <x-modules.card title="Usuarios del Sistema">
                <div class="flex items-center justify-between mb-3">
                    <button class="inline-flex items-center gap-2 bg-green-600 text-white px-3 py-2 rounded-md text-xs shadow-sm hover:bg-green-700">➕ Agregar Usuario</button>
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
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">{{ $user['role'] }}</span>
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
