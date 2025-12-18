<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-modules.dashboard.panel>
                <div class="p-6 text-gray-900">
                    Listo. Estás logueado y ya tenemos un dashboard modular.
                </div>
            </x-modules.dashboard.panel>
        </div>
    </div>
</x-app-layout>
