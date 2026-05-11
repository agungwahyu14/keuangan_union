<x-app-layout>
@section('title', 'Beranda')
@section('page-title', 'Beranda')
<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="relative">
            <p class="text-indigo-200 text-sm mb-1">Selamat datang,</p>
            <h2 class="text-2xl font-bold flex items-center gap-2">{{ Auth::user()->name }} <i data-lucide="smile" class="w-6 h-6 text-indigo-200"></i></h2>
            <p class="text-indigo-200 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
            <div class="mt-5">
                <a href="{{ route('transaksi.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-indigo-700 font-semibold rounded-xl text-sm hover:bg-indigo-50 transition-all shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Transaksi Baru
                </a>
            </div>
        </div>
    </div>

    {{-- Stat Cards Hari Ini --}}
    <div>
        <h3 class="text-base font-semibold text-gray-700 mb-3">Transaksi Hari Ini</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-gray-800">{{ $todayTransactions->count() }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Transaksi</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($todayPemasukan, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">Pemasukan</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-2xl font-bold text-red-600">Rp {{ number_format($todayPengeluaran, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">Pengeluaran</p>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat 7 Hari --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
            <div>
                <h3 class="font-semibold text-gray-800">Riwayat Transaksi Saya</h3>
                <p class="text-xs text-gray-400 mt-0.5">7 hari terakhir</p>
            </div>
            <a href="{{ route('transaksi.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat semua <i data-lucide="arrow-right" class="w-4 h-4 inline-block ml-1"></i></a>
        </div>
        @if($recentTransactions->isEmpty())
        <div class="py-16 text-center text-gray-400">
            <p class="text-sm">Belum ada transaksi dalam 7 hari terakhir</p>
            <a href="{{ route('transaksi.create') }}" class="mt-2 inline-block text-sm text-indigo-600 font-medium">+ Tambah sekarang</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Keterangan</th>
                        <th class="px-6 py-3 text-left">Kategori</th>
                        <th class="px-6 py-3 text-right">Nominal</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentTransactions as $trx)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                            {{ $trx->transaction_date->format('d/m/Y') }}
                            @if($trx->transaction_date->isToday())<br><span class="text-xs text-indigo-600 font-medium">Hari ini</span>@endif
                        </td>
                        <td class="px-6 py-3 text-gray-800 max-w-xs truncate">{{ $trx->description }}</td>
                        <td class="px-6 py-3">
                            <span class="{{ $trx->category?->is_hpp ? 'badge-hpp' : ($trx->type === 'pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran') }}">
                                {{ $trx->category?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right font-semibold whitespace-nowrap {{ $trx->type === 'pemasukan' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $trx->type === 'pengeluaran' ? '-' : '+' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if($trx->transaction_date->isToday())
                            <a href="{{ route('transaksi.edit', $trx) }}" class="text-xs text-indigo-600 border border-indigo-200 rounded-lg px-2.5 py-1 hover:bg-indigo-50 transition-colors">Edit</a>
                            @else<span class="text-xs text-gray-300">—</span>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
</x-app-layout>
