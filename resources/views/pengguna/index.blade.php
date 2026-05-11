<x-app-layout>
@section('title', 'Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola akun Petugas yang dapat mengakses sistem')

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('pengguna.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="search" placeholder="Cari nama atau email..."
                   value="{{ request('search') }}"
                   class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none w-52">
            <select name="is_active" class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none">
                <option value="">Semua Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-lg"
                    style="background-color: #A07800;"
                    onmouseover="this.style.backgroundColor='#8B6700'"
                    onmouseout="this.style.backgroundColor='#A07800'">Cari</button>
            @if(request()->hasAny(['search','is_active']))
            <a href="{{ route('pengguna.index') }}" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Reset</a>
            @endif
        </form>
        <a href="{{ route('pengguna.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl"
           style="background-color: #FFEA6C; color: #1C1C1E;"
           onmouseover="this.style.backgroundColor='#FFE033'"
           onmouseout="this.style.backgroundColor='#FFEA6C'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Tambah Petugas
        </a>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full data-table text-sm">
            <thead>
                <tr>
                    <th class="w-10">#</th>
                    <th>Nama & Email</th>
                    <th>No. Telepon</th>
                    <th class="text-center">Peran</th>
                    <th class="text-center">Jml Transaksi</th>
                    <th class="text-center">Status</th>
                    <th class="text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                <tr class="{{ !$user->is_active ? 'opacity-60' : '' }}">
                    <td class="text-gray-400 text-xs">{{ $users->firstItem() + $index }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0"
                                 style="background-color: {{ $user->hasRole('admin') ? '#FFEA6C' : '#F3F4F6' }};
                                        color: {{ $user->hasRole('admin') ? '#1C1C1E' : '#6B7280' }};">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $user->name }}
                                    @if($user->id === Auth::id())
                                    <span class="text-xs text-gray-400">(Anda)</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="text-gray-500 text-xs">{{ $user->phone ?: '—' }}</td>
                    <td class="text-center">
                        @if($user->hasRole('admin'))
                        <span class="badge-admin"><i data-lucide="shield-check" class="w-3 h-3 inline-block mr-1"></i> Admin</span>
                        @else
                        <span class="badge-petugas"><i data-lucide="clipboard-list" class="w-3 h-3 inline-block mr-1"></i> Petugas</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @php $txCount = $user->createdTransactions()->count(); @endphp
                        <span class="font-semibold {{ $txCount > 0 ? 'text-gray-800' : 'text-gray-300' }}">
                            {{ number_format($txCount) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($user->id !== Auth::id())
                        <form method="POST" action="{{ route('pengguna.toggle-active', $user) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-xs font-semibold px-3 py-1 rounded-full border transition-all"
                                    style="{{ $user->is_active
                                        ? 'background:#ECFDF5;color:#1D9E75;border-color:#D1FAE5;'
                                        : 'background:#F3F4F6;color:#9CA3AF;border-color:#E5E7EB;' }}"
                                    title="{{ $user->is_active ? 'Klik nonaktifkan' : 'Klik aktifkan' }}">
                                @if($user->is_active)
                                    <i data-lucide="circle" class="w-2 h-2 inline-block mr-1 fill-current"></i> Aktif
                                @else
                                    <i data-lucide="circle" class="w-2 h-2 inline-block mr-1"></i> Nonaktif
                                @endif
                            </button>
                        </form>
                        @else
                        <span class="text-xs font-semibold px-3 py-1 rounded-full"
                              style="background:#ECFDF5;color:#1D9E75;"><i data-lucide="circle" class="w-2 h-2 inline-block mr-1 fill-current"></i> Aktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('pengguna.edit', $user) }}"
                               class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($user->id !== Auth::id())
                            <form method="POST" action="{{ route('pengguna.destroy', $user) }}"
                                  onsubmit="return confirm('Hapus akun {{ $user->name }}? Aksi ini tidak dapat dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl mx-auto mb-3 flex items-center justify-center bg-gray-50">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <p class="text-gray-400 text-sm font-medium">Tidak ada pengguna ditemukan</p>
                        <a href="{{ route('pengguna.create') }}" class="mt-2 inline-block text-sm font-semibold" style="color:#A07800;">
                            + Tambah petugas baru
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-3 flex items-start gap-3">
        <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <p class="text-xs text-blue-800">
            <strong>Hanya Petugas</strong> yang dapat ditambahkan melalui halaman ini. Admin dibuat langsung di database oleh sistem.
            Pengguna yang memiliki transaksi tidak dapat dihapus — gunakan <strong>Nonaktifkan</strong> untuk mencabut akses.
        </p>
    </div>
</div>
</x-app-layout>
