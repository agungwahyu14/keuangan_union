<?php

namespace App\Http\Controllers;

use App\Exports\CashFlowExport;
use App\Exports\ProfitLossExport;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    // ─── Arus Kas ─────────────────────────────────────────────────────────

    /**
     * Form pilih periode + tampilkan Laporan Arus Kas
     */
    public function arusKas(Request $request): View
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $data = $this->buildCashFlowData($dateFrom, $dateTo);

        return view('laporan.arus-kas', array_merge($data, [
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
        ]));
    }

    // ─── Laba Rugi ────────────────────────────────────────────────────────

    /**
     * Form pilih periode + tampilkan Laporan Laba Rugi Sederhana
     */
    public function labaRugi(Request $request): View
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $data = $this->buildProfitLossData($dateFrom, $dateTo);

        return view('laporan.laba-rugi', array_merge($data, [
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
        ]));
    }

    // ─── Export Excel ─────────────────────────────────────────────────────

    /**
     * Export laporan ke file Excel
     * @param string $type  'arus-kas' | 'laba-rugi'
     */
    public function exportExcel(string $type, Request $request): BinaryFileResponse
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);
        $filename = $this->buildFilename($type, $dateFrom, $dateTo, 'xlsx');

        if ($type === 'arus-kas') {
            $data = $this->buildCashFlowData($dateFrom, $dateTo);
            return Excel::download(
                new CashFlowExport($data, $dateFrom, $dateTo),
                $filename
            );
        }

        $data = $this->buildProfitLossData($dateFrom, $dateTo);
        return Excel::download(
            new ProfitLossExport($data, $dateFrom, $dateTo),
            $filename
        );
    }

    /**
     * Export laporan ke file PDF
     * @param string $type  'arus-kas' | 'laba-rugi'
     */
    public function exportPdf(string $type, Request $request): StreamedResponse|\Illuminate\Http\Response
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);
        $filename = $this->buildFilename($type, $dateFrom, $dateTo, 'pdf');

        if ($type === 'arus-kas') {
            $data = $this->buildCashFlowData($dateFrom, $dateTo);
            $pdf  = Pdf::loadView('laporan.pdf.cashflow', array_merge($data, [
                'dateFrom' => $dateFrom,
                'dateTo'   => $dateTo,
            ]))->setPaper('a4', 'portrait');
        } else {
            $data = $this->buildProfitLossData($dateFrom, $dateTo);
            $pdf  = Pdf::loadView('laporan.pdf.profitloss', array_merge($data, [
                'dateFrom' => $dateFrom,
                'dateTo'   => $dateTo,
            ]))->setPaper('a4', 'portrait');
        }

        return $pdf->download($filename);
    }

    // ─── Data Builders ────────────────────────────────────────────────────

    /**
     * Bangun data Arus Kas
     */
    private function buildCashFlowData(Carbon $dateFrom, Carbon $dateTo): array
    {
        // Pemasukan per kategori
        $pemasukanPerKategori = Transaction::with('category')
            ->filterByDateRange($dateFrom, $dateTo)
            ->pemasukan()
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as jumlah')
            ->groupBy('category_id')
            ->get();

        // Pengeluaran per kategori (semua, termasuk HPP)
        $pengeluaranPerKategori = Transaction::with('category')
            ->filterByDateRange($dateFrom, $dateTo)
            ->pengeluaran()
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as jumlah')
            ->groupBy('category_id')
            ->get();

        // HPP terpisah (disorot)
        $hppPerKategori = Transaction::with('category')
            ->filterByDateRange($dateFrom, $dateTo)
            ->hpp()
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as jumlah')
            ->groupBy('category_id')
            ->get();

        $totalPemasukan   = $pemasukanPerKategori->sum('total');
        $totalPengeluaran = $pengeluaranPerKategori->sum('total');
        $arusKasBersih    = $totalPemasukan - $totalPengeluaran;

        return compact(
            'pemasukanPerKategori',
            'pengeluaranPerKategori',
            'hppPerKategori',
            'totalPemasukan',
            'totalPengeluaran',
            'arusKasBersih',
        );
    }

    /**
     * Bangun data Laba Rugi Sederhana
     */
    private function buildProfitLossData(Carbon $dateFrom, Carbon $dateTo): array
    {
        // 1. Pendapatan per kategori
        $rincianPendapatan = Transaction::with('category')
            ->filterByDateRange($dateFrom, $dateTo)
            ->pemasukan()
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as jumlah')
            ->groupBy('category_id')
            ->get();

        // 2. HPP per kategori
        $rincianHPP = Transaction::with('category')
            ->filterByDateRange($dateFrom, $dateTo)
            ->hpp()
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as jumlah')
            ->groupBy('category_id')
            ->get();

        // 3. Beban Operasional (pengeluaran NON-HPP) per kategori
        $rincianOperasional = Transaction::with('category')
            ->filterByDateRange($dateFrom, $dateTo)
            ->biayaOperasional()
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as jumlah')
            ->groupBy('category_id')
            ->get();

        $totalPendapatan        = $rincianPendapatan->sum('total');
        $totalHPP               = $rincianHPP->sum('total');
        $labaKotor              = $totalPendapatan - $totalHPP;
        $totalBiayaOperasional  = $rincianOperasional->sum('total');
        $labaBersih             = $labaKotor - $totalBiayaOperasional;

        return compact(
            'rincianPendapatan',
            'rincianHPP',
            'rincianOperasional',
            'totalPendapatan',
            'totalHPP',
            'labaKotor',
            'totalBiayaOperasional',
            'labaBersih',
        );
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    /**
     * Resolve tanggal dari request (default: bulan ini)
     * @return array{Carbon, Carbon}
     */
    private function resolveDateRange(Request $request): array
    {
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo   = Carbon::parse($request->date_to)->endOfDay();
        } elseif ($request->filled('month') && $request->filled('year')) {
            $dateFrom = Carbon::create($request->year, $request->month, 1)->startOfMonth();
            $dateTo   = Carbon::create($request->year, $request->month, 1)->endOfMonth();
        } else {
            $dateFrom = now()->startOfMonth();
            $dateTo   = now()->endOfMonth();
        }

        return [$dateFrom, $dateTo];
    }

    private function buildFilename(string $type, Carbon $from, Carbon $to, string $ext): string
    {
        $label = $type === 'arus-kas' ? 'Arus_Kas' : 'Laba_Rugi';
        return "Laporan_{$label}_{$from->format('Ymd')}-{$to->format('Ymd')}.{$ext}";
    }
}
