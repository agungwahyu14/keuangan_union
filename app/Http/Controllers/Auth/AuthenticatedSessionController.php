<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * - Validasi is_active user sebelum login
     * - Redirect: Admin → /dashboard | Petugas → /transaksi
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Cek apakah akun aktif setelah autentikasi berhasil
        if (! Auth::user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi Administrator.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Redirect berdasarkan role
        if (Auth::user()->hasRole('admin')) {
            return redirect()->intended(route('dashboard'));
        }

        // Petugas diarahkan ke halaman transaksi
        return redirect()->intended(route('transaksi.index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
