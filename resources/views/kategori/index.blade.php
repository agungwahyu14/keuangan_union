<x-app-layout>
@section('title', 'Kategori')
@section('page-title', 'Master Kategori')
@section('page-subtitle', 'Kelola kategori transaksi pemasukan & pengeluaran')

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('kategori.index') }}" class="flex gap-2">
            <select name="type" class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none">
                <option value="">Semua Tipe</option>
                <option value="pemasukan" {{ request('type') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                <option value="pengeluaran" {{ request('type') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
            <select name="is_active" class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none">
                <option value="">Semua Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors"
                    style="background-color: #A07800;"
                    onmouseover="this.style.backgroundColor='#8B6700'"
                    onmouseout="this.style.backgroundColor='#A07800'">Filter</button>
            @if(request()->hasAny(['type','is_active']))
            <a href="{{ route('kategori.index') }}" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            @endif
        </form>
        <a href="{{ route('kategori.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl transition-all"
           style="background-color: #FFEA6C; color: #1C1C1E;"
           onmouseover="this.style.backgroundColor='#FFE033'"
           onmouseout="this.style.backgroundColor='#FFEA6C'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kategori
        </a>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full data-table text-sm">
            <thead>
                <tr>
                    <th class="w-8">#</th>
                    <th>Nama Kategori</th>
                    <th>Tipe</th>
                    <th class="text-center">HPP</th>
                    <th>Keterangan</th>
                    <th class="text-center">Jml Transaksi</th>
                    <th class="text-center">Status</th>
                    <th class="text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $cat)
                <tr>
                    <td class="text-gray-400 text-xs">{{ $categories->firstItem() + $index }}</td>
                    <td>
                        <p class="font-semibold text-gray-800">{{ $cat->name }}</p>
                    </td>
                    <td>
                        @if($cat->type === 'pemasukan')
                        <span class="badge-pemasukan">↑ Pemasukan</span>
                        @else
                        <span class="badge-pengeluaran">↓ Pengeluaran</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($cat->is_hpp)
                        <span class="badge-hpp">HPP</span>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="text-gray-500 max-w-xs">
                        <div class="truncate text-xs">{{ $cat->description ?: '—' }}</div>
                    </td>
                    <td class="text-center">
                        <span class="font-semibold {{ $cat->transactions_count > 0 ? 'text-gray-800' : 'text-gray-300' }}">
                            {{ number_format($cat->transactions_count) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('kategori.toggle', $cat) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-xs font-semibold px-3 py-1 rounded-full border transition-all"
                                    style="{{ $cat->is_active
                                        ? 'background:#ECFDF5;color:#1D9E75;border-color:#D1FAE5;'
                                        : 'background:#F3F4F6;color:#9CA3AF;border-color:#E5E7EB;' }}"
                                    title="{{ $cat->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                {{ $cat->is_active ? '● Aktif' : '○ Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('kategori.edit', $cat) }}"
                               class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('kategori.destroy', $cat) }}"
                                  onsubmit="return confirm('{{ $cat->transactions_count > 0 ? 'Kategori punya '.$cat->transactions_count.' transaksi dan akan dinonaktifkan. Lanjutkan?' : 'Hapus kategori ini secara permanen?' }}')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg transition-colors {{ $cat->transactions_count > 0 ? 'text-orange-400 hover:bg-orange-50' : 'text-red-400 hover:bg-red-50' }}"
                                        title="{{ $cat->transactions_count > 0 ? 'Nonaktifkan' : 'Hapus' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($cat->transactions_count > 0)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        @endif
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl mx-auto mb-3 flex items-center justify-center" style="background-color: #F5F5F0;">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <p class="text-gray-400 text-sm font-medium">Tidak ada kategori ditemukan</p>
                        <a href="{{ route('kategori.create') }}" class="mt-2 inline-block text-sm font-semibold" style="color:#A07800;">
                            + Tambah kategori pertama
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($categories->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

    {{-- Info Box --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 flex items-start gap-3">
        <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <p class="text-xs text-amber-800">
            <strong>Catatan:</strong> Kategori yang sudah memiliki transaksi tidak dapat dihapus permanen — hanya bisa dinonaktifkan untuk menjaga integritas data historis.
            Gunakan tombol status <strong>Aktif/Nonaktif</strong> untuk mengatur visibilitas.
        </p>
    </div>
</div>
</x-app-layout>
