<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * index() — Daftar semua kategori
     */
    public function index(Request $request): View
    {
        $query = Category::withCount('transactions')
            ->orderBy('type')
            ->orderBy('name');

        // Filter tipe
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $categories = $query->paginate(15)->withQueryString();

        return view('kategori.index', compact('categories'));
    }

    /**
     * create() — Form tambah kategori
     */
    public function create(): View
    {
        return view('kategori.create');
    }

    /**
     * store() — Simpan kategori baru
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * show() — Redirect ke edit
     */
    public function show(Category $kategori): RedirectResponse
    {
        return redirect()->route('kategori.edit', $kategori);
    }

    /**
     * edit() — Form edit kategori
     */
    public function edit(Category $kategori): View
    {
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * update() — Simpan perubahan kategori
     */
    public function update(CategoryRequest $request, Category $kategori): RedirectResponse
    {
        $kategori->update($request->validated());

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * destroy() — Nonaktifkan kategori (TIDAK hapus jika ada transaksi)
     * Soft disable: set is_active = false jika ada transaksi terkait
     * Hard delete: hanya jika belum pernah digunakan
     */
    public function destroy(Category $kategori): RedirectResponse
    {
        $transactionCount = $kategori->transactions()->count();

        if ($transactionCount > 0) {
            // Ada transaksi terkait → soft disable saja
            $kategori->update(['is_active' => false]);

            return redirect()->route('kategori.index')
                ->with('warning', "Kategori tidak dapat dihapus karena memiliki {$transactionCount} transaksi. Kategori telah dinonaktifkan.");
        }

        // Belum ada transaksi → bisa hapus permanen
        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * toggle() — Aktifkan/nonaktifkan kategori
     */
    public function toggle(Category $kategori): RedirectResponse
    {
        $kategori->update(['is_active' => ! $kategori->is_active]);

        $status = $kategori->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('kategori.index')
            ->with('success', "Kategori berhasil {$status}.");
    }
}
