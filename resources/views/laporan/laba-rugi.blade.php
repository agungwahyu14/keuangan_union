<x-app-layout>
@section('title', 'Laporan Laba Rugi')
@section('page-title', 'Laporan Laba Rugi')

<div class="space-y-6">

    {{-- Header & Export --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Laporan Laba Rugi Sederhana</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Periode: {{ $dateFrom->translatedFormat('d F Y') }} — {{ $dateTo->translatedFormat('d F Y') }}
            </p>
        </div>
        @can('export-reports')
        <div class="flex gap-2">
            <a href="{{ route('laporan.export.excel', ['type' => 'laba-rugi']) . '?' . http_build_query(request()->except('_token')) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Export Excel
            </a>
            <a href="{{ route('laporan.export.pdf', ['type' => 'laba-rugi']) . '?' . http_build_query(request()->except('_token')) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
        </div>
        @endcan
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5"
         x-data="{ mode: '{{ request()->has('month') || !request()->has('date_from') ? 'month' : 'range' }}' }">
        <form method="GET" action="{{ route('laporan.laba-rugi') }}">
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

    {{-- I. PENDAPATAN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-blue-50 border-b border-blue-100">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-blue-700 font-bold text-sm">I</span>
            </div>
            <h3 class="font-bold text-blue-800 text-sm uppercase tracking-wide">Pendapatan</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Kategori Pemasukan</th>
                    <th class="px-6 py-3 text-right">Jml Transaksi</th>
                    <th class="px-6 py-3 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rincianPendapatan as $item)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-6 py-3 text-gray-800">{{ $item->category?->name ?? 'Tanpa Kategori' }}</td>
                    <td class="px-6 py-3 text-right text-gray-500">{{ number_format($item->jumlah) }}</td>
                    <td class="px-6 py-3 text-right font-semibold text-emerald-600">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Tidak ada data pendapatan</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-blue-50">
                    <td class="px-6 py-3.5 font-bold text-blue-900">Total Pendapatan</td>
                    <td class="px-6 py-3.5 text-right font-semibold text-blue-700">{{ number_format($rincianPendapatan->sum('jumlah')) }}</td>
                    <td class="px-6 py-3.5 text-right font-bold text-blue-700 text-base">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- II. HARGA POKOK PENJUALAN (HPP) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-orange-50 border-b border-orange-100">
            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                <span class="text-orange-700 font-bold text-sm">II</span>
            </div>
            <h3 class="font-bold text-orange-800 text-sm uppercase tracking-wide">Harga Pokok Penjualan (HPP)</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Kategori HPP</th>
                    <th class="px-6 py-3 text-right">Jml Transaksi</th>
                    <th class="px-6 py-3 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rincianHPP as $item)
                <tr class="bg-orange-50/40 hover:bg-orange-50/70 transition-colors">
                    <td class="px-6 py-3 text-gray-800">
                        {{ $item->category?->name ?? 'Tanpa Kategori' }}
                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-700">HPP</span>
                    </td>
                    <td class="px-6 py-3 text-right text-gray-500">{{ number_format($item->jumlah) }}</td>
                    <td class="px-6 py-3 text-right font-semibold text-orange-600">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Tidak ada transaksi HPP pada periode ini</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-orange-50">
                    <td class="px-6 py-3.5 font-bold text-orange-900">Total HPP</td>
                    <td class="px-6 py-3.5 text-right font-semibold text-orange-700">{{ number_format($rincianHPP->sum('jumlah')) }}</td>
                    <td class="px-6 py-3.5 text-right font-bold text-orange-700 text-base">
                        Rp {{ number_format($totalHPP, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Laba Kotor --}}
    <div class="bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 flex items-center justify-between">
        <div>
            <p class="font-bold text-slate-800">Laba Kotor</p>
            <p class="text-xs text-slate-500 mt-0.5">= Total Pendapatan − Total HPP</p>
        </div>
        <span class="font-black text-xl {{ $labaKotor >= 0 ? 'text-slate-800' : 'text-red-600' }}">
            {{ $labaKotor < 0 ? '-' : '' }}Rp {{ number_format(abs($labaKotor), 0, ',', '.') }}
        </span>
    </div>

    {{-- III. BEBAN OPERASIONAL --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-purple-50 border-b border-purple-100">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <span class="text-purple-700 font-bold text-sm">III</span>
            </div>
            <h3 class="font-bold text-purple-800 text-sm uppercase tracking-wide">Beban Operasional</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Kategori Pengeluaran</th>
                    <th class="px-6 py-3 text-right">Jml Transaksi</th>
                    <th class="px-6 py-3 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rincianOperasional as $item)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-6 py-3 text-gray-800">{{ $item->category?->name ?? 'Tanpa Kategori' }}</td>
                    <td class="px-6 py-3 text-right text-gray-500">{{ number_format($item->jumlah) }}</td>
                    <td class="px-6 py-3 text-right font-semibold text-red-500">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Tidak ada beban operasional pada periode ini</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-purple-50">
                    <td class="px-6 py-3.5 font-bold text-purple-900">Total Beban Operasional</td>
                    <td class="px-6 py-3.5 text-right font-semibold text-purple-700">{{ number_format($rincianOperasional->sum('jumlah')) }}</td>
                    <td class="px-6 py-3.5 text-right font-bold text-purple-700 text-base">
                        Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- LABA / RUGI BERSIH --}}
    @php $labaPositif = $labaBersih >= 0; @endphp
    <div class="rounded-2xl overflow-hidden shadow-md">
        <div class="px-6 py-5 {{ $labaPositif ? 'bg-emerald-600' : 'bg-red-600' }} flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm font-medium">
                    = Laba Kotor − Beban Operasional
                </p>
                <p class="text-white font-black text-xl mt-1">
                    <i data-lucide="{{ $labaPositif ? 'check-circle' : 'x-circle' }}" class="w-6 h-6 inline-block mr-1"></i> {{ $labaPositif ? 'LABA BERSIH' : 'RUGI BERSIH' }}
                </p>
            </div>
            <span class="text-white font-black text-3xl">
                {{ $labaBersih < 0 ? '-' : '' }}Rp {{ number_format(abs($labaBersih), 0, ',', '.') }}
            </span>
        </div>
        <div class="{{ $labaPositif ? 'bg-emerald-50 border-emerald-100' : 'bg-red-50 border-red-100' }} border px-6 py-3 rounded-b-2xl">
            <p class="text-sm {{ $labaPositif ? 'text-emerald-700' : 'text-red-700' }}">
                @if($labaPositif)
                    <i data-lucide="check-circle" class="w-4 h-4 inline-block mr-1"></i> Usaha menghasilkan keuntungan bersih sebesar <strong>Rp {{ number_format($labaBersih, 0, ',', '.') }}</strong> pada periode ini.
                @else
                    <i data-lucide="alert-triangle" class="w-4 h-4 inline-block mr-1"></i> Usaha mengalami kerugian bersih sebesar <strong>Rp {{ number_format(abs($labaBersih), 0, ',', '.') }}</strong> pada periode ini.
                @endif
            </p>
        </div>
    </div>

</div>
</x-app-layout>
