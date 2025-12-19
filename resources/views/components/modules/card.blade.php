<div {{ $attributes->merge(['class' => 'bg-white/80 backdrop-blur rounded-2xl border border-gray-100 shadow-lg shadow-gray-100']) }}>
    <div class="p-4 sm:p-5">
        @isset($title)
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
                {{ $actions ?? '' }}
            </div>
        @endisset
        {{ $slot }}
    </div>
</div>
