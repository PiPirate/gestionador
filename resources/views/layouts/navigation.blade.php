<nav x-data="{ open: false, showNotifications: false }" class="bg-white border-b border-gray-100">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            <div class="flex items-center gap-4">
                <button @click="open = !open" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold text-blue-700">
                    <x-application-logo class="block h-6 w-auto fill-current text-blue-700" />
                    <span>Gestionador</span>
                </a>
            </div>

            <div class="hidden lg:flex items-center gap-6 flex-1 justify-center">
                @php
                    $rateBuy = \App\Models\Setting::where('key', 'rate_buy')->value('value') ?? 0;
                    $rateSell = \App\Models\Setting::where('key', 'rate_sell')->value('value') ?? 0;
                    $trmToday = \App\Models\Setting::where('key', 'trm_today')->value('value') ?? 0;
                @endphp
            </div>

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('currency.switch') }}" class="hidden md:flex items-center gap-1">
                    @csrf
                    <span class="text-xs text-gray-600">Moneda:</span>
                    <div class="flex rounded-full border border-gray-200 overflow-hidden text-xs">
                        <button type="submit" name="currency" value="usd" class="px-3 py-1 {{ \App\Support\Currency::current() === \App\Support\Currency::USD ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">USD</button>
                        <button type="submit" name="currency" value="cop" class="px-3 py-1 {{ \App\Support\Currency::current() === \App\Support\Currency::COP ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">COP</button>
                    </div>
                </form>

                <form method="GET" action="{{ route('search') }}" class="hidden md:flex items-center">
                    <input type="text" name="q" placeholder="Buscar..." class="border rounded-full px-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500" value="{{ request('q') }}">
                </form>

                <div class="relative">
                    <button @click="showNotifications = !showNotifications" class="relative text-gray-500 hover:text-gray-700" aria-label="Notificaciones">
                        🔔
                        @if(auth()->user()?->unreadNotifications?->count())
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center h-4 w-4 rounded-full bg-red-500 text-white text-[10px]">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                    <div x-cloak x-show="showNotifications" @click.outside="showNotifications=false" class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                        <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900">Notificaciones</p>
                            <form method="POST" action="{{ route('notifications.read') }}">
                                @csrf
                                <button class="text-xs text-blue-600 hover:underline" type="submit">Marcar todas</button>
                            </form>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-gray-100">
                            @forelse (auth()->user()?->notifications()->latest()->limit(5)->get() ?? [] as $notification)
                                <div class="p-3 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                                    <p class="text-sm font-semibold text-gray-900">{{ $notification->data['title'] ?? 'Notificación' }}</p>
                                    <p class="text-xs text-gray-600">{{ $notification->data['body'] ?? '' }}</p>
                                </div>
                            @empty
                                <p class="p-3 text-sm text-gray-600">Sin notificaciones.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Cerrar sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden px-4 pb-4">
        <form method="GET" action="{{ route('search') }}" class="mt-3">
            <input type="text" name="q" placeholder="Buscar..." class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500" value="{{ request('q') }}">
        </form>
        <form method="POST" action="{{ route('currency.switch') }}" class="mt-3 flex items-center gap-2">
            @csrf
            <span class="text-xs text-gray-600">Moneda:</span>
            <div class="flex rounded-full border border-gray-200 overflow-hidden text-xs">
                <button type="submit" name="currency" value="usd" class="px-3 py-1 {{ \App\Support\Currency::current() === \App\Support\Currency::USD ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">USD</button>
                <button type="submit" name="currency" value="cop" class="px-3 py-1 {{ \App\Support\Currency::current() === \App\Support\Currency::COP ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">COP</button>
            </div>
        </form>
        <div class="mt-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')">
                {{ __('Perfil') }}
            </x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    {{ __('Cerrar sesión') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
