<x-app-layout>
@section('title', 'Laporan Arus Kas')
@section('page-title', 'Laporan Arus Kas')

<div class="space-y-6">

    {{-- Header & Export --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Laporan Arus Kas</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Periode: {{ $dateFrom->translatedFormat('d F Y') }} — {{ $dateTo->translatedFormat('d F Y') }}
            </p>
        </div>
        @can('export-reports')
        <div class="flex gap-2">
            <a href="{{ route('laporan.export.excel', ['type' => 'arus-kas']) . '?' . http_build_query(request()->except('_token')) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Export Excel
            </a>
            <a href="{{ route('laporan.export.pdf', ['type' => 'arus-kas']) . '?' . http_build_query(request()->except('_token')) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
        </div>
        @endcan
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5"
         x-data="{ mode: '{{ request()->has('month') ? 'month' : (request()->has('date_from') ? 'range' : 'month') }}' }">
        <form method="GET" action="{{ route('laporan.arus-kas') }}">
            <div class="flex gap-4 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="_mode" value="month" x-model="mode" class="text-indigo-600">
                    <span class="text-sm font-medium text-gray-700">Per Bulan</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="_mode" value="range" x-model="mode" class="text-indigo-600">
                    <span class="text-sm font-medium text-gray-700">Range Tanggal</span>
                </label>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div x-show="mode === 'month'">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                    <select name="month" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month', $dateFrom->month) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div x-show="mode === 'month'">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                    <select name="year" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ request('year', $dateFrom->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div x-show="mode === 'range'">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from', $dateFrom->format('Y-m-d')) }}"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div x-show="mode === 'range'">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to', $dateTo->format('Y-m-d')) }}"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Arus Kas Masuk --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-emerald-50 border-b border-emerald-100">
            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </div>
            <h3 class="font-bold text-emerald-800 text-sm uppercase tracking-wide">Arus Kas Masuk (Pemasukan)</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Kategori</th>
                    <th class="px-6 py-3 text-right">Jml Transaksi</th>
                    <th class="px-6 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pemasukanPerKategori as $item)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-6 py-3 text-gray-800">{{ $item->category?->name ?? 'Tanpa Kategori' }}</td>
                    <td class="px-6 py-3 text-right text-gray-500">{{ number_format($item->jumlah) }}</td>
                    <td class="px-6 py-3 text-right font-semibold text-emerald-600">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 text-sm">
                        Tidak ada data pemasukan pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-emerald-50">
                    <td class="px-6 py-3.5 font-bold text-emerald-900">Subtotal Arus Kas Masuk</td>
                    <td class="px-6 py-3.5 text-right font-semibold text-emerald-700">{{ number_format($pemasukanPerKategori->sum('jumlah')) }}</td>
                    <td class="px-6 py-3.5 text-right font-bold text-emerald-700 text-base">
                        Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Arus Kas Keluar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-red-50 border-b border-red-100">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </div>
            <h3 class="font-bold text-red-800 text-sm uppercase tracking-wide">Arus Kas Keluar (Pengeluaran)</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Kategori</th>
                    <th class="px-6 py-3 text-right">Jml Transaksi</th>
                    <th class="px-6 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pengeluaranPerKategori as $item)
                <tr class="{{ $item->category?->is_hpp ? 'bg-orange-50/60' : 'hover:bg-gray-50/60' }} transition-colors">
                    <td class="px-6 py-3 text-gray-800">
                        {{ $item->category?->name ?? 'Tanpa Kategori' }}
                        @if($item->category?->is_hpp)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200">
                                HPP
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-right text-gray-500">{{ number_format($item->jumlah) }}</td>
                    <td class="px-6 py-3 text-right font-semibold text-red-600">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 text-sm">
                        Tidak ada data pengeluaran pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-red-50">
                    <td class="px-6 py-3.5 font-bold text-red-900">Subtotal Arus Kas Keluar</td>
                    <td class="px-6 py-3.5 text-right font-semibold text-red-700">{{ number_format($pengeluaranPerKategori->sum('jumlah')) }}</td>
                    <td class="px-6 py-3.5 text-right font-bold text-red-700 text-base">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Ringkasan Arus Kas --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Ringkasan Arus Kas</h3>
        </div>
        <div class="p-6 space-y-3">
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                <span class="text-gray-600">Total Arus Kas Masuk</span>
                <span class="font-bold text-emerald-600 text-lg">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                <span class="text-gray-600">Total Arus Kas Keluar</span>
                <span class="font-bold text-red-600 text-lg">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
            </div>
            @php $positif = $arusKasBersih >= 0; @endphp
            <div class="flex items-center justify-between py-4 px-6 rounded-xl
                        {{ $positif ? 'bg-emerald-600' : 'bg-red-600' }}">
                <div>
                    <p class="text-white font-bold text-lg">Arus Kas Bersih</p>
                    <p class="text-white/70 text-xs mt-0.5">
                        {{ $positif ? '✓ Positif — lebih banyak masuk dari keluar' : '⚠ Negatif — pengeluaran melebihi pemasukan' }}
                    </p>
                </div>
                <span class="text-white font-black text-2xl">
                    {{ $arusKasBersih < 0 ? '-' : '' }}Rp {{ number_format(abs($arusKasBersih), 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

</div>
</x-app-layout>
