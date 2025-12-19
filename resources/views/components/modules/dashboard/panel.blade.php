@once
    @push('scripts')
        @vite('resources/js/modules/dashboard/index.js')
    @endpush
@endonce

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg']) }}>
    {{ $slot }}
</div>
