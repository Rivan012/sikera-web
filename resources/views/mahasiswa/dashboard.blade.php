@extends('layouts.admin')

@section('title', 'Menu Utama Mahasiswa')
@section('page-title', 'Menu Utama SIKERA')

@section('content')
<div class="space-y-6">
    {{-- Banner Selamat Datang & Status Riset --}}
    <div class="rounded-2xl border border-brand-100 bg-gradient-to-r from-brand-50 to-white p-6 dark:border-brand-900/50 dark:from-brand-950/30 dark:to-gray-900 sm:p-7">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="rounded-full bg-brand-500/10 px-2.5 py-0.5 text-xs font-semibold text-brand-600 dark:text-brand-400">Responden Mahasiswa</span>
                    <span class="text-xs text-gray-400">&bull;</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $user->nim ?? $user->email }}</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Halo, {{ $user->name }}!</h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">Selamat datang di Menu Utama Edukasi &amp; Pemantauan Kesehatan Reproduksi Remaja.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="rounded-xl border border-gray-200 bg-white p-3 text-center dark:border-gray-800 dark:bg-gray-800 shadow-theme-xs">
                    <p class="text-xs text-gray-400">Pre-Test</p>
                    <p class="text-sm font-bold text-success-600 dark:text-success-400">{{ $pretest ? $pretest->total_score . '/100' : 'Belum' }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-3 text-center dark:border-gray-800 dark:bg-gray-800 shadow-theme-xs">
                    <p class="text-xs text-gray-400">Post-Test</p>
                    <p class="text-sm font-bold {{ $posttest ? 'text-success-600 dark:text-success-400' : 'text-warning-600 dark:text-warning-400' }}">
                        {{ $posttest ? $posttest->total_score . '/100' : 'Saat Keluar' }}
                    </p>
                </div>
                <div class="rounded-xl border border-brand-200 bg-brand-500/10 p-3 text-center dark:border-brand-800 shadow-theme-xs">
                    <p class="text-xs text-brand-600 dark:text-brand-400">Poin</p>
                    <p class="text-sm font-bold text-brand-700 dark:text-brand-300">{{ $user->points }} pts</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Kelompok 4 Fitur Utama Sesuai Diagram Alur --}}
    <div>
        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Fitur Edukasi &amp; Pemantauan</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- I1: Modul Edukasi & Video YouTube --}}
            <a href="{{ route('modules.index') }}" class="group rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-brand-500 hover:shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 mb-4 group-hover:scale-105 transition">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12zM10 9h8v2h-8V9zm0 3h4v2h-4v-2zm0-6h8v2h-8V6z"/></svg>
                </div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-brand-600 dark:text-brand-400">I-1 &bull; Modul Ilmiah</span>
                    <span class="text-xs text-gray-400">4 Modul</span>
                </div>
                <h4 class="text-base font-bold text-gray-900 group-hover:text-brand-600 dark:text-white">Modul Edukasi &amp; Video</h4>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Materi anatomi, batasan relasi, risiko medis, &amp; video YouTube terintegrasi.</p>
            </a>

            {{-- I2: Projek Visual Poster --}}
            <a href="{{ route('posters.index') }}" class="group rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-warning-500 hover:shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400 mb-4 group-hover:scale-105 transition">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                </div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-warning-600 dark:text-warning-400">I-2 &bull; Galeri Visual</span>
                    <span class="text-xs text-gray-400">HD Poster</span>
                </div>
                <h4 class="text-base font-bold text-gray-900 group-hover:text-warning-600 dark:text-white">Projek Visual Poster</h4>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Pamflet &amp; poster digital siap unduh dan bagikan ke WhatsApp / Instagram.</p>
            </a>

            {{-- I3: Kalender Haid (Perempuan) / Kalkulator IMT (Laki-laki) --}}
            <a href="{{ route('selfcare.index') }}" class="group rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-error-500 hover:shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400 mb-4 group-hover:scale-105 transition">
                    @if($user->gender === 'P')
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    @else
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 2h1.5v3H12V5zm-2 0h1.5v3H10V5zm-2 0h1.5v3H8V5zm-2 0h1.5v3H6V5zm12 14H6v-9h12v9z"/></svg>
                    @endif
                </div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-error-600 dark:text-error-400">I-3 &bull; Self-Care</span>
                    <span class="text-xs text-gray-400">{{ $user->gender === 'P' ? 'Haid & IMT' : 'Status Gizi' }}</span>
                </div>
                <h4 class="text-base font-bold text-gray-900 group-hover:text-error-600 dark:text-white">
                    {{ $user->gender === 'P' ? 'Kalender Haid & IMT' : 'Kalkulator IMT & Gizi' }}
                </h4>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    {{ $user->gender === 'P' ? 'Prediksi ovulasi, skala nyeri NRS haid, spektrum darah & kalkulator gizi.' : 'Hitung indeks massa tubuh (IMT), deteksi gizi, dan panduan higienitas reproduksi.' }}
                </p>
            </a>

            {{-- I4: Diskusi Anonim & Kasus Kampus --}}
            <a href="{{ route('cases.index') }}" class="group rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-success-500 hover:shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400 mb-4 group-hover:scale-105 transition">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/><path d="M12 10H7v2h5v-2zm5-4H7v2h10V6z"/></svg>
                </div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-success-600 dark:text-success-400">I-4 &bull; Sosial &amp; Hukum</span>
                    <span class="text-xs text-gray-400">Kasus Nyata</span>
                </div>
                <h4 class="text-base font-bold text-gray-900 group-hover:text-success-600 dark:text-white">Diskusi &amp; Studi Kasus</h4>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Bedah kasus nyata indekos/relasi &amp; forum konsultasi anonim bersama konselor.</p>
            </a>
        </div>
    </div>

    {{-- Bagian Konten Ringkasan --}}
    <div class="grid grid-cols-12 gap-6">
        {{-- Progress Modul Pembelajaran --}}
        <div class="col-span-12 lg:col-span-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Daftar Modul Pembelajaran SIKERA</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Selesaikan seluruh materi untuk persiapan evaluasi Post-Test.</p>
                    </div>
                    <a href="{{ route('modules.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Buka Semua &rarr;</a>
                </div>

                <div class="space-y-3">
                    @foreach($modules as $module)
                    <a href="{{ route('modules.show', $module->id) }}" class="flex items-center justify-between rounded-xl border border-gray-100 p-4 transition hover:border-brand-300 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600 font-bold text-xs dark:bg-brand-500/10 dark:text-brand-400">
                                M{{ $module->module_number }}
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $module->title }}</h5>
                                <p class="text-xs text-gray-400">{{ $module->topics_count }} Submateri &bull; {{ $module->estimated_time }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-medium text-brand-600 dark:text-brand-400">Pelajari &rarr;</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Panel Kanan: Poster Populer & Hotline Cepat --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">
            {{-- Projek Visual Poster Terpopuler --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Poster Kaspro Pilihan</h3>
                    <a href="{{ route('posters.index') }}" class="text-xs font-semibold text-warning-600 hover:text-warning-700 dark:text-warning-400">Lihat &rarr;</a>
                </div>
                <div class="space-y-3">
                    @foreach($posters as $poster)
                    <div class="rounded-xl border border-gray-100 p-3 dark:border-gray-800">
                        <span class="rounded bg-warning-50 px-2 py-0.5 text-[10px] font-semibold text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">{{ $poster->category }}</span>
                        <p class="text-xs font-semibold text-gray-800 dark:text-white mt-1.5">{{ $poster->title }}</p>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100 dark:border-gray-800 text-[11px] text-gray-400">
                            <span>{{ $poster->download_count }} diunduh</span>
                            <a href="{{ route('posters.index') }}" class="text-brand-600 font-medium dark:text-brand-400">Unduh HD</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pintasan Keluar & Post-Test Gating --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Selesai Belajar?</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Ketika Anda memilih keluar, sistem akan meminta pengisian kuesioner Post-Test untuk mengukur peningkatan pemahaman (N-Gain).</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-gray-900 py-2.5 text-xs font-semibold text-white hover:bg-black transition dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" fill="currentColor"/>
                        </svg>
                        Pilih Keluar / Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
