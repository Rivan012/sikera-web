@extends('layouts.admin')

@section('title', 'Daftar Pengguna Terdaftar')
@section('page-title', 'Daftar Pengguna Terdaftar')

@section('content')
<div class="space-y-5">
    {{-- Header & Info --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Pengguna SIKERA</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar seluruh akun responden mahasiswa, dosen pembimbing akademik, dan super admin.</p>
        </div>
    </div>

    {{-- Filter Peran & Pencarian --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Filter Tombol Peran --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase mr-1">Filter Peran:</span>
                <a
                    href="{{ route('admin.users.index', ['role' => 'all', 'q' => request('q')]) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $roleFilter === 'all' ? 'bg-brand-500 text-white shadow-theme-xs' : 'border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300' }}"
                >
                    Semua ({{ $totalAllUsers }})
                </a>
                <a
                    href="{{ route('admin.users.index', ['role' => 'mahasiswa', 'q' => request('q')]) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $roleFilter === 'mahasiswa' ? 'bg-brand-500 text-white shadow-theme-xs' : 'border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300' }}"
                >
                    Mahasiswa ({{ $totalMhsUsers }})
                </a>
                <a
                    href="{{ route('admin.users.index', ['role' => 'dosen_pa', 'q' => request('q')]) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $roleFilter === 'dosen_pa' ? 'bg-brand-500 text-white shadow-theme-xs' : 'border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300' }}"
                >
                    Dosen PA ({{ $totalDosenUsers }})
                </a>
                <a
                    href="{{ route('admin.users.index', ['role' => 'admin', 'q' => request('q')]) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $roleFilter === 'admin' ? 'bg-brand-500 text-white shadow-theme-xs' : 'border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300' }}"
                >
                    Super Admin ({{ $totalAdminUsers }})
                </a>
            </div>

            {{-- Form Pencarian Keyword --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="role" value="{{ $roleFilter }}">
                <div class="relative w-full sm:w-60">
                    <input
                        type="text"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="Cari nama, NIM, email..."
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white"
                    >
                </div>
                <button type="submit" class="rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-black transition dark:bg-gray-700">
                    Cari
                </button>
                @if($searchQuery)
                <a href="{{ route('admin.users.index', ['role' => $roleFilter]) }}" class="text-xs text-error-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel Daftar Pengguna --}}
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Daftar Pengguna ({{ $users->count() }} Data Ditemukan)</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar akun terdaftar lengkap dengan status riset dan demografi.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase font-semibold">
                    <tr>
                        <th class="px-5 py-3">Pengguna</th>
                        <th class="px-5 py-3">NIM / NIP</th>
                        <th class="px-5 py-3">Peran (Role)</th>
                        <th class="px-5 py-3">Program Studi &amp; Fakultas</th>
                        <th class="px-5 py-3 text-center">Usia / JK</th>
                        <th class="px-5 py-3">Agama</th>
                        <th class="px-5 py-3 text-center">Pre-Test</th>
                        <th class="px-5 py-3 text-center">Post-Test</th>
                        <th class="px-5 py-3 text-center">Poin</th>
                        <th class="px-5 py-3">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                    @forelse($users as $u)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50">
                        {{-- Profil --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $u->role === 'admin' ? 'bg-success-100 text-success-700' : ($u->role === 'dosen_pa' ? 'bg-warning-100 text-warning-700' : 'bg-brand-100 text-brand-700') }} font-bold text-xs">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $u->name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $u->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- NIM / NIP --}}
                        <td class="px-5 py-3.5 font-mono text-gray-600 dark:text-gray-400 font-medium">
                            {{ $u->nim ?? '-' }}
                        </td>

                        {{-- Peran Badge --}}
                        <td class="px-5 py-3.5">
                            @if($u->role === 'admin')
                            <span class="rounded-full bg-success-50 px-2.5 py-0.5 text-[11px] font-bold text-success-700 dark:bg-success-500/15 dark:text-success-400">
                                Super Admin
                            </span>
                            @elseif($u->role === 'dosen_pa')
                            <span class="rounded-full bg-warning-50 px-2.5 py-0.5 text-[11px] font-bold text-warning-700 dark:bg-warning-500/15 dark:text-warning-400">
                                Dosen PA
                            </span>
                            @else
                            <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-[11px] font-bold text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
                                Mahasiswa
                            </span>
                            @endif
                        </td>

                        {{-- Prodi & Fakultas --}}
                        <td class="px-5 py-3.5 max-w-[200px] truncate">
                            <p class="font-medium text-gray-800 dark:text-white">{{ $u->prodi ?? '-' }}</p>
                            <p class="text-[11px] text-gray-400 truncate">{{ $u->fakultas ?? '-' }}</p>
                        </td>

                        {{-- Usia / JK --}}
                        <td class="px-5 py-3.5 text-center">
                            {{ $u->usia ? $u->usia . ' th' : '-' }} &bull; <span class="font-bold">{{ $u->gender ?? '-' }}</span>
                        </td>

                        {{-- Agama --}}
                        <td class="px-5 py-3.5">{{ $u->agama ?? '-' }}</td>

                        {{-- Status Pre-Test --}}
                        <td class="px-5 py-3.5 text-center">
                            @if($u->pretest_completed)
                            <span class="rounded-full bg-success-50 px-2 py-0.5 text-[10px] font-bold text-success-700 dark:bg-success-500/15 dark:text-success-400">✓ Selesai</span>
                            @else
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-500 dark:bg-gray-800">Belum</span>
                            @endif
                        </td>

                        {{-- Status Post-Test --}}
                        <td class="px-5 py-3.5 text-center">
                            @if($u->posttest_completed)
                            <span class="rounded-full bg-success-50 px-2 py-0.5 text-[10px] font-bold text-success-700 dark:bg-success-500/15 dark:text-success-400">✓ Selesai</span>
                            @else
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-500 dark:bg-gray-800">Belum</span>
                            @endif
                        </td>

                        {{-- Poin --}}
                        <td class="px-5 py-3.5 text-center font-bold text-brand-600 dark:text-brand-400">
                            {{ $u->points }}
                        </td>

                        {{-- Tanggal Registrasi --}}
                        <td class="px-5 py-3.5 text-gray-400 text-[11px]">
                            {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-8 text-center text-xs text-gray-400">
                            Tidak ada pengguna yang sesuai dengan filter peran atau pencarian yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
