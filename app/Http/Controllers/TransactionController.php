<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * index() — Daftar transaksi
     * Admin : semua transaksi dari semua user
     * Petugas: hanya transaksi yang dia buat (created_by = auth user)
     */
    public function index(Request $request): View
    {
        $query = Transaction::with(['category', 'creator'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Petugas hanya lihat transaksi milik sendiri
        if (Auth::user()->hasRole('petugas')) {
            $query->where('created_by', Auth::id());
        }

        // Filter tanggal mulai
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        // Filter tanggal akhir
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        // Filter tipe (pemasukan / pengeluaran)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter berdasarkan kata kunci (description / reference_number)
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('description', 'like', "%{$keyword}%")
                  ->orWhere('reference_number', 'like', "%{$keyword}%");
            });
        }

        $transactions = $query->paginate(15)->withQueryString();

        // Ringkasan total — query terpisah dengan filter yang sama
        $summaryBase = Transaction::query();
        if (Auth::user()->hasRole('petugas')) {
            $summaryBase->where('created_by', Auth::id());
        }
        if ($request->filled('date_from')) {
            $summaryBase->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $summaryBase->where('transaction_date', '<=', $request->date_to);
        }
        if ($request->filled('category_id')) {
            $summaryBase->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $kw = $request->search;
            $summaryBase->where(function ($q) use ($kw) {
                $q->where('description', 'like', "%{$kw}%")
                  ->orWhere('reference_number', 'like', "%{$kw}%");
            });
        }
        $totalPemasukan   = (clone $summaryBase)->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = (clone $summaryBase)->where('type', 'pengeluaran')->sum('amount');

        $categories = Category::aktif()->orderBy('type')->orderBy('name')->get();

        return view('transaksi.index', compact(
            'transactions',
            'categories',
            'totalPemasukan',
            'totalPengeluaran',
        ));
    }

    /**
     * create() — Form tambah transaksi baru
     */
    public function create(): View
    {
        $categories = Category::aktif()->orderBy('type')->orderBy('name')->get();
        return view('transaksi.create', compact('categories'));
    }

    /**
     * store() — Simpan transaksi baru
     */
    public function store(TransactionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Transaction::create([
            'user_id'          => Auth::id(),
            'category_id'      => $validated['category_id'],
            'type'             => $validated['type'],
            'amount'           => $validated['amount'],
            'description'      => $validated['description'],
            'transaction_date' => $validated['transaction_date'],
            'reference_number' => $validated['reference_number'] ?? null,
            'note'             => $validated['note'] ?? null,
            'created_by'       => Auth::id(),
        ]);

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * show() — Detail transaksi (opsional, redirect ke edit)
     */
    public function show(Transaction $transaksi)
    {
        return redirect()->route('transaksi.edit', $transaksi);
    }

    /**
     * edit() — Form edit transaksi
     * Petugas: hanya bisa edit jika tanggal hari ini & transaksi milik sendiri
     * Admin: bisa edit semua
     */
    public function edit(Transaction $transaksi): View|RedirectResponse
    {
        $this->authorizeEdit($transaksi);

        $categories = Category::aktif()->orderBy('type')->orderBy('name')->get();
        return view('transaksi.edit', compact('transaksi', 'categories'));
    }

    /**
     * update() — Simpan perubahan transaksi
     */
    public function update(TransactionRequest $request, Transaction $transaksi): RedirectResponse
    {
        $this->authorizeEdit($transaksi);

        $validated = $request->validated();

        $transaksi->update([
            'category_id'      => $validated['category_id'],
            'type'             => $validated['type'],
            'amount'           => $validated['amount'],
            'description'      => $validated['description'],
            'transaction_date' => $validated['transaction_date'],
            'reference_number' => $validated['reference_number'] ?? null,
            'note'             => $validated['note'] ?? null,
        ]);

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * destroy() — Hapus transaksi (soft delete)
     * KHUSUS Admin dengan permission delete-transactions
     */
    public function destroy(Transaction $transaksi): RedirectResponse
    {
        $this->authorize('delete-transactions');

        $transaksi->delete();

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────

    /**
     * Otorisasi edit: Petugas hanya bisa edit transaksi sendiri & hari ini.
     */
    private function authorizeEdit(Transaction $transaksi): void
    {
        if (Auth::user()->hasRole('petugas')) {
            // Cek kepemilikan
            if ($transaksi->created_by !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit transaksi ini.');
            }
            // Cek tanggal hari ini saja
            if ($transaksi->transaction_date->toDateString() !== now()->toDateString()) {
                abort(403, 'Petugas hanya dapat mengedit transaksi yang dibuat hari ini.');
            }
        }
    }
}
