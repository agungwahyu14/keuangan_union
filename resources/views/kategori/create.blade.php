<x-app-layout>
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

<div class="max-w-xl mx-auto">
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
     x-data="{ type: '{{ old('type', 'pemasukan') }}', isHpp: {{ old('is_hpp', '0') ? 'true' : 'false' }} }">

    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color:#FFEA6C;">
            <svg class="w-5 h-5" style="color:#1C1C1E;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        </div>
        <div>
            <h2 class="font-bold text-gray-800">Tambah Kategori Baru</h2>
            <p class="text-xs text-gray-400 mt-0.5">Kategori digunakan untuk mengelompokkan transaksi</p>
        </div>
    </div>

    <form method="POST" action="{{ route('kategori.store') }}" class="p-6 space-y-5">
        @csrf

        {{-- Tipe Transaksi --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Transaksi <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="pemasukan" class="sr-only" x-model="type" @change="isHpp = false">
                    <div :class="type === 'pemasukan' ? 'border-2 shadow-md' : 'border border-gray-200 hover:border-gray-300'"
                         :style="type === 'pemasukan' ? 'border-color:#1D9E75;background:#ECFDF5;' : ''"
                         class="rounded-xl p-4 text-center transition-all select-none">
                        <div class="text-xl mb-1">↑</div>
                        <div class="text-sm font-bold" :style="type === 'pemasukan' ? 'color:#1D9E75;' : 'color:#6B7280;'">Pemasukan</div>
                        <div class="text-xs mt-0.5" :style="type === 'pemasukan' ? 'color:#6EE7B7;' : 'color:#9CA3AF;'">Penjualan, pendapatan</div>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="pengeluaran" class="sr-only" x-model="type">
                    <div :class="type === 'pengeluaran' ? 'border-2 shadow-md' : 'border border-gray-200 hover:border-gray-300'"
                         :style="type === 'pengeluaran' ? 'border-color:#C0392B;background:#FEF2F2;' : ''"
                         class="rounded-xl p-4 text-center transition-all select-none">
                        <div class="text-xl mb-1">↓</div>
                        <div class="text-sm font-bold" :style="type === 'pengeluaran' ? 'color:#C0392B;' : 'color:#6B7280;'">Pengeluaran</div>
                        <div class="text-xs mt-0.5" :style="type === 'pengeluaran' ? 'color:#FCA5A5;' : 'color:#9CA3AF;'">Biaya, pembelian</div>
                    </div>
                </label>
            </div>
            @error('type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Nama Kategori --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   placeholder="Contoh: Penjualan Tunai, Beli Bahan Baku..."
                   maxlength="100" required
                   class="form-input">
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- HPP checkbox — hanya tampil jika pengeluaran --}}
        <div x-show="type === 'pengeluaran'" x-transition class="p-4 rounded-xl border-2 cursor-pointer"
             :style="isHpp ? 'border-color:#BA7517;background:#FFF7ED;' : 'border-color:#E5E7EB;'"
             @click="isHpp = !isHpp">
            <div class="flex items-center gap-3">
                <div class="w-5 h-5 rounded flex items-center justify-center flex-shrink-0 border-2 transition-all"
                     :style="isHpp ? 'background:#BA7517;border-color:#BA7517;' : 'border-color:#D1D5DB;'">
                    <svg x-show="isHpp" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold" :style="isHpp ? 'color:#BA7517;' : 'color:#374151;'">
                        Tandai sebagai HPP (Harga Pokok Penjualan)
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Centang jika kategori ini untuk pembelian stok/bahan baku yang menjadi HPP
                    </p>
                </div>
            </div>
            <input type="hidden" name="is_hpp" :value="isHpp ? '1' : '0'">
        </div>
        <input x-show="type !== 'pengeluaran'" type="hidden" name="is_hpp" value="0">

        {{-- Keterangan --}}
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Keterangan <span class="text-gray-400 font-normal">(opsional)</span>
            </label>
            <textarea name="description" id="description" rows="2" maxlength="255"
                      placeholder="Deskripsi singkat kategori ini..."
                      class="form-input resize-none">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Status Aktif --}}
        <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
            <div>
                <p class="text-sm font-semibold text-gray-700">Status Aktif</p>
                <p class="text-xs text-gray-400">Kategori nonaktif tidak muncul di form transaksi</p>
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

        {{-- Buttons --}}
        <div class="flex gap-3 pt-1">
            <a href="{{ route('kategori.index') }}"
               class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="flex-1 px-4 py-2.5 text-sm font-bold rounded-xl text-white transition-all"
                    style="background-color: #1C1C1E;"
                    onmouseover="this.style.backgroundColor='#000'"
                    onmouseout="this.style.backgroundColor='#1C1C1E'">
                Simpan Kategori
            </button>
        </div>
    </form>
</div>
</div>
</x-app-layout>
