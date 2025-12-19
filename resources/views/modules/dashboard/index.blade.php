<x-app-layout>
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
                        <option>Marzo 2024</option>
                        <option>Febrero 2024</option>
                    </select>
                    <button class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">
                        🔄 Actualizar
                    </button>
                </div>
            </div>

            <div class="space-y-3">
                <x-modules.card>
                    <div class="flex items-start gap-3">
                        <div class="text-yellow-600 text-xl">⚠️</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Liquidaciones pendientes</p>
                            <p class="text-sm text-gray-600">5 inversores requieren liquidación este mes. Total a pagar: $ 8.450.000</p>
                        </div>
                    </div>
                </x-modules.card>
                <x-modules.card>
                    <div class="flex items-start gap-3">
                        <div class="text-blue-600 text-xl">ℹ️</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Capital disponible</p>
                            <p class="text-sm text-gray-600">Tienes US$ 15 disponibles para nuevas operaciones. Margen de ganancia estimado: $ 3.850.000</p>
                        </div>
                    </div>
                </x-modules.card>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-modules.card title="Capital Total en USD">
                    <div class="text-3xl font-bold text-gray-900">US$52</div>
                    <p class="text-xs text-green-600 mt-2">▲ 12.5% vs mes anterior</p>
                </x-modules.card>
                <x-modules.card title="Valor Total en COP">
                    <div class="text-3xl font-bold text-gray-900">$198.170.000</div>
                    <p class="text-xs text-gray-600 mt-2">Basado en tasa promedio de compra: $ 3.800</p>
                </x-modules.card>
                <x-modules.card title="Ganancias del Mes">
                    <div class="text-3xl font-bold text-gray-900">$8.450.000</div>
                    <ul class="text-sm text-gray-600 mt-2 space-y-1">
                        <li>Para inversores: $ 6.120.000</li>
                        <li>Ganancia neta: $ 2.330.000</li>
                    </ul>
                </x-modules.card>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-modules.card title="Inversores Activos">
                    <div class="text-2xl font-bold text-gray-900">15</div>
                    <p class="text-xs text-green-600 mt-2">▲ 3 nuevos</p>
                </x-modules.card>
                <x-modules.card title="Inversión Promedio">
                    <div class="text-2xl font-bold text-gray-900">US$3</div>
                    <p class="text-xs text-green-600 mt-2">▲ 5.2%</p>
                </x-modules.card>
                <x-modules.card title="Rendimiento Promedio">
                    <div class="text-2xl font-bold text-green-700">4.2%</div>
                    <p class="text-xs text-red-500 mt-2">▼ 0.3%</p>
                </x-modules.card>
                <x-modules.card title="Operaciones del Mes">
                    <div class="text-2xl font-bold text-gray-900">42</div>
                    <p class="text-xs text-green-600 mt-2">▲ 18.5%</p>
                </x-modules.card>
            </div>
        </section>
    </x-modules.shell>
</x-app-layout>
