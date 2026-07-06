<x-app-layout>
@section('title', 'Tambah Petugas')
@section('page-title', 'Tambah Akun Petugas')

<div class="max-w-xl mx-auto">
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color:#FFEA6C;">
            <svg class="w-5 h-5" style="color:#1C1C1E;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        </div>
        <div>
            <h2 class="font-bold text-gray-800">Tambah Akun Petugas</h2>
            <p class="text-xs text-gray-400 mt-0.5">Petugas hanya dapat input transaksi, tidak bisa akses laporan</p>
        </div>
    </div>

    <form method="POST" action="{{ route('pengguna.store') }}" class="p-6 space-y-5"
          x-data="{ showPass: false, showConfirm: false }">
        @csrf
        {{-- Role field (hidden, selalu petugas) --}}
        <input type="hidden" name="role" value="petugas">

        {{-- Nama --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   placeholder="Nama petugas..." maxlength="100" required
                   class="form-input">
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Alamat Email <span class="text-red-500">*</span>
            </label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   placeholder="petugas@email.com" maxlength="150" required
                   class="form-input">
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Telepon --}}
        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">
                No. Telepon <span class="text-gray-400 font-normal">(opsional)</span>
            </label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                   placeholder="08xxxxxxxxxx" maxlength="20"
                   class="form-input">
            @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Kata Sandi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'"
                       name="password" id="password" required
                       placeholder="Min. 8 karakter..." class="form-input pr-10">
                <button type="button" @click="showPass = !showPass"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-gray-400">Min. 8 karakter, harus mengandung huruf besar, huruf kecil, dan angka.</p>
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Konfirmasi Kata Sandi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input :type="showConfirm ? 'text' : 'password'"
                       name="password_confirmation" id="password_confirmation" required
                       placeholder="Ulangi kata sandi..." class="form-input pr-10">
                <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
        </div>

        {{-- Status Aktif --}}
        <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
            <div>
                <p class="text-sm font-semibold text-gray-700">Aktifkan Akun</p>
                <p class="text-xs text-gray-400">Akun nonaktif tidak bisa login</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer" x-data="{ on: true }">
                <input type="checkbox" name="is_active" value="1" class="sr-only" x-model="on" checked>
                <div class="w-11 h-6 rounded-full transition-colors"
                     :style="on ? 'background-color:#1D9E75;' : 'background-color:#D1D5DB;'">
                    <div class="w-5 h-5 bg-white rounded-full shadow-sm transition-transform mt-0.5 mx-0.5"
                         :class="on ? 'translate-x-5' : 'translate-x-0'"></div>
                </div>
            </label>
        </div>

        {{-- Peran Info --}}
        <div class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-200">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-600">Peran: Petugas</p>
                <p class="text-xs text-gray-400">Input transaksi · Edit milik sendiri (hari ini) · Tidak bisa akses laporan</p>
            </div>
        </div>

        <div class="flex gap-3 pt-1">
            <a href="{{ route('pengguna.index') }}"
               class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="flex-1 px-4 py-2.5 text-sm font-bold rounded-xl text-white transition-all"
                    style="background-color: #1C1C1E;"
                    onmouseover="this.style.backgroundColor='#000'"
                    onmouseout="this.style.backgroundColor='#1C1C1E'">
                Buat Akun Petugas
            </button>
        </div>
    </form>
</div>
</div>
</x-app-layout>
