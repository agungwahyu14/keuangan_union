<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="flex h-screen overflow-hidden">

    {{-- ═══ SIDEBAR ═══════════════════════════════════════════════════════ --}}
    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/60 lg:hidden"
         x-transition:enter="transition-opacity ease-in duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-out duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    <aside class="fixed inset-y-0 left-0 z-30 w-60 flex flex-col transform transition-transform duration-200 ease-in-out lg:relative lg:translate-x-0"
           style="background-color: #1C1C1E;"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- ── Logo / App Name ─────────────────────────────────── --}}
        <div class="flex items-center gap-3 px-5 py-5" style="border-bottom: 1px solid #3A3A3C;">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0"
                 style="background-color: #FFEA6C; color: #1C1C1E;">
                SK
            </div>
            <div>
                <p class="text-white font-bold text-sm leading-tight">Sistem Keuangan</p>
                <p class="text-xs" style="color: #636366;">Manajemen Keuangan</p>
            </div>
        </div>

        {{-- ── Navigation ──────────────────────────────────────── --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @auth
                @if(Auth::user()->hasRole('admin'))
                {{-- ADMIN MENU --}}
                <p class="px-3 pt-2 pb-1.5 text-xs font-semibold uppercase tracking-widest" style="color: #636366;">Utama</p>

                <a href="{{ route('dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <a href="{{ route('transaksi.index') }}"
                   class="sidebar-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Transaksi
                    @php $txCount = \App\Models\Transaction::count(); @endphp
                    @if($txCount > 0)
                    <span class="ml-auto menu-badge text-xs font-bold px-2 py-0.5 rounded-full"
                          style="background-color: #3A3A3C; color: #AEAEB2;">{{ $txCount }}</span>
                    @endif
                </a>

                <p class="px-3 pt-4 pb-1.5 text-xs font-semibold uppercase tracking-widest" style="color: #636366;">Master Data</p>

                <a href="{{ route('kategori.index') }}"
                   class="sidebar-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Kategori
                </a>

                <a href="{{ route('pengguna.index') }}"
                   class="sidebar-link {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Pengguna
                </a>

                <p class="px-3 pt-4 pb-1.5 text-xs font-semibold uppercase tracking-widest" style="color: #636366;">Laporan</p>

                <a href="{{ route('laporan.arus-kas') }}"
                   class="sidebar-link {{ request()->routeIs('laporan.arus-kas') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                    Arus Kas
                </a>

                <a href="{{ route('laporan.laba-rugi') }}"
                   class="sidebar-link {{ request()->routeIs('laporan.laba-rugi') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Laba Rugi
                </a>

                @else
                {{-- PETUGAS MENU --}}
                <p class="px-3 pt-2 pb-1.5 text-xs font-semibold uppercase tracking-widest" style="color: #636366;">Menu Saya</p>

                <a href="{{ route('dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Beranda
                </a>

                <a href="{{ route('transaksi.create') }}"
                   class="sidebar-link {{ request()->routeIs('transaksi.create') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Transaksi
                </a>

                <a href="{{ route('transaksi.index') }}"
                   class="sidebar-link {{ request()->routeIs('transaksi.index') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Riwayat Saya
                </a>
                @endif
            @endauth
        </nav>

        {{-- ── User Footer ─────────────────────────────────────── --}}
        @auth
        <div class="px-4 py-4" style="border-top: 1px solid #3A3A3C;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0"
                     style="background-color: #2C2C2E; color: #FFEA6C;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-xs font-semibold truncate">{{ Auth::user()->name }}</p>
                    <span class="badge-admin text-xs mt-0.5">
                        {{ Auth::user()->hasRole('admin') ? 'Admin' : 'Petugas' }}
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar"
                            class="p-1.5 rounded-lg transition-colors"
                            style="color: #636366;"
                            onmouseover="this.style.color='#fff';this.style.backgroundColor='#3A3A3C';"
                            onmouseout="this.style.color='#636366';this.style.backgroundColor='transparent';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    {{-- ═══ MAIN CONTENT ════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- ── Top Header ──────────────────────────────────────── --}}
        <header class="bg-white border-b border-gray-100 z-10 flex-shrink-0">
            <div class="flex items-center justify-between px-5 h-14">

                {{-- Hamburger mobile --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                {{-- Page title --}}
                <div class="flex-1 ml-3 lg:ml-0">
                    @hasSection('page-title')
                    <h1 class="text-base font-bold text-gray-800">@yield('page-title')</h1>
                    @if(View::hasSection('page-subtitle'))
                    <p class="text-xs text-gray-400">@yield('page-subtitle')</p>
                    @endif
                    @endif
                </div>

                {{-- Right: date + user avatar --}}
                <div class="flex items-center gap-3">
                    <span class="hidden md:block text-xs text-gray-400">
                        {{ now()->translatedFormat('d F Y') }}
                    </span>

                    {{-- User Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center gap-2 px-2.5 py-1.5 rounded-xl border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black"
                                 style="background-color: #FFEA6C; color: #1C1C1E;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-28 truncate">{{ Auth::user()->name ?? '' }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" x-cloak @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50">
                            <div class="px-4 py-2.5 border-b border-gray-50">
                                <p class="text-xs text-gray-400">Masuk sebagai</p>
                                <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name ?? '' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- ── Flash Messages ──────────────────────────────────── --}}
        @if(session()->hasAny(['success','error','warning','info']))
        <div class="px-5 pt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="text-green-500 hover:text-green-700">✕</button>
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span class="flex-1">{{ session('error') }}</span>
                <button @click="show = false" class="text-red-500 hover:text-red-700">✕</button>
            </div>
            @endif
            @if(session('warning'))
            <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">
                <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span class="flex-1">{{ session('warning') }}</span>
                <button @click="show = false" class="text-amber-500 hover:text-amber-700">✕</button>
            </div>
            @endif
            @if(session('info'))
            <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-800 text-sm">
                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <span class="flex-1">{{ session('info') }}</span>
                <button @click="show = false" class="text-blue-500 hover:text-blue-700">✕</button>
            </div>
            @endif
        </div>
        @endif

        {{-- ── Page Content ─────────────────────────────────────── --}}
        <main class="flex-1 overflow-y-auto p-5">
            {{ $slot }}
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
