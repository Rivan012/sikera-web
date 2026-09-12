@extends('layouts.admin')

@section('title', 'Daftar Pengguna Terdaftar')
@section('page-title', 'Daftar Pengguna Terdaftar')

@section('content')
<div
    x-data="{
        createModalOpen: false,
        editModalOpen: false,
        deleteModalOpen: false,
        deleteUser: { id: null, name: '', email: '' },
        editForm: {
            id: null,
            name: '',
            initials: '',
            email: '',
            nim: '',
            role: 'mahasiswa',
            usia: '',
            gender: '',
            agama: '',
            prodi: '',
            fakultas: '',
            pendidikan_terakhir: '',
            points: 0,
            is_biodata_filled: true,
            pretest_completed: false,
            posttest_completed: false
        },
        openCreateModal() {
            this.createModalOpen = true;
        },
        openEditModal(user) {
            this.editForm = {
                id: user.id,
                name: user.name || '',
                initials: user.initials || '',
                email: user.email || '',
                nim: user.nim || '',
                role: user.role || 'mahasiswa',
                usia: user.usia || '',
                gender: user.gender || '',
                agama: user.agama || '',
                prodi: user.prodi || '',
                fakultas: user.fakultas || '',
                pendidikan_terakhir: user.pendidikan_terakhir || '',
                points: user.points || 0,
                is_biodata_filled: Boolean(user.is_biodata_filled),
                pretest_completed: Boolean(user.pretest_completed),
                posttest_completed: Boolean(user.posttest_completed)
            };
            this.editModalOpen = true;
        },
        openDeleteModal(user) {
            this.deleteUser = {
                id: user.id,
                name: user.name,
                email: user.email
            };
            this.deleteModalOpen = true;
        }
    }"
    @keydown.escape.window="createModalOpen = false; editModalOpen = false; deleteModalOpen = false"
    class="space-y-5"
