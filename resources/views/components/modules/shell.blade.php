<div class="min-h-screen bg-gray-50 flex">
    <x-modules.sidebar />

    <div class="flex-1 flex flex-col min-h-screen">
        <main class="flex-1 p-6 space-y-6">
            {{ $slot }}
        </main>
    </div>
</div>
