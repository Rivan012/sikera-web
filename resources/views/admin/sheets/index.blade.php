@extends('layouts.admin')

@section('title', 'Lembar Data Google Sheets Riset')
@section('page-title', 'Lembar Data Riset & Integrasi Google Sheets')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Lembar Data Google Sheets</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Data mentah terstruktur responden, skor Pre-Test, Post-Test, dan kalkulasi N-Gain untuk analisis statistik.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a
                href="{{ route('admin.evaluations.index') }}"
                class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-3.5 py-2 text-xs font-bold text-white shadow-theme-xs hover:bg-brand-600 transition"
            >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                </svg>
                Lihat Lembar Jawaban Butir Soal
            </a>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-success-50 px-3.5 py-2 text-xs font-semibold text-success-700 shadow-theme-xs dark:bg-success-950 dark:text-success-400 border border-success-200 dark:border-success-800">
                <span class="h-2 w-2 rounded-full bg-success-500 animate-pulse"></span>
                Webhook API Terhubung
            </span>
        </div>
    </div>

    {{-- 3 Status Card --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Data Tersinkronisasi</p>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalSyncCount }} Baris</h4>
            <p class="text-[11px] text-success-600 dark:text-success-400 mt-1">100% Berhasil dikirim ke Spreadsheet</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">Target Sampel Riset UNIB</p>
            <h4 class="text-2xl font-bold text-brand-600 dark:text-brand-400 mt-1">108 <span class="text-xs text-gray-400 font-normal">Responden</span></h4>
            <p class="text-[11px] text-gray-400 mt-1">Rumus Slovin ($e=10\%$, dropout $10\%$)</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">Format Ekspor Analisis</p>
            <h4 class="text-base font-bold text-gray-900 dark:text-white mt-2 flex flex-wrap gap-2">
                <span class="rounded bg-brand-50 px-2 py-0.5 text-xs text-brand-700">SPSS Ready</span>
                <a
                    href="{{ route('admin.evaluations.export') }}"
                    class="rounded bg-success-50 px-2 py-0.5 text-xs font-bold text-success-700 hover:bg-success-100 dark:bg-success-950 dark:text-success-400 dark:hover:bg-success-900/60 transition"
                    title="Unduh Data Post-Test Format Excel"
                >
                    Unduh Excel (.xlsx) &darr;
                </a>
            </h4>
        </div>
    </div>

    {{-- Spreadsheet Live View --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Lembar Data Google Sheets: Responden &amp; Nilai N-Gain</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tampilan data terstruktur demografi, skor Pre-Test, Post-Test, dan skor Gain.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3">NIM</th>
                        <th class="px-4 py-3">Inisial</th>
                        <th class="px-4 py-3 text-center">Usia</th>
                        <th class="px-4 py-3 text-center">JK</th>
                        <th class="px-4 py-3">Agama</th>
                        <th class="px-4 py-3">Program Studi</th>
                        <th class="px-4 py-3">Fakultas</th>
                        <th class="px-4 py-3 text-center">Pre-Test</th>
                        <th class="px-4 py-3 text-center">Post-Test</th>
                        <th class="px-4 py-3 text-center">N-Gain</th>
                        <th class="px-4 py-3">Status Riset</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                    @foreach($respondenData as $row)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3 font-mono font-medium">{{ $row['nim'] }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $row['nama_inisial'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $row['usia'] }}</td>
                        <td class="px-4 py-3 text-center font-bold">{{ $row['gender'] }}</td>
                        <td class="px-4 py-3">{{ $row['agama'] }}</td>
                        <td class="px-4 py-3 max-w-[150px] truncate">{{ $row['prodi'] }}</td>
                        <td class="px-4 py-3 max-w-[160px] truncate">{{ $row['fakultas'] }}</td>
                        <td class="px-4 py-3 text-center font-bold text-brand-600 dark:text-brand-400">{{ $row['skor_pretest'] }}</td>
                        <td class="px-4 py-3 text-center font-bold text-success-600 dark:text-success-400">{{ $row['skor_posttest'] }}</td>
                        <td class="px-4 py-3 text-center font-bold text-warning-600 dark:text-warning-400">{{ $row['n_gain'] }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $row['status'] === 'Lengkap' ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-400' }}">
                                {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Log Riwayat Sinkronisasi Google Sheets Webhook --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Log Sinkronisasi Real-Time (Google Sheets Webhook API)</h3>
        <div class="space-y-2">
            @foreach($syncLogs as $log)
            <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3 text-xs dark:bg-gray-800">
                <div class="flex items-center gap-2.5">
                    <span class="h-2 w-2 rounded-full bg-success-500"></span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $log->student_identifier }}</span>
                    <span class="text-gray-400">&bull;</span>
                    <span class="text-gray-500 dark:text-gray-400 font-mono">{{ $log->sheet_range }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="rounded bg-success-50 px-2 py-0.5 text-[10px] font-semibold text-success-700 dark:bg-success-500/15 dark:text-success-400">{{ $log->event_type }}</span>
                    <span class="text-[11px] text-gray-400">{{ $log->synced_at ? $log->synced_at->diffForHumans() : '-' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
