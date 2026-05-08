<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminDashboard($request);
        }

        return $this->petugasDashboard();
    }

    private function adminDashboard(Request $request): View
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        // ── Stat Cards ────────────────────────────────────────────
        $totalPemasukan = Transaction::filterByDateRange($startOfMonth, $endOfMonth)
            ->pemasukan()->sum('amount');

        $totalPengeluaran = Transaction::filterByDateRange($startOfMonth, $endOfMonth)
            ->pengeluaran()->sum('amount');

        $totalHPP = Transaction::filterByDateRange($startOfMonth, $endOfMonth)
            ->hpp()->sum('amount');

        $labaRugiBersih = $totalPemasukan - $totalPengeluaran;

        // ── Chart: 6 Bulan Terakhir ───────────────────────────────
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();

            $chartData[] = [
                'label'       => $date->translatedFormat('M Y'),
                'pemasukan'   => (float) Transaction::filterByDateRange($start, $end)->pemasukan()->sum('amount'),
                'pengeluaran' => (float) Transaction::filterByDateRange($start, $end)->pengeluaran()->sum('amount'),
            ];
        }

        // ── 10 Transaksi Terbaru ──────────────────────────────────
        $recentTransactions = Transaction::with(['category', 'creator'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'totalHPP',
            'labaRugiBersih',
            'chartData',
            'recentTransactions',
            'month',
            'year',
        ));
    }

    private function petugasDashboard(): View
    {
        $userId = Auth::id();
        $today  = now()->toDateString();

        // Transaksi hari ini oleh petugas ini
        $todayTransactions = Transaction::with('category')
            ->where('created_by', $userId)
            ->whereDate('transaction_date', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        // Total hari ini
        $todayPemasukan   = $todayTransactions->where('type', 'pemasukan')->sum('amount');
        $todayPengeluaran = $todayTransactions->where('type', 'pengeluaran')->sum('amount');

        // Riwayat 7 hari terakhir
        $recentTransactions = Transaction::with('category')
            ->where('created_by', $userId)
            ->where('transaction_date', '>=', now()->subDays(6)->toDateString())
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.petugas', compact(
            'todayTransactions',
            'todayPemasukan',
            'todayPengeluaran',
            'recentTransactions',
        ));
    }
}
