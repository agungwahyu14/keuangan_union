<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo / App Name -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->user()?->hasRole('admin') ? route('dashboard') : route('transaksi.index') }}"
                       class="text-sm font-bold text-gray-800 tracking-tight">
                        <i data-lucide="wallet" class="w-4 h-4 inline-block mr-1"></i> {{ config('app.name') }}
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        {{-- Dashboard — hanya Admin --}}
                        @role('admin')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            Dashboard
                        </x-nav-link>
                        @endrole

                        {{-- Transaksi — semua role --}}
                        <x-nav-link :href="route('transaksi.index')" :active="request()->routeIs('transaksi.*')">
                            Transaksi
                        </x-nav-link>

                        {{-- Kategori — hanya Admin --}}
                        @can('manage-categories')
                        <x-nav-link :href="route('kategori.index')" :active="request()->routeIs('kategori.*')">
                            Kategori
                        </x-nav-link>
                        @endcan

                        {{-- Laporan — hanya Admin --}}
                        @can('view-reports')
                        <x-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.*')">
                            Laporan
                        </x-nav-link>
                        @endcan

                        {{-- Manajemen User — hanya Admin --}}
                        @can('manage-users')
                        <x-nav-link :href="route('pengguna.index')" :active="request()->routeIs('pengguna.*')">
                            Pengguna
                        </x-nav-link>
                        @endcan
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @auth
                    {{-- Badge Role --}}
                    @if(Auth::user()->hasRole('admin'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                            <i data-lucide="shield-check" class="w-3 h-3 mr-1"></i> Admin
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <i data-lucide="clipboard-list" class="w-3 h-3 mr-1"></i> Petugas
                        </span>
                    @endif
                @endauth

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="font-medium">{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Info user -->
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs text-gray-500">Login sebagai</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            <i data-lucide="settings" class="w-4 h-4 inline-block mr-2"></i> Profil Saya
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                <i data-lucide="log-out" class="w-4 h-4 inline-block mr-2 text-red-500"></i> Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @role('admin')
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            @endrole

            <x-responsive-nav-link :href="route('transaksi.index')" :active="request()->routeIs('transaksi.*')">
                Transaksi
            </x-responsive-nav-link>

            @can('manage-categories')
            <x-responsive-nav-link :href="route('kategori.index')" :active="request()->routeIs('kategori.*')">
                Kategori
            </x-responsive-nav-link>
            @endcan

            @can('view-reports')
            <x-responsive-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.*')">
                Laporan
            </x-responsive-nav-link>
            @endcan

            @can('manage-users')
            <x-responsive-nav-link :href="route('pengguna.index')" :active="request()->routeIs('pengguna.*')">
                Pengguna
            </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <div class="mt-1">
                    @if(Auth::user()->hasRole('admin'))
                        <span class="text-xs font-semibold text-indigo-600 flex items-center gap-1"><i data-lucide="shield-check" class="w-3 h-3"></i> Administrator</span>
                    @else
                        <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1"><i data-lucide="clipboard-list" class="w-3 h-3"></i> Petugas</span>
                    @endif
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i data-lucide="settings" class="w-4 h-4 inline-block mr-2"></i> Profil Saya
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <i data-lucide="log-out" class="w-4 h-4 inline-block mr-2 text-red-500"></i> Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
