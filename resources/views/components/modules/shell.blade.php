@once
    @push('styles')
        @vite('resources/css/modules/dashboard/index.css')
    @endpush

    @push('scripts')
        @vite('resources/js/modules/dashboard/index.js')
    @endpush
@endonce

<div class="min-h-screen bg-gray-50 flex">
    <x-modules.sidebar />

    <div class="flex-1 flex flex-col min-h-screen">
        <x-modules.topbar />

        <main class="flex-1 p-6 space-y-6">
            {{ $slot }}
        </main>
    </div>
</div>