>
    {{-- Header & Info --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Pengguna SIKERA</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar seluruh akun responden mahasiswa, dosen pembimbing akademik, dan super admin.</p>
        </div>
        <button
            type="button"
            @click="openCreateModal()"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-theme-xs hover:bg-brand-600 transition"
        >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            Tambah Pengguna Baru
        </button>
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
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar akun terdaftar lengkap dengan status riset, demografi, dan menu tindakan.</p>
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
                        <th class="px-5 py-3 text-center">Aksi</th>
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

                        {{-- Aksi (Edit & Hapus) --}}
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Tombol Edit --}}
                                <button
                                    type="button"
                                    @click="openEditModal({
                                        id: {{ $u->id }},
                                        name: '{{ addslashes($u->name) }}',
                                        initials: '{{ addslashes($u->initials ?? '') }}',
                                        email: '{{ addslashes($u->email) }}',
                                        nim: '{{ addslashes($u->nim ?? '') }}',
                                        role: '{{ $u->role }}',
                                        usia: '{{ $u->usia ?? '' }}',
                                        gender: '{{ $u->gender ?? '' }}',
                                        agama: '{{ addslashes($u->agama ?? '') }}',
                                        prodi: '{{ addslashes($u->prodi ?? '') }}',
                                        fakultas: '{{ addslashes($u->fakultas ?? '') }}',
                                        pendidikan_terakhir: '{{ addslashes($u->pendidikan_terakhir ?? '') }}',
                                        points: {{ $u->points ?? 0 }},
                                        is_biodata_filled: {{ $u->is_biodata_filled ? 1 : 0 }},
                                        pretest_completed: {{ $u->pretest_completed ? 1 : 0 }},
                                        posttest_completed: {{ $u->posttest_completed ? 1 : 0 }}
                                    })"
                                    class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-600 hover:border-brand-500 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:text-brand-400 transition"
                                    title="Edit Pengguna"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>

                                {{-- Tombol Hapus --}}
                                @if($u->id !== auth()->id())
                                <button
                                    type="button"
                                    @click="openDeleteModal({ id: {{ $u->id }}, name: '{{ addslashes($u->name) }}', email: '{{ addslashes($u->email) }}' })"
                                    class="rounded-lg border border-gray-200 bg-white p-1.5 text-error-500 hover:border-error-500 hover:bg-error-50 dark:border-gray-700 dark:bg-gray-800 dark:text-error-400 dark:hover:bg-error-950/30 transition"
                                    title="Hapus Pengguna"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                                @else
                                <span class="rounded-lg border border-gray-100 bg-gray-50 p-1.5 text-gray-300 dark:border-gray-800 dark:bg-gray-800/40 dark:text-gray-600 cursor-not-allowed" title="Akun Anda Sendiri">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-5 py-8 text-center text-xs text-gray-400">
                            Tidak ada pengguna yang sesuai dengan filter peran atau pencarian yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH PENGGUNA BARU --}}
    <div
        x-show="createModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs overflow-y-auto"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.outside="createModalOpen = false"
            class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                <div class="flex items-center gap-2.5">
                    <span class="rounded-lg bg-brand-50 p-2 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Tambah Pengguna Baru</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Daftarkan akun mahasiswa responden, dosen PA, atau admin baru ke SIKERA.</p>
                    </div>
                </div>
                <button @click="createModalOpen = false" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-5 space-y-4">
                @csrf

                {{-- Bagian 1: Identitas Akun --}}
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">1. Identitas Akun &amp; Peran</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-error-500">*</span></label>
                            <input type="text" name="name" required placeholder="Contoh: Siti Rahmawati" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Inisial Nama (Opsional)</label>
                            <input type="text" name="initials" placeholder="Otomatis dibuat jika kosong" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Alamat Email <span class="text-error-500">*</span></label>
                            <input type="email" name="email" required placeholder="email@unib.ac.id" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Peran Akun (Role) <span class="text-error-500">*</span></label>
                            <select name="role" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="mahasiswa">Mahasiswa (Responden Riset)</option>
                                <option value="dosen_pa">Dosen Pembimbing Akademik (PA)</option>
                                <option value="admin">Super Admin / Pengelola</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">NIM / NIP / Kode Identitas</label>
                            <input type="text" name="nim" placeholder="Contoh: G1A021001" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Usia (Tahun)</label>
                            <input type="number" name="usia" min="10" max="100" placeholder="20" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Data Akademik & Demografi --}}
                <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">2. Demografi &amp; Akademik</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                            <select name="gender" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Agama</label>
                            <input type="text" name="agama" placeholder="Contoh: Islam" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Fakultas</label>
                            <input type="text" name="fakultas" placeholder="Fakultas Kedokteran &amp; Ilmu Kesehatan" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Program Studi</label>
                            <input type="text" name="prodi" placeholder="S1 Kesehatan Masyarakat" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="SMA/SMK Sederajat">SMA / SMK Sederajat</option>
                            <option value="Diploma (D3/D4)">Diploma (D3/D4)</option>
                            <option value="Sarjana (S1)" selected>Sarjana (S1)</option>
                            <option value="Magister (S2)">Magister (S2)</option>
                            <option value="Doktor (S3)">Doktor (S3)</option>
                        </select>
                    </div>
                </div>

                {{-- Bagian 3: Kata Sandi --}}
                <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">3. Keamanan Sandi Akun</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Kata Sandi <span class="text-error-500">*</span></label>
                            <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Konfirmasi Sandi <span class="text-error-500">*</span></label>
                            <input type="password" name="password_confirmation" required minlength="6" placeholder="Ulangi kata sandi" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- Bagian 4: Status Riset & Gamifikasi --}}
                <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">4. Status Riset &amp; Poin Awal</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-center">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="create_is_biodata_filled" name="is_biodata_filled" value="1" checked class="rounded text-brand-600 focus:ring-brand-500">
                            <label for="create_is_biodata_filled" class="text-xs text-gray-700 dark:text-gray-300">Biodata Lengkap</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="create_pretest_completed" name="pretest_completed" value="1" class="rounded text-brand-600 focus:ring-brand-500">
                            <label for="create_pretest_completed" class="text-xs text-gray-700 dark:text-gray-300">Pre-Test Selesai</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="create_posttest_completed" name="posttest_completed" value="1" class="rounded text-brand-600 focus:ring-brand-500">
                            <label for="create_posttest_completed" class="text-xs text-gray-700 dark:text-gray-300">Post-Test Selesai</label>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 mb-0.5">Poin Awal</label>
                            <input type="number" name="points" value="0" min="0" class="w-full rounded-lg border border-gray-300 bg-transparent px-2.5 py-1 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="createModalOpen = false" class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-brand-500 px-5 py-2 text-xs font-bold text-white hover:bg-brand-600 transition shadow-theme-xs">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT DATA PENGGUNA --}}
    <div
        x-show="editModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs overflow-y-auto"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.outside="editModalOpen = false"
            class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                <div class="flex items-center gap-2.5">
                    <span class="rounded-lg bg-amber-50 p-2 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Edit Data Pengguna</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui profil, peran, akademik, kata sandi, atau status riset akun.</p>
                    </div>
                </div>
                <button @click="editModalOpen = false" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form method="POST" :action="'{{ url('/admin/users') }}/' + editForm.id" class="mt-5 space-y-4">
                @csrf
                @method('PUT')

                {{-- Bagian 1: Identitas Akun --}}
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">1. Identitas Akun &amp; Peran</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-error-500">*</span></label>
                            <input type="text" name="name" x-model="editForm.name" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Inisial Nama</label>
                            <input type="text" name="initials" x-model="editForm.initials" placeholder="Otomatis jika kosong" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Alamat Email <span class="text-error-500">*</span></label>
                            <input type="email" name="email" x-model="editForm.email" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Peran Akun (Role) <span class="text-error-500">*</span></label>
                            <select name="role" x-model="editForm.role" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="mahasiswa">Mahasiswa (Responden Riset)</option>
                                <option value="dosen_pa">Dosen Pembimbing Akademik (PA)</option>
                                <option value="admin">Super Admin / Pengelola</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">NIM / NIP / Kode Identitas</label>
                            <input type="text" name="nim" x-model="editForm.nim" placeholder="Contoh: G1A021001" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Usia (Tahun)</label>
                            <input type="number" name="usia" x-model="editForm.usia" min="10" max="100" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Data Akademik & Demografi --}}
                <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">2. Demografi &amp; Akademik</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                            <select name="gender" x-model="editForm.gender" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Agama</label>
                            <input type="text" name="agama" x-model="editForm.agama" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Fakultas</label>
                            <input type="text" name="fakultas" x-model="editForm.fakultas" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Program Studi</label>
                            <input type="text" name="prodi" x-model="editForm.prodi" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" x-model="editForm.pendidikan_terakhir" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="SMA/SMK Sederajat">SMA / SMK Sederajat</option>
                            <option value="Diploma (D3/D4)">Diploma (D3/D4)</option>
                            <option value="Sarjana (S1)">Sarjana (S1)</option>
                            <option value="Magister (S2)">Magister (S2)</option>
                            <option value="Doktor (S3)">Doktor (S3)</option>
                        </select>
                    </div>
                </div>

                {{-- Bagian 3: Ganti Kata Sandi (Opsional) --}}
                <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">3. Ubah Kata Sandi</h4>
                        <span class="text-[10px] text-gray-400 italic">Kosongkan jika sandi tidak diubah</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Kata Sandi Baru</label>
                            <input type="password" name="password" minlength="6" placeholder="Minimal 6 karakter baru" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Konfirmasi Sandi Baru</label>
                            <input type="password" name="password_confirmation" minlength="6" placeholder="Ulangi sandi baru" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- Bagian 4: Status Riset & Gamifikasi --}}
                <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">4. Status Riset &amp; Poin</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-center">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="edit_is_biodata_filled" name="is_biodata_filled" value="1" x-model="editForm.is_biodata_filled" class="rounded text-brand-600 focus:ring-brand-500">
                            <label for="edit_is_biodata_filled" class="text-xs text-gray-700 dark:text-gray-300">Biodata Lengkap</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="edit_pretest_completed" name="pretest_completed" value="1" x-model="editForm.pretest_completed" class="rounded text-brand-600 focus:ring-brand-500">
                            <label for="edit_pretest_completed" class="text-xs text-gray-700 dark:text-gray-300">Pre-Test Selesai</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="edit_posttest_completed" name="posttest_completed" value="1" x-model="editForm.posttest_completed" class="rounded text-brand-600 focus:ring-brand-500">
                            <label for="edit_posttest_completed" class="text-xs text-gray-700 dark:text-gray-300">Post-Test Selesai</label>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 mb-0.5">Poin</label>
                            <input type="number" name="points" x-model="editForm.points" min="0" class="w-full rounded-lg border border-gray-300 bg-transparent px-2.5 py-1 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="editModalOpen = false" class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-brand-500 px-5 py-2 text-xs font-bold text-white hover:bg-brand-600 transition shadow-theme-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS PENGGUNA --}}
    <div
        x-show="deleteModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.outside="deleteModalOpen = false"
            class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex items-center gap-3 mb-3">
                <span class="rounded-xl bg-error-50 p-2.5 text-error-600 dark:bg-error-950 dark:text-error-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Hapus Akun Pengguna</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Konfirmasi tindakan penghapusan akun.</p>
                </div>
            </div>

            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed mb-4">
                Apakah Anda yakin ingin menghapus akun pengguna <strong class="text-gray-900 dark:text-white" x-text="deleteUser.name"></strong> (<span class="font-mono" x-text="deleteUser.email"></span>)? Tindakan ini akan menghapus seluruh data kuesioner dan aktivitas riset pengguna tersebut. Tindakan ini tidak dapat dibatalkan.
            </p>

            <form method="POST" :action="'{{ url('/admin/users') }}/' + deleteUser.id" class="flex items-center justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModalOpen = false" class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-error-600 px-4 py-2 text-xs font-bold text-white hover:bg-error-700 transition shadow-theme-xs">
                    Ya, Hapus Pengguna
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
