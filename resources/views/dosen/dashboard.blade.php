@extends('layouts.admin')

@section('title', 'Dashboard Dosen Pembimbing Akademik')
@section('page-title', 'Dashboard Dosen PA & Konselor')

@section('content')
<div class="space-y-6">
    {{-- Header Selamat Datang Dosen --}}
    <div class="rounded-2xl border border-warning-100 bg-gradient-to-r from-warning-50 to-white p-6 dark:border-warning-900/50 dark:from-warning-950/30 dark:to-gray-900 sm:p-7">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <span class="rounded-full bg-warning-500/15 px-2.5 py-0.5 text-xs font-semibold text-warning-700 dark:text-warning-300">Dosen Pembimbing Akademik &bull; FKIP UNIB</span>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-1.5">{{ $dosen->name }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">NIP: {{ $dosen->nim }} &bull; Program Studi: {{ $dosen->prodi }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-theme-xs dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                    <span class="h-2 w-2 rounded-full bg-success-500 animate-pulse"></span>
                    Konselor Aktif
                </span>
            </div>
        </div>
    </div>

    {{-- 1. Pantau Rata-rata Skor Mahasiswa --}}
    <div>
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">1. Pemantauan Evaluasi &amp; Skor Mahasiswa</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Mahasiswa Terdaftar</p>
                <h4 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalMahasiswa }}</h4>
                <p class="text-[11px] text-success-600 dark:text-success-400 mt-2">{{ $pretestDone }} sudah isi Pre-Test</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400">Rata-rata Skor Pre-Test</p>
                <h4 class="text-2xl font-bold text-brand-600 dark:text-brand-400 mt-1">{{ round($avgPre, 1) }}<span class="text-sm text-gray-400 font-normal">/100</span></h4>
                <p class="text-[11px] text-gray-400 mt-2">Tingkat pemahaman awal</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400">Rata-rata Skor Post-Test</p>
                <h4 class="text-2xl font-bold text-success-600 dark:text-success-400 mt-1">{{ round($avgPost, 1) }}<span class="text-sm text-gray-400 font-normal">/100</span></h4>
                <p class="text-[11px] text-success-600 dark:text-success-400 mt-2">+{{ round($avgPost - $avgPre, 1) }} kenaikan rata-rata</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400">Indeks Efektivitas (N-Gain)</p>
                <h4 class="text-2xl font-bold text-warning-600 dark:text-warning-400 mt-1">{{ round($avgNGain, 3) }}</h4>
                <p class="text-[11px] font-semibold {{ $avgNGain >= 0.7 ? 'text-success-600' : 'text-warning-600' }} mt-2">
                    Kategori {{ $avgNGain >= 0.7 ? 'Tinggi' : ($avgNGain >= 0.3 ? 'Sedang' : 'Rendah') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Distribusi Partisipasi per Fakultas --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Rekapitulasi Partisipasi Kelas &amp; Fakultas</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3">Fakultas</th>
                        <th class="px-6 py-3 text-center">Total Maba</th>
                        <th class="px-6 py-3 text-center">Pre-Test Selesai</th>
                        <th class="px-6 py-3 text-center">Post-Test Selesai</th>
                        <th class="px-6 py-3 text-center">Tingkat Partisipasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                    @foreach($fakultasBreakdown as $fb)
                    <tr>
                        <td class="px-6 py-3 font-medium">{{ $fb->fakultas }}</td>
                        <td class="px-6 py-3 text-center">{{ $fb->total_mhs }}</td>
                        <td class="px-6 py-3 text-center text-brand-600 font-semibold">{{ $fb->pre_done }}</td>
                        <td class="px-6 py-3 text-center text-success-600 font-semibold">{{ $fb->post_done }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="rounded-full bg-success-50 px-2 py-0.5 text-[11px] font-semibold text-success-700 dark:bg-success-500/15 dark:text-success-400">
                                {{ $fb->total_mhs > 0 ? round(($fb->pre_done / $fb->total_mhs) * 100) : 0 }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. Bahan Bimbingan & Konseling Kampus --}}
    <div>
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">2. Bahan Bimbingan &amp; Konseling Kampus</h3>
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Modul Materi Bimbingan --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Katalog Modul untuk Bimbingan Mahasiswa</h4>
                <div class="space-y-3">
                    @foreach($modules as $m)
                    <div class="rounded-xl border border-gray-100 p-3.5 dark:border-gray-800">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-brand-600 dark:text-brand-400">Modul {{ $m->module_number }}</span>
                            <a href="{{ route('modules.show', $m->id) }}" class="text-xs text-brand-600 font-semibold hover:underline">Buka Materi &rarr;</a>
                        </div>
                        <p class="text-xs font-semibold text-gray-800 dark:text-white mt-1">{{ $m->title }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $m->subtitle }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Forum Konsultasi Menunggu Tanggapan Dosen/Konselor --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Konsultasi Anonim Mahasiswa</h4>
                    <a href="{{ route('forum.index') }}" class="text-xs text-brand-600 font-semibold hover:underline">Buka Forum &rarr;</a>
                </div>
                <div class="space-y-3">
                    @forelse($unansweredThreads as $th)
                    <div class="rounded-xl border border-warning-100 bg-warning-50/30 p-3.5 dark:border-warning-900/50 dark:bg-warning-950/10">
                        <div class="flex items-center justify-between">
                            <span class="rounded bg-warning-100 px-1.5 py-0.5 text-[10px] font-semibold text-warning-800 dark:bg-warning-500/20 dark:text-warning-300">{{ $th->topic }}</span>
                            <span class="text-[10px] text-gray-400">{{ $th->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs font-semibold text-gray-800 dark:text-white mt-1.5">{{ $th->title }}</p>
                        <p class="text-[11px] text-gray-500 line-clamp-2 mt-0.5">{{ $th->content }}</p>
                        <div class="mt-2.5 pt-2 border-t border-warning-100 dark:border-warning-900/30 flex justify-end">
                            <a href="{{ route('forum.show', $th->id) }}" class="inline-flex items-center gap-1 rounded-lg bg-brand-500 px-3 py-1 text-xs font-semibold text-white hover:bg-brand-600">
                                Berikan Respon Konselor &rarr;
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 text-center py-6">Semua pertanyaan mahasiswa telah ditanggapi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
