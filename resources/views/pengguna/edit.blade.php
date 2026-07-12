<x-app-layout>
@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Akun Pengguna')

<div class="max-w-xl mx-auto">
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-base flex-shrink-0"
             style="background-color: {{ $pengguna->hasRole('admin') ? '#FFEA6C' : '#F3F4F6' }};
                    color: {{ $pengguna->hasRole('admin') ? '#1C1C1E' : '#6B7280' }};">
            {{ strtoupper(substr($pengguna->name, 0, 2)) }}
        </div>
        <div>
            <h2 class="font-bold text-gray-800">{{ $pengguna->name }}</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ $pengguna->email }}</p>
            <span class="{{ $pengguna->hasRole('admin') ? 'badge-admin' : 'badge-petugas' }} mt-1">
                {{ $pengguna->hasRole('admin') ? '👑 Admin' : '📋 Petugas' }}
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('pengguna.update', $pengguna) }}" class="p-6 space-y-5"
          x-data="{ showPass: false, showConfirm: false, changePassword: false }">
        @csrf @method('PUT')

        {{-- Nama --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name"
                   value="{{ old('name', $pengguna->name) }}"
                   maxlength="100" required class="form-input">
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email"
                   value="{{ old('email', $pengguna->email) }}"
                   maxlength="150" required class="form-input">
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Telepon --}}
        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">No. WhatsApp <span class="text-gray-400 font-normal">(opsional)</span></label>
            <input type="text" name="phone" id="phone"
                   value="{{ old('phone', $pengguna->phone) }}"
                   maxlength="20" class="form-input">
            @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Ganti Password (collapse) --}}
        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <button type="button" @click="changePassword = !changePassword"
                    class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors">
                <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Ganti Kata Sandi
                </span>
                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="changePassword ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="changePassword" x-collapse class="px-4 py-4 space-y-4 border-t border-gray-100">
                <p class="text-xs text-gray-400">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password"
                               placeholder="Min. 8 karakter..." class="form-input pr-10">
                        <button type="button" @click="showPass = !showPass"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi</label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                               placeholder="Ulangi kata sandi..." class="form-input pr-10">
                        <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-1">
            <a href="{{ route('pengguna.index') }}"
               class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="flex-1 px-4 py-2.5 text-sm font-bold rounded-xl text-white transition-all"
                    style="background-color: #A07800;"
                    onmouseover="this.style.backgroundColor='#8B6700'"
                    onmouseout="this.style.backgroundColor='#A07800'">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
</div>
</x-app-layout>
