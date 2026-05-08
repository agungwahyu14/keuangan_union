<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex" style="background-color: #F5F5F0;">

    {{-- ══ Kiri: Branding Panel ══ --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-2/5 flex-col justify-between p-12 relative overflow-hidden"
         style="background-color: #1C1C1E;">

        {{-- Pattern background --}}
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>
        </div>

        {{-- Top: Logo --}}
        <div class="relative">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-sm"
                     style="background-color: #FFEA6C; color: #1C1C1E; letter-spacing: -0.5px;">
                    SK
                </div>
                <div>
                    <p class="text-white font-bold text-base leading-tight">Sistem Keuangan</p>
                    <p class="text-xs" style="color: #636366;">Manajemen Keuangan Union</p>
                </div>
            </div>
        </div>

        {{-- Middle: Tagline + Feature List --}}
        <div class="relative space-y-8">
            <div>
                <h1 class="text-4xl font-black text-white leading-tight">
                    Kelola keuangan<br>bisnis Anda dengan<br>
                    <span style="color: #FFEA6C;">lebih cerdas.</span>
                </h1>
                <p class="mt-4 text-sm leading-relaxed" style="color: #AEAEB2;">
                    Platform manajemen keuangan terpadu dengan laporan Arus Kas dan Laba Rugi yang mudah dipahami.
                </p>
            </div>

            <div class="space-y-3">
                @foreach([
                    ['icon' => '📊', 'text' => 'Dashboard ringkasan keuangan real-time'],
                    ['icon' => '📋', 'text' => 'Laporan Arus Kas & Laba Rugi otomatis'],
                    ['icon' => '👥', 'text' => 'Multi-user dengan kontrol akses peran'],
                    ['icon' => '📤', 'text' => 'Export laporan ke Excel & PDF'],
                ] as $feature)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm flex-shrink-0"
                         style="background-color: #2C2C2E;">{{ $feature['icon'] }}</div>
                    <p class="text-sm" style="color: #AEAEB2;">{{ $feature['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom: Credit --}}
        <div class="relative">
            <p class="text-xs" style="color: #636366;">
                © {{ date('Y') }} Sistem Manajemen Keuangan Union
            </p>
        </div>
    </div>

    {{-- ══ Kanan: Login Form ══ --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm"
                     style="background-color: #1C1C1E; color: #FFEA6C;">SK</div>
                <div>
                    <p class="font-bold text-gray-800">Sistem Keuangan</p>
                    <p class="text-xs text-gray-400">Manajemen Keuangan Union</p>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/60 p-8">
                <div class="mb-7">
                    <h2 class="text-2xl font-black text-gray-900">Selamat Datang 👋</h2>
                    <p class="text-sm text-gray-400 mt-1">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                {{-- Session Status --}}
                @if(session('status'))
                <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                    {{ session('status') }}
                </div>
                @endif

                {{-- General error (inactive user) --}}
                @if($errors->has('email') || $errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input id="email" type="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="admin@keuangan.com"
                                   required autofocus autocomplete="email"
                                   class="w-full pl-11 pr-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-800 placeholder-gray-400 focus:outline-none focus:bg-white transition-all"
                                   style="focus:border-color: #A07800; focus:box-shadow: 0 0 0 2px rgba(160,120,0,0.15);"
                                   onfocus="this.style.borderColor='#A07800';this.style.boxShadow='0 0 0 3px rgba(160,120,0,0.12)';this.style.background='white'"
                                   onblur="this.style.borderColor='#E5E7EB';this.style.boxShadow='none';this.style.background='#F9FAFB'">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input id="password"
                                   :type="show ? 'text' : 'password'"
                                   name="password"
                                   placeholder="••••••••"
                                   required autocomplete="current-password"
                                   class="w-full pl-11 pr-12 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-800 placeholder-gray-400 focus:outline-none focus:bg-white transition-all"
                                   onfocus="this.style.borderColor='#A07800';this.style.boxShadow='0 0 0 3px rgba(160,120,0,0.12)';this.style.background='white'"
                                   onblur="this.style.borderColor='#E5E7EB';this.style.boxShadow='none';this.style.background='#F9FAFB'">
                            <button type="button" @click="show = !show"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" id="remember_me"
                                   class="w-4 h-4 rounded border-gray-300"
                                   style="accent-color: #A07800;">
                            <span class="text-sm text-gray-600">Ingat saya</span>
                        </label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm font-medium transition-colors"
                           style="color: #A07800;"
                           onmouseover="this.style.color='#6B4F00'"
                           onmouseout="this.style.color='#A07800'">
                            Lupa kata sandi?
                        </a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full py-3 text-sm font-bold rounded-xl text-white transition-all flex items-center justify-center gap-2 mt-2"
                            style="background-color: #1C1C1E;"
                            onmouseover="this.style.backgroundColor='#000000'"
                            onmouseout="this.style.backgroundColor='#1C1C1E'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Masuk ke Sistem
                    </button>
                </form>

                {{-- Info akun demo --}}
                <div class="mt-6 pt-5 border-t border-gray-100">
                    <p class="text-xs text-center text-gray-400 mb-3 font-medium uppercase tracking-wider">Akun Demo</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                onclick="document.getElementById('email').value='admin@keuangan.com';document.getElementById('password').value='Admin@123'"
                                class="text-xs py-2 px-3 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors text-left">
                            <span class="block font-semibold" style="color:#A07800;">Admin</span>
                            <span class="text-gray-400">admin@keuangan.com</span>
                        </button>
                        <button type="button"
                                onclick="document.getElementById('email').value='petugas@keuangan.com';document.getElementById('password').value='Petugas@123'"
                                class="text-xs py-2 px-3 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors text-left">
                            <span class="block font-semibold text-gray-700">Petugas</span>
                            <span class="text-gray-400">petugas@keuangan.com</span>
                        </button>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                Akses diberikan oleh Administrator sistem
            </p>
        </div>
    </div>

</body>
</html>
