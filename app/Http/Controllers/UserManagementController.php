<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserManagementRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * index() — Daftar semua user (Petugas) dengan filter role
     */
    public function index(Request $request): View
    {
        $query = User::with('roles')
            ->orderBy('name');

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter pencarian nama / email
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('pengguna.index', compact('users'));
    }

    /**
     * create() — Form tambah Petugas baru
     */
    public function create(): View
    {
        return view('pengguna.create');
    }

    /**
     * store() — Simpan Petugas baru
     * Admin hanya bisa tambah user dengan role=petugas
     */
    public function store(UserManagementRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'password'  => Hash::make($validated['password']),
            'role'      => 'petugas', // Selalu petugas
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Assign Spatie role
        $user->assignRole('petugas');

        return redirect()->route('pengguna.index')
            ->with('success', "Akun Petugas {$user->name} berhasil ditambahkan.");
    }

    /**
     * show() — Redirect ke edit
     */
    public function show(User $pengguna): RedirectResponse
    {
        return redirect()->route('pengguna.edit', $pengguna);
    }

    /**
     * edit() — Form edit data user
     */
    public function edit(User $pengguna): View
    {
        return view('pengguna.edit', compact('pengguna'));
    }

    /**
     * update() — Simpan perubahan data user
     * Password hanya diupdate jika diisi
     */
    public function update(UserManagementRequest $request, User $pengguna): RedirectResponse
    {
        $validated = $request->validated();

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        // Update password hanya jika diisi
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $pengguna->update($updateData);

        return redirect()->route('pengguna.index')
            ->with('success', "Data pengguna {$pengguna->name} berhasil diperbarui.");
    }

    /**
     * destroy() — Hapus user (hanya jika belum punya transaksi)
     */
    public function destroy(User $pengguna): RedirectResponse
    {
        // Cegah Admin menghapus dirinya sendiri
        if ($pengguna->id === auth()->id()) {
            return redirect()->route('pengguna.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // Cegah hapus user yang punya transaksi
        $transactionCount = $pengguna->createdTransactions()->count();
        if ($transactionCount > 0) {
            return redirect()->route('pengguna.index')
                ->with('error', "Pengguna tidak dapat dihapus karena memiliki {$transactionCount} transaksi. Nonaktifkan akun sebagai gantinya.");
        }

        $name = $pengguna->name;
        $pengguna->delete();

        return redirect()->route('pengguna.index')
            ->with('success', "Akun {$name} berhasil dihapus.");
    }

    /**
     * toggleActive() — Aktifkan / nonaktifkan akun Petugas
     */
    public function toggleActive(User $pengguna): RedirectResponse
    {
        // Cegah Admin menonaktifkan dirinya sendiri
        if ($pengguna->id === auth()->id()) {
            return redirect()->route('pengguna.index')
                ->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $pengguna->update(['is_active' => ! $pengguna->is_active]);

        $status = $pengguna->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('pengguna.index')
            ->with('success', "Akun {$pengguna->name} berhasil {$status}.");
    }
}
