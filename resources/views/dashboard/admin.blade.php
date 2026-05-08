<x-app-layout>
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan keuangan bisnis Anda')

<div class="space-y-5">

    {{-- ── Filter Periode ─────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            <select name="month" id="filter-month"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none"
                    style="focus:border-color: #A07800;">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m)->translatedFormat('M') }}
                </option>
                @endfor
            </select>

            {{-- Quarter Quick --}}
            @foreach(['Q1' => [1,3], 'Q2' => [4,6], 'Q3' => [7,9], 'Q4' => [10,12]] as $label => [$start, $end])
            <button type="button"
                    onclick="document.getElementById('filter-month').value={{ $start }}"
                    class="text-xs font-medium px-2.5 py-1.5 rounded-lg border transition-colors
                           {{ $month >= $start && $month <= $end ? 'border-yellow-300 text-yellow-800' : 'border-gray-200 text-gray-500 hover:border-gray-300' }}"
                    style="{{ $month >= $start && $month <= $end ? 'background-color:#FFEA6C;' : '' }}">
                {{ $label }}
            </button>
            @endforeach

            <select name="year"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit"
                    class="px-4 py-1.5 text-sm font-semibold rounded-lg text-white transition-colors"
                    style="background-color: #A07800;"
                    onmouseover="this.style.backgroundColor='#8B6700'"
                    onmouseout="this.style.backgroundColor='#A07800'">
                Tampilkan
            </button>
        </form>

        <a href="{{ route('transaksi.create') }}"
           class="inline-flex items-center gap-2 px-4 py-1.5 text-sm font-bold rounded-xl"
           style="background-color: #FFEA6C; color: #1C1C1E;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Transaksi
        </a>
    </div>

    {{-- ── 4 Stat Cards ────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Total Pemasukan --}}
        <div class="stat-card">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center icon-income">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
                <span class="text-xs text-gray-400">↑ Total Pemasukan</span>
            </div>
            <p class="text-2xl font-black" style="color: #1D9E75;">
                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
            </p>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="stat-card">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center icon-expense">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
                <span class="text-xs text-gray-400">↓ Total Pengeluaran</span>
            </div>
            <p class="text-2xl font-black" style="color: #C0392B;">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </p>
            @if($totalHPP > 0)
            <p class="text-xs text-gray-400 mt-1">
                HPP: Rp {{ number_format($totalHPP, 0, ',', '.') }}
            </p>
            @endif
        </div>

        {{-- Total HPP --}}
        <div class="stat-card">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center icon-hpp">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="text-xs text-gray-400">⚙ Total HPP</span>
            </div>
            <p class="text-2xl font-black" style="color: #BA7517;">
                Rp {{ number_format($totalHPP, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Pembelian stok</p>
        </div>

        {{-- Laba/Rugi --}}
        @php $labaPositif = $labaRugiBersih >= 0; @endphp
        <div class="stat-card {{ $labaPositif ? '' : 'border-l-4' }}"
             style="{{ $labaPositif ? 'border-left: 4px solid #1D9E75;' : 'border-left: 4px solid #C0392B;' }}">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $labaPositif ? 'icon-profit' : 'icon-loss' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-xs text-gray-400">✦ Laba Bersih</span>
            </div>
            <p class="text-2xl font-black" style="color: {{ $labaPositif ? '#1D9E75' : '#C0392B' }};">
                {{ $labaRugiBersih < 0 ? '-' : '' }}Rp {{ number_format(abs($labaRugiBersih), 0, ',', '.') }}
            </p>
            <p class="text-xs mt-1" style="color: {{ $labaPositif ? '#1D9E75' : '#C0392B' }};">
                {{ $labaPositif ? '▲ Untung' : '▼ Rugi' }}
                {{ \Carbon\Carbon::create($year, $month)->translatedFormat('M Y') }}
            </p>
        </div>
    </div>

    {{-- ── Chart + Mini Stats ──────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Chart Bar --}}
        <div class="xl:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Pemasukan vs Pengeluaran</h3>
                    <p class="text-xs text-gray-400 mt-0.5">6 bulan terakhir</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm inline-block" style="background:#FFEA6C;"></span>
                        Pemasukan
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm inline-block bg-gray-300"></span>
                        Pengeluaran
                    </span>
                    <a href="{{ route('laporan.arus-kas') }}"
                       class="text-xs font-semibold" style="color: #A07800;">
                        Lihat detail →
                    </a>
                </div>
            </div>
            <div class="relative h-56">
                <canvas id="chartPemasukanPengeluaran"></canvas>
            </div>
        </div>

        {{-- Komposisi Pengeluaran --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm mb-4">Komposisi Pengeluaran</h3>
            @php
                $totalAll = $totalPemasukan + $totalPengeluaran;
                $hppPct   = $totalPengeluaran > 0 ? round($totalHPP / $totalPengeluaran * 100) : 0;
                $opsPct   = $totalPengeluaran > 0 ? round(($totalPengeluaran - $totalHPP) / $totalPengeluaran * 100) : 0;
            @endphp

            {{-- Donut Chart --}}
            <div class="flex justify-center mb-4">
                <div class="relative w-32 h-32">
                    <canvas id="chartDonut"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-lg font-black" style="color: #BA7517;">{{ $hppPct }}%</p>
                            <p class="text-xs text-gray-400">HPP</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full inline-block" style="background:#BA7517;"></span>
                        HPP stok
                    </span>
                    <span class="font-semibold" style="color:#BA7517;">{{ $hppPct }}%</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full inline-block" style="background:#1D9E75;"></span>
                        Operasional
                    </span>
                    <span class="font-semibold" style="color:#1D9E75;">{{ $opsPct }}%</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('laporan.laba-rugi') }}"
                   class="text-xs font-semibold flex items-center gap-1" style="color: #A07800;">
                    Lihat Laba Rugi
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- ── Transaksi Terbaru ───────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h3 class="font-bold text-gray-800 text-sm">Transaksi Terbaru</h3>
            <a href="{{ route('transaksi.index') }}"
               class="text-xs font-semibold" style="color: #A07800;">Lihat semua →</a>
        </div>
        <table class="w-full data-table text-sm">
            <thead>
                <tr>
                    <th class="w-28">Tanggal</th>
                    <th>Keterangan</th>
                    <th>Kategori</th>
                    <th class="text-right">Nominal</th>
                    <th>Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $trx)
                <tr>
                    <td class="text-gray-500 text-xs">{{ $trx->transaction_date->format('d M Y') }}</td>
                    <td class="text-gray-800 max-w-xs">
                        <div class="truncate">{{ $trx->description }}</div>
                        @if($trx->reference_number)
                        <div class="text-xs text-gray-400">{{ $trx->reference_number }}</div>
                        @endif
                    </td>
                    <td>
                        @if($trx->category?->is_hpp)
                        <span class="badge-hpp">HPP</span>
                        @elseif($trx->type === 'pemasukan')
                        <span class="badge-pemasukan">{{ $trx->category?->name ?? '—' }}</span>
                        @else
                        <span class="badge-pengeluaran">{{ $trx->category?->name ?? '—' }}</span>
                        @endif
                    </td>
                    <td class="text-right font-bold whitespace-nowrap"
                        style="color: {{ $trx->type === 'pemasukan' ? '#1D9E75' : '#C0392B' }};">
                        {{ $trx->type === 'pengeluaran' ? '-' : '+' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </td>
                    <td class="text-gray-400 text-xs">{{ $trx->creator?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-gray-400">
                        <p class="text-sm">Belum ada transaksi bulan ini</p>
                        <a href="{{ route('transaksi.create') }}" class="text-xs mt-1 inline-block font-semibold" style="color:#A07800;">
                            + Tambah sekarang
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
// ── Bar Chart ──────────────────────────────────────────────────────
const chartData = @json($chartData);
new Chart(document.getElementById('chartPemasukanPengeluaran').getContext('2d'), {
    type: 'bar',
    data: {
        labels: chartData.map(d => d.label),
        datasets: [
            {
                label: 'Pemasukan',
                data: chartData.map(d => d.pemasukan),
                backgroundColor: '#FFEA6C',
                borderColor: '#F5D300',
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false,
            },
            {
                label: 'Pengeluaran',
                data: chartData.map(d => d.pengeluaran),
                backgroundColor: '#E5E7EB',
                borderColor: '#D1D5DB',
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1C1C1E',
                titleColor: '#AEAEB2',
                bodyColor: '#FFFFFF',
                borderColor: '#3A3A3C',
                borderWidth: 1,
                padding: 10,
                callbacks: {
                    label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    callback: v => v >= 1000000 ? 'Rp '+(v/1000000).toFixed(0)+'jt' : 'Rp '+v.toLocaleString('id-ID'),
                    font: { size: 10 }, color: '#9CA3AF',
                }
            },
            x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#6B7280' } }
        },
        interaction: { mode: 'index', intersect: false },
    }
});

// ── Donut Chart ────────────────────────────────────────────────────
@php
    $hppPctVal = $totalPengeluaran > 0 ? round($totalHPP / $totalPengeluaran * 100) : 0;
    $opsPctVal = 100 - $hppPctVal;
@endphp
new Chart(document.getElementById('chartDonut').getContext('2d'), {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $hppPctVal }}, {{ $opsPctVal }}],
            backgroundColor: ['#BA7517', '#1D9E75'],
            borderWidth: 0,
            hoverOffset: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '68%',
        plugins: { legend: { display: false }, tooltip: { enabled: false } }
    }
});
</script>
@endpush
</x-app-layout>
