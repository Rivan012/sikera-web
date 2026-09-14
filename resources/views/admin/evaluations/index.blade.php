@extends('layouts.admin')

@section('title', 'Jawaban Evaluasi & Post-Test')
@section('page-title', 'Hasil & Analisis Jawaban Evaluasi')

@section('content')
<div class="space-y-6">
    {{-- Header & Subtitle --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Analisis &amp; Lembar Jawaban Evaluasi</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                Data terperinci jawaban Pre-Test &amp; Post-Test responden mahasiswa, perbandingan skor, dan kalkulasi N-Gain.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a
                href="{{ route('admin.evaluations.export', ['module' => request('module'), 'q' => request('q')]) }}"
                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-theme-xs hover:bg-emerald-700 transition"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                </svg>
                Ekspor Post-Test (.xlsx)
            </a>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-teal-50 px-3.5 py-2 text-xs font-semibold text-teal-700 shadow-theme-xs dark:bg-teal-950 dark:text-teal-400 border border-teal-200 dark:border-teal-800">
                <span class="h-2 w-2 rounded-full bg-teal-500 animate-pulse"></span>
                Data Evaluasi Aktif
            </span>
        </div>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Card 1: Total Pengisian --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Submission</p>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalEvaluations }}</h4>
            <div class="flex items-center gap-2 mt-2 text-xs">
                <span class="rounded bg-teal-50 px-2 py-0.5 font-semibold text-teal-700 dark:bg-teal-900/40 dark:text-teal-300">
                    {{ $totalPosttests }} Post-Test
                </span>
                <span class="rounded bg-blue-50 px-2 py-0.5 font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    {{ $totalPretests }} Pre-Test
                </span>
            </div>
        </div>

        {{-- Card 2: Rata-Rata Post-Test --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Rata-Rata Nilai Post-Test</p>
            <h4 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                {{ $avgPostScore }} <span class="text-xs font-normal text-gray-400">/ 100</span>
            </h4>
            <p class="text-[11px] text-gray-400 mt-2">
                Pre-Test: <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $avgPreScore }}</span> / 100
            </p>
        </div>

        {{-- Card 3: Rata-Rata N-Gain --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Rata-Rata N-Gain Score</p>
            <h4 class="text-2xl font-bold text-teal-600 dark:text-teal-400 mt-1">
                {{ $avgNGain }}
            </h4>
            <p class="text-[11px] font-semibold text-teal-700 dark:text-teal-400 mt-2">
                Efektivitas: {{ $avgNGain >= 0.7 ? 'Tinggi (g >= 0.7)' : ($avgNGain >= 0.3 ? 'Sedang (0.3 <= g < 0.7)' : 'Rendah (g < 0.3)') }}
            </p>
        </div>

        {{-- Card 4: Distribusi Efektivitas --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Distribusi N-Gain Responden</p>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="rounded bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400" title="Efektivitas Tinggi">
                    Tinggi: {{ $highGainCount }}
                </span>
                <span class="rounded bg-amber-50 px-2 py-1 text-[11px] font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-400" title="Efektivitas Sedang">
                    Sedang: {{ $mediumGainCount }}
                </span>
                <span class="rounded bg-gray-100 px-2 py-1 text-[11px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300" title="Efektivitas Rendah">
                    Rendah: {{ $lowGainCount }}
                </span>
            </div>
            <p class="text-[10px] text-gray-400 mt-2">Kriteria Normalized Gain (Hake, 1998)</p>
        </div>
    </div>

    {{-- Filter Bar & Pencarian --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="GET" action="{{ route('admin.evaluations.index') }}" class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Filter Tipe --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Tipe Evaluasi</label>
                    <select name="type" class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-theme-xs focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="post_test" {{ $typeFilter === 'post_test' ? 'selected' : '' }}>Post-Test Sahaja</option>
                        <option value="pre_test" {{ $typeFilter === 'pre_test' ? 'selected' : '' }}>Pre-Test Sahaja</option>
                    </select>
                </div>

                {{-- Filter Modul --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Lingkup Modul</label>
                    <select name="module" class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-theme-xs focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        <option value="all" {{ $moduleFilter === 'all' ? 'selected' : '' }}>Semua Lingkup</option>
                        <option value="general" {{ $moduleFilter === 'general' ? 'selected' : '' }}>Umum (Keseluruhan)</option>
                        <option value="1" {{ $moduleFilter === '1' ? 'selected' : '' }}>Modul 1 (Anatomi &amp; Fisiologi)</option>
                        <option value="2" {{ $moduleFilter === '2' ? 'selected' : '' }}>Modul 2 (Higienitas Menstruasi)</option>
                        <option value="3" {{ $moduleFilter === '3' ? 'selected' : '' }}>Modul 3 (Gizi &amp; Anemia)</option>
                        <option value="4" {{ $moduleFilter === '4' ? 'selected' : '' }}>Modul 4 (Pencegahan KBGO/TPKS)</option>
                    </select>
                </div>

                {{-- Filter Pencarian --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Cari Mahasiswa</label>
                    <input
                        type="text"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="Nama, NIM, atau Prodi..."
                        class="w-48 sm:w-64 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-800 placeholder-gray-400 shadow-theme-xs focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                </div>
            </div>

            <div class="flex items-center gap-2 self-end lg:self-center">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-theme-xs hover:bg-brand-600 transition"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    Terapkan Filter
                </button>
                @if($typeFilter !== 'all' || $moduleFilter !== 'all' || $searchQuery)
                <a
                    href="{{ route('admin.evaluations.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition"
                >
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Daftar Lembar Jawaban Evaluasi --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Daftar Lembar Jawaban Responden</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Klik tombol "Lihat Detail Jawaban" untuk memeriksa butir soal, kunci, dan pilihan responden.
                </p>
            </div>
            <span class="text-xs font-medium text-gray-400">
                Menampilkan {{ $evaluations->count() }} dari total {{ $evaluations->total() }} baris
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3">Responden</th>
                        <th class="px-4 py-3 text-center">Tipe Evaluasi</th>
                        <th class="px-4 py-3">Lingkup Modul</th>
                        <th class="px-4 py-3 text-center">Skor Evaluasi</th>
                        <th class="px-4 py-3 text-center">N-Gain</th>
                        <th class="px-4 py-3">Waktu Submit</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                    @forelse($evaluations as $item)
                    @php
                        $user = $item->user;
                    @endphp
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/60 transition">
                        {{-- Responden Info --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $user?->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' }} font-bold text-xs">
                                    {{ $user ? strtoupper(substr($user->initials ?? $user->name, 0, 2)) : '??' }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ $user?->name ?? 'Pengguna Dihapus' }}
                                    </p>
                                    <p class="text-[11px] text-gray-400">
                                        {{ $user?->nim ?? '-' }} &bull; {{ $user?->prodi ?? 'Prodi -' }} ({{ $user?->gender === 'L' ? 'L' : 'P' }}/{{ $user?->usia ?? '-' }} thn)
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Tipe Evaluasi --}}
                        <td class="px-4 py-3 text-center">
                            @if($item->type === 'post_test')
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Post-Test
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                Pre-Test
                            </span>
                            @endif
                        </td>

                        {{-- Lingkup Modul --}}
                        <td class="px-4 py-3">
                            @if($item->module_number)
                            <span class="font-medium text-gray-800 dark:text-gray-200">
                                Modul {{ $item->module_number }}
                            </span>
                            @else
                            <span class="rounded bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                Ujian Umum (4 Modul)
                            </span>
                            @endif
                        </td>

                        {{-- Skor --}}
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-bold {{ $item->total_score >= 70 ? 'text-emerald-600 dark:text-emerald-400' : ($item->total_score >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                                {{ $item->total_score }}
                            </span>
                            <span class="text-[10px] text-gray-400">/ 100</span>
                        </td>

                        {{-- N-Gain --}}
                        <td class="px-4 py-3 text-center">
                            @if($item->type === 'post_test' && $item->n_gain_score !== null)
                                <div class="flex flex-col items-center">
                                    <span class="font-mono font-bold text-xs {{ $item->n_gain_score >= 0.7 ? 'text-emerald-600 dark:text-emerald-400' : ($item->n_gain_score >= 0.3 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400') }}">
                                        {{ number_format($item->n_gain_score, 3) }}
                                    </span>
                                    <span class="rounded px-1.5 py-0.2 text-[9px] font-semibold {{ $item->n_gain_score >= 0.7 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($item->n_gain_score >= 0.3 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400') }}">
                                        {{ $item->n_gain_score >= 0.7 ? 'Tinggi' : ($item->n_gain_score >= 0.3 ? 'Sedang' : 'Rendah') }}
                                    </span>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        {{-- Waktu Submit --}}
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            {{ $item->submitted_at ? $item->submitted_at->format('d M Y, H:i') : '-' }}
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3 text-center">
                            <a
                                href="{{ route('admin.evaluations.show', $item->id) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-teal-50 px-3 py-1.5 text-xs font-bold text-teal-700 hover:bg-teal-100 dark:bg-teal-950 dark:text-teal-300 dark:hover:bg-teal-900/60 border border-teal-200 dark:border-teal-800 transition"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                </svg>
                                Lihat Jawaban
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm font-semibold">Belum Ada Data Lembar Jawaban</p>
                                <p class="text-xs text-gray-400 mt-1">Tidak ditemukan hasil evaluasi yang sesuai dengan kriteria filter pencarian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($evaluations->hasPages())
        <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
            {{ $evaluations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
