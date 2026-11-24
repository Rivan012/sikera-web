@extends('layouts.admin')

@section('title', 'Menu Utama Mahasiswa')
@section('page-title', 'Menu Utama Mahasiswa')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Fokus Utama: Kartu Identitas Responden & Status Instrumen --}}
    <section aria-labelledby="mhs-welcome-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex h-1.5 w-full" aria-hidden="true">
            <span class="w-20 bg-rose-600 dark:bg-rose-500"></span>
            <span class="grow bg-teal-700 dark:bg-teal-500"></span>
        </div>

        <div class="p-6 sm:p-7">
            <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">
                        Responden Mahasiswa &middot; {{ $user->nim ?? $user->email }}
                    </p>
                    <h2 id="mhs-welcome-heading" class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                        Halo, {{ $user->name }}
                    </h2>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                        {{ $user->prodi ?? 'Program Studi Mahasiswa' }}@if($user->fakultas) &middot; {{ $user->fakultas }}@endif. Pelajari setiap modul secara berurutan dan gunakan alat pemantauan mandiri sesuai kebutuhan Anda.
                    </p>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <a
                            href="{{ route('modules.index') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                        >
                            Buka Kurikulum Modul
                        </a>
                        <a
                            href="{{ route('selfcare.index') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
                        >
                            {{ $user->gender === 'P' ? 'Catat Siklus Haid & IMT' : 'Hitung IMT & Status Gizi' }}
                        </a>
                    </div>
                </div>

                {{-- Ringkasan Metrik Evaluasi & Aktivitas Responden --}}
                <dl class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:min-w-[390px]">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/70">
                        <dt class="text-xs font-semibold text-slate-600 dark:text-slate-300">Evaluasi Pre-Test</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900 dark:text-white">
                            {{ $pretest ? $pretest->total_score . '/100' : 'Per Modul' }}
                        </dd>
                        <dd class="mt-0.5 text-[11px] font-medium text-teal-700 dark:text-teal-300">
                            {{ $pretest ? 'Tercatat' : 'Dikerjakan di awal modul' }}
                        </dd>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/70">
                        <dt class="text-xs font-semibold text-slate-600 dark:text-slate-300">Evaluasi Post-Test</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900 dark:text-white">
                            {{ $posttest ? $posttest->total_score . '/100' : 'Belum' }}
                        </dd>
                        <dd class="mt-0.5 text-[11px] font-medium text-slate-600 dark:text-slate-300">
                            {{ $posttest ? 'Selesai dikerjakan' : 'Setelah materi tuntas' }}
                        </dd>
                    </div>

                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-800/70 dark:bg-amber-950/60">
                        <dt class="text-xs font-semibold text-amber-900 dark:text-amber-200">Poin &amp; Konsistensi</dt>
                        <dd class="mt-1 text-lg font-bold text-amber-950 dark:text-amber-100">
                            {{ $user->points ?? 0 }} poin
                        </dd>
                        <dd class="mt-0.5 text-[11px] font-medium text-amber-900 dark:text-amber-200">
                            {{ $user->streak_days ?? 0 }} hari beruntun
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- Grid Utama Asimetris (RHYTHM 2) --}}
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
        {{-- Kolom Kiri (8 kolom): Kurikulum Pembelajaran & Ringkasan Self-Care --}}
        <div class="flex flex-col gap-6 lg:col-span-8">
            {{-- 1. Daftar Modul Pembelajaran --}}
            <section aria-labelledby="modul-heading" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-4 dark:border-slate-700">
                    <div>
                        <h3 id="modul-heading" class="text-lg font-semibold text-slate-900 dark:text-white">
                            Daftar Modul Pembelajaran SIKERA
                        </h3>
                        <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                            Setiap modul dilengkapi Pre-Test pembuka, materi ilmiah, video, dan Post-Test evaluasi.
                        </p>
                    </div>
                    <a
                        href="{{ route('modules.index') }}"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 text-xs font-semibold text-teal-700 underline decoration-teal-700/40 underline-offset-4 hover:text-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-teal-300 dark:decoration-teal-300/40 dark:hover:text-teal-200 dark:focus-visible:outline-teal-400"
                    >
                        Lihat Semua Modul
                    </a>
                </div>

                <div class="mt-4 flex flex-col gap-3">
                    @forelse($modules as $module)
                    <a
                        href="{{ route('modules.show', $module->id) }}"
                        class="flex min-h-11 flex-col justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50/70 p-4 transition-colors duration-150 hover:border-teal-700 hover:bg-teal-50/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-900/50 dark:hover:border-teal-400 dark:hover:bg-slate-900 sm:flex-row sm:items-center"
                    >
                        <div class="flex items-start gap-3.5">
                            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-teal-700 text-xs font-bold text-white dark:bg-teal-500 dark:text-slate-950">
                                0{{ $module->module_number }}
                            </span>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $module->title }}</h4>
                                <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                                    {{ $module->topics_count }} Submateri &middot; Estimasi {{ $module->estimated_time }}
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex shrink-0 items-center self-start rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 sm:self-center">
                            Pelajari Modul
                        </span>
                    </a>
                    @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                        Belum ada modul pembelajaran yang dipublikasikan saat ini.
                    </div>
                    @endforelse
                </div>
            </section>

            {{-- 2. Ringkasan Pemantauan Mandiri (Self-Care) --}}
            <section aria-labelledby="selfcare-heading" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-4 dark:border-slate-700">
                    <div>
                        <h3 id="selfcare-heading" class="text-lg font-semibold text-slate-900 dark:text-white">
                            Pemantauan Kesehatan Mandiri
                        </h3>
                        <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                            Catatan berkala membantu mengenali pola fisiologis tubuh sejak dini.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a
                            href="{{ route('selfcare.hygiene_guide') }}"
                            class="inline-flex min-h-11 items-center rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-700"
                        >
                            Panduan Higienitas
                        </a>
                        @if($user->gender === 'P')
                        <a
                            href="{{ route('selfcare.blood_guide') }}"
                            class="inline-flex min-h-11 items-center rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-700"
                        >
                            Spektrum Darah Haid
                        </a>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 {{ $user->gender === 'P' ? 'sm:grid-cols-2' : '' }}">
                    @if($user->gender === 'P')
                    <div class="flex flex-col justify-between rounded-xl border border-rose-200 bg-rose-50/50 p-4 dark:border-rose-900/60 dark:bg-rose-950/30">
                        <div>
                            <p class="text-xs font-semibold text-rose-800 dark:text-rose-200">Pelacak Siklus Haid Terakhir</p>
                            @if($latestPeriod)
                            <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                                {{ $latestPeriod->start_date->format('d M Y') }}
                            </p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-700 dark:text-slate-200">
                                Siklus {{ $latestPeriod->cycle_length }} hari &middot; Durasi {{ $latestPeriod->period_duration }} hari &middot; Skala nyeri NRS {{ $latestPeriod->nrs_pain_score }}/10
                            </p>
                            @else
                            <p class="mt-2 text-xs leading-relaxed text-slate-700 dark:text-slate-200">
                                Belum ada catatan siklus haid. Catat hari pertama haid untuk menghitung perkiraan ovulasi dan masa subur.
                            </p>
                            @endif
                        </div>
                        <div class="mt-4">
                            <a
                                href="{{ route('selfcare.index') }}"
                                class="inline-flex min-h-11 items-center rounded-lg bg-rose-700 px-3.5 py-2 text-xs font-semibold text-white transition-colors hover:bg-rose-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 dark:bg-rose-500 dark:text-slate-950 dark:hover:bg-rose-400"
                            >
                                {{ $latestPeriod ? 'Perbarui Catatan Haid' : 'Mulai Catat Siklus Haid' }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="flex flex-col justify-between rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/60">
                        <div>
                            <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">Status Indeks Massa Tubuh (IMT)</p>
                            @if($latestBmi)
                            <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                                {{ number_format($latestBmi->bmi_value, 1) }} kg/m&sup2; &middot; {{ $latestBmi->category }}
                            </p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                                Tinggi {{ $latestBmi->height_cm }} cm, berat {{ $latestBmi->weight_kg }} kg.
                            </p>
                            @else
                            <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                                Belum ada pengukuran IMT tersimpan. Periksa status gizi Anda untuk memantau kecukupan nutrisi reproduksi.
                            </p>
                            @endif
                        </div>
                        <div class="mt-4">
                            <a
                                href="{{ route('selfcare.index') }}"
                                class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                            >
                                {{ $latestBmi ? 'Hitung Ulang IMT' : 'Hitung IMT Sekarang' }}
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Kolom Kanan (4 kolom): Konsultasi Anonim, Poster Kaspro, & Keluar Sesi --}}
        <div class="flex flex-col gap-6 lg:col-span-4">
            {{-- 1. Forum Konsultasi Anonim & Studi Kasus --}}
            <section aria-labelledby="konseling-heading" class="rounded-xl bg-teal-900 p-6 text-white shadow-sm dark:border dark:border-slate-700 dark:bg-slate-800">
                <p class="text-xs font-semibold text-amber-300">Ruang Aman &amp; Perlindungan Kampus</p>
                <h3 id="konseling-heading" class="mt-1 text-lg font-semibold text-white">
                    Diskusi Anonim &amp; Studi Kasus
                </h3>
                <p class="mt-1.5 text-xs leading-relaxed text-teal-50 dark:text-slate-300">
                    Konsultasikan pertanyaan sensitif tanpa menampilkan nama asli bersama Dosen PA dan konselor kampus.
                </p>

                <div class="mt-4 flex flex-col gap-2.5">
                    @forelse($recentThreads as $thread)
                    <a
                        href="{{ route('forum.show', $thread->id) }}"
                        class="block rounded-lg border border-teal-700/80 bg-teal-950/60 p-3 transition-colors hover:border-amber-300/70 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300 dark:border-slate-700 dark:bg-slate-900"
                    >
                        <div class="flex items-center justify-between gap-2 text-[11px]">
                            <span class="font-semibold text-amber-300">{{ $thread->topic }}</span>
                            <span class="text-teal-100 dark:text-slate-300">{{ $thread->replies_count }} balasan</span>
                        </div>
                        <p class="mt-1 line-clamp-1 text-xs font-semibold text-white">{{ $thread->title }}</p>
                    </a>
                    @empty
                    <p class="rounded-lg border border-teal-800 bg-teal-950/40 p-3 text-xs text-teal-50 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
                        Belum ada topik diskusi baru. Anda dapat memulai pertanyaan anonim kapan saja.
                    </p>
                    @endforelse
                </div>

                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-1">
                    <a
                        href="{{ route('forum.create') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-slate-900 transition-colors hover:bg-teal-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300"
                    >
                        Buat Pertanyaan Anonim
                    </a>
                    <a
                        href="{{ route('cases.index') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-teal-600 px-3.5 py-2 text-xs font-semibold text-white transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300 dark:border-slate-600 dark:hover:bg-slate-700"
                    >
                        Baca Studi Kasus Kampus
                    </a>
                </div>
            </section>

            {{-- 2. Galeri Poster Kaspro --}}
            <section aria-labelledby="poster-heading" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center justify-between gap-2">
                    <h3 id="poster-heading" class="text-base font-semibold text-slate-900 dark:text-white">Poster Edukasi Kaspro</h3>
                    <a
                        href="{{ route('posters.index') }}"
                        class="inline-flex min-h-11 items-center rounded-lg px-2 text-xs font-semibold text-teal-700 underline decoration-teal-700/40 underline-offset-4 hover:text-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-teal-300 dark:hover:text-teal-200"
                    >
                        Buka Galeri
                    </a>
                </div>

                <div class="mt-3 flex flex-col gap-3">
                    @forelse($posters as $poster)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-700 dark:bg-slate-900/60">
                        <span class="inline-block rounded-md bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-900 dark:bg-amber-950 dark:text-amber-200">
                            {{ $poster->category }}
                        </span>
                        <p class="mt-1.5 text-xs font-semibold text-slate-900 dark:text-white">{{ $poster->title }}</p>
                        <div class="mt-2 flex items-center justify-between border-t border-slate-200 pt-2 text-[11px] text-slate-600 dark:border-slate-700 dark:text-slate-300">
                            <span>{{ $poster->download_count }} unduhan</span>
                            <a
                                href="{{ route('posters.index') }}"
                                class="font-semibold text-teal-700 hover:text-teal-800 focus-visible:outline-2 focus-visible:outline-teal-700 dark:text-teal-300"
                            >
                                Lihat Poster
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="rounded-lg border border-dashed border-slate-300 p-4 text-center text-xs text-slate-600 dark:border-slate-700 dark:text-slate-300">
                        Belum ada poster Kaspro yang diunggah.
                    </p>
                    @endforelse
                </div>
            </section>

            {{-- 3. Akhiri Sesi & Evaluasi Post-Test --}}
            <section aria-labelledby="logout-heading" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800" x-data="{ loggingOut: false }">
                <h3 id="logout-heading" class="text-sm font-semibold text-slate-900 dark:text-white">Selesai Belajar Hari Ini?</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                    Pastikan evaluasi Post-Test modul yang telah dipelajari sudah terkirim sebelum keluar dari akun.
                </p>
                <form method="POST" action="{{ route('logout') }}" @submit="loggingOut = true" class="mt-4">
                    @csrf
                    <button
                        type="submit"
                        :disabled="loggingOut"
                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-slate-100 px-4 py-2.5 text-xs font-semibold text-slate-900 transition-colors duration-150 hover:bg-rose-50 hover:border-rose-300 hover:text-rose-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 disabled:opacity-70 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-rose-700 dark:hover:bg-rose-950/60 dark:hover:text-rose-200"
                    >
                        <span x-show="!loggingOut">Keluar dari Akun SIKERA</span>
                        <span x-cloak x-show="loggingOut">Mengakhiri sesi...</span>
                    </button>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
