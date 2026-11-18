@extends('layouts.admin')

@section('title', 'Dashboard Dosen Pembimbing Akademik')
@section('page-title', 'Dashboard Dosen PA & Konselor')

@section('content')
@php
    $nGainLevel = $avgNGain >= 0.7 ? 'Tinggi' : ($avgNGain >= 0.3 ? 'Sedang' : 'Rendah');
    $scoreDelta = round($avgPost - $avgPre, 1);
@endphp

<div class="flex flex-col gap-6">
    {{-- Header Identitas Dosen Pembimbing Akademik --}}
    <section aria-labelledby="dosen-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex h-1.5 w-full" aria-hidden="true">
            <span class="w-20 bg-rose-600 dark:bg-rose-500"></span>
            <span class="grow bg-teal-700 dark:bg-teal-500"></span>
        </div>

        <div class="flex flex-col justify-between gap-4 p-6 sm:flex-row sm:items-center sm:p-7">
            <div>
                <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">
                    Dosen Pembimbing Akademik &amp; Konselor &middot; {{ $dosen->fakultas ?? 'Universitas Bengkulu' }}
                </p>
                <h2 id="dosen-heading" class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    {{ $dosen->name }}
                </h2>
                <p class="mt-1 text-xs font-medium text-slate-600 dark:text-slate-300">
                    NIP / ID: {{ $dosen->nim ?? '-' }} &middot; Program Studi: {{ $dosen->prodi ?? '-' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('forum.index') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                >
                    Buka Ruang Konseling ({{ $unansweredThreads->count() }} menunggu)
                </a>
                <a
                    href="{{ route('modules.index') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-800 transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700"
                >
                    Rujukan Materi
                </a>
            </div>
        </div>
    </section>

    {{-- Ringkasan Metrik Evaluasi Kelas (Hierarkis: 1 Fokus N-Gain + 3 Metrik Pendukung) --}}
    <section aria-label="Ringkasan Evaluasi Mahasiswa" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-teal-900 p-5 text-white shadow-sm dark:border dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold text-amber-300">Indeks Efektivitas (N-Gain)</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-white">{{ number_format((float) $avgNGain, 3) }}</p>
            <p class="mt-2 text-xs font-medium text-teal-50 dark:text-slate-300">
                Kategori efektivitas: <strong class="font-semibold text-white">{{ $nGainLevel }}</strong>
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Total Mahasiswa Responden</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $totalMahasiswa }}</p>
            <p class="mt-2 text-xs font-medium text-teal-700 dark:text-teal-300">
                {{ $pretestDone }} selesai Pre-Test &middot; {{ $posttestDone }} selesai Post-Test
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Rata-rata Skor Pre-Test</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">
                {{ round($avgPre, 1) }}<span class="text-sm font-normal text-slate-600 dark:text-slate-300">/100</span>
            </p>
            <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">Pemetaan pemahaman awal mahasiswa</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Rata-rata Skor Post-Test</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">
                {{ round($avgPost, 1) }}<span class="text-sm font-normal text-slate-600 dark:text-slate-300">/100</span>
            </p>
            <p class="mt-2 text-xs font-semibold text-teal-700 dark:text-teal-300">
                {{ $scoreDelta >= 0 ? '+' . $scoreDelta : $scoreDelta }} poin terhadap Pre-Test
            </p>
        </div>
    </section>

    {{-- Grid Utama Dua Kolom Asimetris --}}
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
        {{-- Kolom Kiri (7 kolom): Tabel Partisipasi Fakultas & Katalog Modul Bimbingan --}}
        <div class="flex flex-col gap-6 lg:col-span-7">
            <section aria-labelledby="partisipasi-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                    <h3 id="partisipasi-heading" class="text-base font-semibold text-slate-900 dark:text-white">
                        Rekapitulasi Partisipasi Kelas &amp; Fakultas
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                        Agregasi ketuntasan instrumen evaluasi tanpa membuka identitas sensitif individu.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[520px] text-left text-xs">
                        <thead class="bg-slate-50 font-semibold text-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <tr>
                                <th scope="col" class="px-5 py-3.5">Fakultas</th>
                                <th scope="col" class="px-4 py-3.5 text-right">Mahasiswa</th>
                                <th scope="col" class="px-4 py-3.5 text-right">Pre-Test</th>
                                <th scope="col" class="px-4 py-3.5 text-right">Post-Test</th>
                                <th scope="col" class="px-5 py-3.5 text-right">Partisipasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-800 dark:divide-slate-700 dark:text-slate-200">
                            @forelse($fakultasBreakdown as $fb)
                            @php
                                $pct = $fb->total_mhs > 0 ? round(($fb->pre_done / $fb->total_mhs) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-900/40">
                                <td class="px-5 py-3.5 font-medium text-slate-900 dark:text-white">{{ $fb->fakultas ?: 'Belum Ditentukan' }}</td>
                                <td class="px-4 py-3.5 text-right">{{ $fb->total_mhs }}</td>
                                <td class="px-4 py-3.5 text-right font-semibold text-teal-700 dark:text-teal-300">{{ $fb->pre_done }}</td>
                                <td class="px-4 py-3.5 text-right font-semibold text-slate-900 dark:text-white">{{ $fb->post_done }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="inline-block rounded-md bg-teal-50 px-2.5 py-1 text-[11px] font-semibold text-teal-800 dark:bg-teal-950 dark:text-teal-200">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-600 dark:text-slate-300">
                                    Belum ada data responden mahasiswa yang terdaftar.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section aria-labelledby="katalog-bimbingan-heading" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-200 pb-4 dark:border-slate-700">
                    <h3 id="katalog-bimbingan-heading" class="text-base font-semibold text-slate-900 dark:text-white">
                        Katalog Modul Rujukan Bimbingan
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                        Materi ilmiah standar untuk bahan pendampingan akademik bersama mahasiswa.
                    </p>
                </div>

                <div class="mt-4 flex flex-col gap-3">
                    @forelse($modules as $m)
                    <div class="flex flex-col justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-900/50 sm:flex-row sm:items-center">
                        <div>
                            <span class="text-xs font-bold text-teal-700 dark:text-teal-300">Modul 0{{ $m->module_number }}</span>
                            <h4 class="mt-0.5 text-sm font-semibold text-slate-900 dark:text-white">{{ $m->title }}</h4>
                            <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">{{ $m->subtitle }}</p>
                        </div>
                        <a
                            href="{{ route('modules.show', $m->id) }}"
                            class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                        >
                            Buka Materi
                        </a>
                    </div>
                    @empty
                    <p class="rounded-lg border border-dashed border-slate-300 p-6 text-center text-xs text-slate-600 dark:border-slate-700 dark:text-slate-300">
                        Belum ada modul rujukan yang tersedia.
                    </p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Kolom Kanan (5 kolom): Antrean Konsultasi Anonim Mahasiswa (Fokus Tindakan Konselor) --}}
        <section aria-labelledby="antrean-konseling-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 lg:col-span-5">
            <div class="flex h-1.5 w-full" aria-hidden="true">
                <span class="w-16 bg-amber-500"></span>
                <span class="grow bg-teal-700 dark:bg-teal-500"></span>
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between gap-2 border-b border-slate-200 pb-4 dark:border-slate-700">
                    <div>
                        <h3 id="antrean-konseling-heading" class="text-base font-semibold text-slate-900 dark:text-white">
                            Konsultasi Anonim Mahasiswa
                        </h3>
                        <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                            Pertanyaan yang membutuhkan tanggapan konselor atau Dosen PA.
                        </p>
                    </div>
                    <a
                        href="{{ route('forum.index') }}"
                        class="inline-flex min-h-11 items-center rounded-lg px-2 text-xs font-semibold text-teal-700 underline decoration-teal-700/40 underline-offset-4 hover:text-teal-800 focus-visible:outline-2 focus-visible:outline-teal-700 dark:text-teal-300"
                    >
                        Semua Diskusi
                    </a>
                </div>

                <div class="mt-4 flex flex-col gap-3.5">
                    @forelse($unansweredThreads as $th)
                    <article class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-800/60 dark:bg-amber-950/30">
                        <div class="flex items-center justify-between gap-2 text-[11px]">
                            <span class="rounded-md bg-amber-100 px-2 py-0.5 font-semibold text-amber-950 dark:bg-amber-900/70 dark:text-amber-100">
                                {{ $th->topic }}
                            </span>
                            <span class="font-medium text-slate-600 dark:text-slate-300">
                                {{ $th->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <h4 class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $th->title }}</h4>
                        <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-700 dark:text-slate-200">{{ $th->content }}</p>

                        <div class="mt-3 flex items-center justify-between border-t border-amber-200/80 pt-3 dark:border-amber-800/50">
                            <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">
                                Penanya: {{ $th->anonymous_alias ?: 'Responden Anonim' }}
                            </span>
                            <a
                                href="{{ route('forum.show', $th->id) }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-3.5 py-2 text-xs font-semibold text-white transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                            >
                                Berikan Tanggapan
                            </a>
                        </div>
                    </article>
                    @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center dark:border-slate-700 dark:bg-slate-900/50">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Antrean konsultasi bersih</p>
                        <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                            Seluruh pertanyaan konseling mahasiswa saat ini telah mendapatkan tanggapan.
                        </p>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
