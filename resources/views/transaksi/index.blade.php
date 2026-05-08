<x-app-layout>
@section('title', 'Transaksi')
@section('page-title', 'Manajemen Transaksi')

<div class="space-y-5">

    {{-- ── Header & Actions ─────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Transaksi</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                @if(Auth::user()->hasRole('admin')) Semua transaksi dari semua petugas
                @else Riwayat transaksi Anda @endif
            </p>
        </div>
        <div class="flex gap-2">
            @can('export-reports')
            <button id="btn-export" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export
            </button>
            @endcan
            @can('manage-transactions')
            <a href="{{ route('transaksi.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Transaksi
            </a>
            @endcan
        </div>
    </div>

    {{-- ── Filter Form ────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5" x-data="{ showFilter: false }">
        <div class="flex items-center justify-between cursor-pointer" @click="showFilter = !showFilter">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-700">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter Transaksi
                @if(request()->hasAny(['date_from','date_to','type','category_id','search']))
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">Aktif</span>
                @endif
            </div>
            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="showFilter ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>

        <div x-show="showFilter" x-cloak x-transition class="mt-4 pt-4 border-t border-gray-50">
            <form method="GET" action="{{ route('transaksi.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe</label>
                    <select name="type" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Semua Tipe</option>
                        <option value="pemasukan" {{ request('type') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ request('type') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                    <select name="category_id" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->type }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
                    <a href="{{ route('transaksi.index') }}" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Summary Bar ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl px-5 py-3 flex items-center justify-between">
            <span class="text-sm text-emerald-700 font-medium">Total Pemasukan</span>
            <span class="font-bold text-emerald-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
        </div>
        <div class="bg-red-50 border border-red-100 rounded-xl px-5 py-3 flex items-center justify-between">
            <span class="text-sm text-red-700 font-medium">Total Pengeluaran</span>
            <span class="font-bold text-red-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- ── Tabel Transaksi ─────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="tabel-transaksi" class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Keterangan</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Tipe</th>
                        <th class="px-4 py-3 text-right">Nominal</th>
                        @if(Auth::user()->hasRole('admin'))
                        <th class="px-4 py-3 text-left">Dibuat Oleh</th>
                        @endif
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-800">
                            <div class="max-w-xs truncate">{{ $trx->description }}</div>
                            @if($trx->reference_number)
                                <div class="text-xs text-gray-400">No: {{ $trx->reference_number }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($trx->category?->is_hpp)
                                <span class="badge-hpp">🏷 {{ $trx->category->name }}</span>
                            @elseif($trx->type === 'pemasukan')
                                <span class="badge-pemasukan">{{ $trx->category?->name ?? '-' }}</span>
                            @else
                                <span class="badge-pengeluaran">{{ $trx->category?->name ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="{{ $trx->type === 'pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran' }}">
                                {{ $trx->type === 'pemasukan' ? '↑ Pemasukan' : '↓ Pengeluaran' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold whitespace-nowrap
                            {{ $trx->type === 'pemasukan' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $trx->type === 'pengeluaran' ? '-' : '+' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                        @if(Auth::user()->hasRole('admin'))
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $trx->creator?->name ?? '-' }}</td>
                        @endif
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Edit: Admin selalu bisa, Petugas hanya hari ini milik sendiri --}}
                                @if(Auth::user()->hasRole('admin') || ($trx->created_by === Auth::id() && $trx->transaction_date->isToday()))
                                <a href="{{ route('transaksi.edit', $trx) }}"
                                   class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @endif
                                {{-- Hapus: Admin only --}}
                                @can('delete-transactions')
                                <form method="POST" action="{{ route('transaksi.destroy', $trx) }}"
                                      onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Tidak ada transaksi ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
</x-app-layout>
