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
        <div id="investor-summary-cards" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <x-modules.card title="Total invertido">
                <div class="text-2xl font-bold text-gray-900">
                    {{ \App\Support\Currency::format($summary['total_invested'], 'cop') }}</div>
                <p class="text-xs text-gray-500 mt-2">Capital histórico</p>
            </x-modules.card>
            <x-modules.card title="Capital en circulación">
                <div class="text-2xl font-bold text-gray-900">
                    {{ \App\Support\Currency::format($summary['capital_in_circulation'], 'cop') }}</div>
                <p class="text-xs text-gray-500 mt-2">Inversiones activas</p>
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
        <x-modules.card id="investor-investments-table" data-table-root data-refresh-target="#investor-summary-cards">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Inversiones del inversor</h3>
                    <p class="text-sm text-gray-500">Total {{ $investments->count() }} inversiones</p>
                </div>
                <button data-modal-target="investment-create-investor"
                    class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm shadow-sm hover:bg-green-700">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    <span>Nueva inversión</span>
                </button>
            </div>
            <div class="overflow-x-auto">
                <div class="min-w-[800px]">
                    <div class="grid grid-cols-9 text-xs font-semibold text-gray-500 pb-2" data-table-header>
                        <button type="button" class="text-left" data-sortable data-sort-column="0">
                            Inversión <span data-sort-arrow></span>
                        </button>
                        <button type="button" class="text-left" data-sortable data-sort-column="1">
                            Monto <span data-sort-arrow></span>
                        </button>
                        <button type="button" class="text-left" data-sortable data-sort-column="2">
                            % Mensual <span data-sort-arrow></span>
                        </button>
                        <button type="button" class="text-left" data-sortable data-sort-column="3">
                            Inicio <span data-sort-arrow></span>
                        </button>
                        <button type="button" class="text-left" data-sortable data-sort-column="4">
                            Fin <span data-sort-arrow></span>
                        </button>
                        <button type="button" class="text-left" data-sortable data-sort-column="5">
                            Interés diario <span data-sort-arrow></span>
                        </button>
                        <button type="button" class="text-left" data-sortable data-sort-column="6">
                            Proyección total <span data-sort-arrow></span>
                        </button>
                        <button type="button" class="text-left" data-sortable data-sort-column="7">
                            Días <span data-sort-arrow></span>
                        </button>
                        <span>Estado</span>
                    </div>
                    <div class="divide-y divide-gray-100" data-table-body>
                @forelse ($investments as $investment)
                    <div class="grid grid-cols-9 py-3 text-sm items-center" data-row data-index="{{ $loop->index }}">
                        <span class="font-semibold text-gray-900" data-cell>{{ $investment->code }}</span>
                        <span class="text-gray-900 font-semibold" data-cell>
                            {{ \App\Support\Currency::format($investment->amount_cop, 'cop') }}</span>
                        <span class="text-green-700 font-semibold" data-cell>{{ number_format($investment->monthly_rate, 2) }}%</span>
                        <span class="text-gray-700" data-cell>{{ optional($investment->start_date)->format('d/m/Y') }}</span>
                        <span class="text-gray-700" data-cell>{{ optional($investment->end_date)->format('d/m/Y') ?? '—' }}</span>
                        <span class="text-gray-900 font-semibold" data-cell>
                            {{ \App\Support\Currency::format($investment->dailyGainCop(), 'cop') }}</span>
                        <span class="text-gray-900 font-semibold" data-cell>
                            {{ \App\Support\Currency::format($investment->totalProjectedGainCop(), 'cop') }}</span>
                        <span class="text-gray-700" data-cell>{{ $investment->totalInvestmentDays() }}</span>
                        <span class="text-right">
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $investment->status === 'cerrada' ? 'bg-gray-100 text-gray-700' : ($investment->status === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">{{ ucfirst($investment->status) }}</span>
                            <button data-modal-target="investment-edit-investor" data-investment='@json($investment)'
                                class="text-blue-600 text-xs ml-2">Editar</button>
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
    <div id="modal-investment-create-investor" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
            <h3 class="text-lg font-semibold mb-4">Nueva Inversión</h3>
            <form method="POST" action="{{ route('investments.store') }}" class="space-y-3" data-table-update data-table-target="#investor-investments-table" data-validate-dates>
                @csrf
                <input type="hidden" name="investor_id" value="{{ $investor->id }}">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Inversor</label>
                        <x-text-input value="{{ $investor->name }}" class="w-full" disabled />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Código</label>
                        <select name="continuation_id" class="border rounded-md px-3 py-2 w-full" data-continuation-select>
                            <option value="">Nueva inversión (código automático)</option>
                            @foreach ($continuableInvestments as $continuable)
                                <option value="{{ $continuable->id }}">
                                    Continuar {{ $continuable->code }} · Fin {{ optional($continuable->end_date)->format('d/m/Y') ?? '—' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">Al continuar, se conserva el mismo código.</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="amount_cop" type="text" placeholder="Monto (COP)" class="w-full"
                        data-format="cop" data-continuation-field required />
                    <x-text-input name="monthly_rate" type="number" step="0.01" placeholder="% mensual" class="w-full"
                        data-continuation-field required />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Fechas de inversión</p>
                    <p class="text-xs text-gray-400">Inicio y finalización del periodo de inversión.</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Fecha de inicio</label>
                        <x-text-input name="start_date" type="date" class="w-full" data-date-start data-continuation-field required />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Fecha de finalización</label>
                        <x-text-input name="end_date" type="date" class="w-full" data-date-end />
                    </div>
                </div>
                <select name="status" class="border rounded-md px-3 py-2 w-full">
                    <option value="pendiente">Pendiente</option>
                    <option value="activa">Activa</option>
                    <option value="cerrada">Cerrada</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-investment-edit-investor" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
            <h3 class="text-lg font-semibold mb-4">Editar Inversión</h3>
            <form method="POST" id="investment-edit-investor-form" class="space-y-3" data-table-update data-table-target="#investor-investments-table" data-validate-dates>
                @csrf
                @method('PUT')
                <input type="hidden" name="investor_id" value="{{ $investor->id }}">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Inversor</label>
                        <x-text-input value="{{ $investor->name }}" class="w-full" disabled />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Código asignado</label>
                        <x-text-input name="code" id="investment-edit-code" class="w-full" disabled />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-text-input name="amount_cop" id="investment-edit-amount" type="text"
                        placeholder="Monto (COP)" class="w-full" data-format="cop" required />
                    <x-text-input name="monthly_rate" id="investment-edit-rate" type="number" step="0.01"
                        placeholder="% mensual" class="w-full" required />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Fechas de inversión</p>
                    <p class="text-xs text-gray-400">Inicio y finalización del periodo de inversión.</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Fecha de inicio</label>
                        <x-text-input name="start_date" id="investment-edit-start" type="date" class="w-full" data-date-start required />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Fecha de finalización</label>
                        <x-text-input name="end_date" id="investment-edit-end" type="date" class="w-full" data-date-end />
                    </div>
                </div>
                <select name="status" id="investment-edit-status" class="border rounded-md px-3 py-2 w-full">
                    <option value="pendiente">Pendiente</option>
                    <option value="activa">Activa</option>
                    <option value="cerrada">Cerrada</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal class="px-4 py-2 text-sm border rounded-md">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
