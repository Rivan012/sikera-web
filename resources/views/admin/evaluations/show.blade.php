@extends('layouts.admin')

@section('title', 'Detail Lembar Jawaban Evaluasi')
@section('page-title', 'Detail Lembar Jawaban Responden')

@section('content')
<div class="space-y-6">
    {{-- Navigasi Kembali & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('admin.evaluations.index') }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 shadow-theme-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                title="Kembali ke Daftar Evaluasi"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    Lembar Jawaban: {{ $user?->name ?? 'Responden' }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Rincian butir soal, pilihan jawaban responden, kunci jawaban, dan pembahasan medis.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($evaluation->type === 'post_test')
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3.5 py-2 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Ujian Post-Test
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 px-3.5 py-2 text-xs font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                Ujian Pre-Test
            </span>
            @endif
        </div>
    </div>

    {{-- Grid 2 Kolom: Profil Responden & Hasil Ujian --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Profil Mahasiswa Responden --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Profil Responden Riset</h3>
            <div class="flex items-center gap-3.5 pb-4 border-b border-gray-100 dark:border-gray-800">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $user?->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }} font-bold text-base">
                    {{ $user ? strtoupper(substr($user->initials ?? $user->name, 0, 2)) : '??' }}
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="font-bold text-gray-900 dark:text-white truncate">{{ $user?->name ?? 'Tidak Diketahui' }}</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Inisial: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $user?->initials ?? '-' }}</span> &bull; NIM: {{ $user?->nim ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="mt-4 space-y-2.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-400">Program Studi:</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200 text-right">{{ $user?->prodi ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Fakultas:</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200 text-right">{{ $user?->fakultas ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Jenis Kelamin:</span>
                    <span class="font-semibold {{ $user?->gender === 'L' ? 'text-blue-600' : 'text-rose-600' }}">
                        {{ $user?->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Usia Responden:</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $user?->usia ? $user->usia . ' Tahun' : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Agama:</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $user?->agama ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Pendidikan Terakhir:</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $user?->pendidikan_terakhir ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Kartu Hasil & Analisis Skor --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:col-span-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Ringkasan Skor &amp; Ketuntasan</h3>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-4 border-b border-gray-100 dark:border-gray-800">
                {{-- Nilai Total --}}
                <div class="rounded-xl bg-gray-50 p-3.5 dark:bg-gray-800/50">
                    <p class="text-[11px] text-gray-400">Skor Total</p>
                    <p class="text-2xl font-black mt-1 {{ $evaluation->total_score >= 70 ? 'text-emerald-600 dark:text-emerald-400' : ($evaluation->total_score >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                        {{ $evaluation->total_score }}
                    </p>
                    <p class="text-[10px] text-gray-400">Skala 0 - 100</p>
                </div>

                {{-- Ketuntasan Soal --}}
                <div class="rounded-xl bg-gray-50 p-3.5 dark:bg-gray-800/50">
                    <p class="text-[11px] text-gray-400">Jawaban Benar</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">
                        {{ $correctCount }} <span class="text-xs font-normal text-gray-400">/ {{ $totalQuestions }}</span>
                    </p>
                    <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">
                        {{ $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0 }}% Akurasi
                    </p>
                </div>

                {{-- Lingkup Ujian --}}
                <div class="rounded-xl bg-gray-50 p-3.5 dark:bg-gray-800/50">
                    <p class="text-[11px] text-gray-400">Lingkup Materi</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">
                        {{ $evaluation->module_number ? 'Modul ' . $evaluation->module_number : 'Ujian Umum' }}
                    </p>
                    <p class="text-[10px] text-gray-400">
                        {{ $evaluation->module_number ? 'Materi Terarah' : 'Komprehensif 4 Modul' }}
                    </p>
                </div>

                {{-- Tanggal Selesai --}}
                <div class="rounded-xl bg-gray-50 p-3.5 dark:bg-gray-800/50">
                    <p class="text-[11px] text-gray-400">Waktu Pengisian</p>
                    <p class="text-xs font-bold text-gray-900 dark:text-white mt-1">
                        {{ $evaluation->submitted_at ? $evaluation->submitted_at->format('d M Y') : '-' }}
                    </p>
                    <p class="text-[10px] text-gray-400">
                        {{ $evaluation->submitted_at ? $evaluation->submitted_at->format('H:i') . ' WIB' : '-' }}
                    </p>
                </div>
            </div>

            {{-- Komparasi N-Gain (Jika Post-Test) --}}
            @if($evaluation->type === 'post_test')
            <div class="mt-4 rounded-xl bg-teal-50/60 p-4 dark:bg-teal-950/30 border border-teal-200 dark:border-teal-800/60">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h4 class="text-xs font-bold text-teal-900 dark:text-teal-200 uppercase tracking-wider">
                            Kalkulasi N-Gain Efektivitas Riset
                        </h4>
                        <p class="text-xs text-teal-700 dark:text-teal-400 mt-0.5">
                            Berdasarkan rumus Normalized Gain: <code>g = (PostScore - PreScore) / (100 - PreScore)</code>
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <span class="text-[10px] text-teal-600 dark:text-teal-400 block uppercase font-bold">Skor Gain</span>
                            <span class="font-mono text-lg font-black text-teal-900 dark:text-teal-200">
                                {{ $evaluation->n_gain_score !== null ? number_format($evaluation->n_gain_score, 3) : '-' }}
                            </span>
                        </div>
                        <span class="rounded-lg px-2.5 py-1 text-xs font-bold {{ ($evaluation->n_gain_score ?? 0) >= 0.7 ? 'bg-emerald-600 text-white' : (($evaluation->n_gain_score ?? 0) >= 0.3 ? 'bg-amber-500 text-white' : 'bg-gray-500 text-white') }}">
                            Efektivitas {{ ($evaluation->n_gain_score ?? 0) >= 0.7 ? 'Tinggi' : (($evaluation->n_gain_score ?? 0) >= 0.3 ? 'Sedang' : 'Rendah') }}
                        </span>
                    </div>
                </div>

                @if($comparisonTest)
                <div class="mt-3 grid grid-cols-3 gap-2 pt-3 border-t border-teal-200/60 dark:border-teal-800/60 text-xs text-center">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-[10px]">Pre-Test</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ $comparisonTest->total_score }} / 100</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-[10px]">Post-Test</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $evaluation->total_score }} / 100</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-[10px]">Peningkatan</span>
                        <span class="font-bold text-teal-700 dark:text-teal-300">
                            +{{ $evaluation->total_score - $comparisonTest->total_score }} Poin
                        </span>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Daftar Analisis Butir Soal --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                Analisis Lembar Jawaban Butir Soal ({{ count($questionItems) }} Pertanyaan)
            </h3>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    Benar: {{ $correctCount }}
                </span>
                <span class="flex items-center gap-1.5 text-rose-600 dark:text-rose-400 font-semibold">
                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                    Salah: {{ $totalQuestions - $correctCount }}
                </span>
            </div>
        </div>

        @foreach($questionItems as $item)
        <div class="rounded-2xl border {{ $item['is_correct'] ? 'border-emerald-200 bg-white dark:border-emerald-950 dark:bg-emerald-950/10' : 'border-rose-200 bg-white dark:border-rose-950 dark:bg-rose-950/10' }} p-6 shadow-theme-xs">
            {{-- Header Butir Soal --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-300">
                        #{{ $item['number'] }}
                    </span>
                    <span class="text-xs font-medium text-gray-400">
                        Modul Target: Modul {{ $item['module_target'] }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    @if($item['is_correct'])
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        BENAR (Pilihan: {{ $item['user_answer'] }})
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 dark:bg-rose-950 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                        SALAH (Pilihan: {{ $item['user_answer'] ?? 'Kosong' }} &bull; Kunci: {{ $item['correct_answer'] }})
                    </span>
                    @endif
                </div>
            </div>

            {{-- Teks Pertanyaan --}}
            <div class="mt-4">
                <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white leading-relaxed">
                    {{ $item['question_text'] }}
                </p>
            </div>

            {{-- Pilihan Jawaban (A, B, C, D) --}}
            <div class="mt-4 space-y-2">
                @foreach($item['options'] as $optKey => $opt)
                @php
                    $optLabel = strtoupper(is_array($opt) ? ($opt['label'] ?? (is_string($optKey) && !is_numeric($optKey) ? $optKey : chr(65 + $loop->index))) : (is_string($optKey) && !is_numeric($optKey) ? $optKey : chr(65 + $loop->index)));
                    $optText = is_array($opt) ? ($opt['text'] ?? '') : (string) $opt;
                    $isUserChoice = ($item['user_answer'] === $optLabel);
                    $isCorrectChoice = ($item['correct_answer'] === $optLabel);
                @endphp
                <div class="flex items-center justify-between rounded-xl p-3 text-xs sm:text-sm border transition {{ $isUserChoice && $isCorrectChoice ? 'border-emerald-400 bg-emerald-50 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200 font-semibold' : ($isUserChoice && ! $isCorrectChoice ? 'border-rose-400 bg-rose-50 text-rose-900 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-200 font-semibold' : ($isCorrectChoice ? 'border-emerald-300 bg-emerald-50/40 text-emerald-800 dark:border-emerald-800/60 dark:bg-emerald-950/20 dark:text-emerald-300 font-semibold' : 'border-gray-200 bg-gray-50/50 text-gray-700 dark:border-gray-800 dark:bg-gray-800/40 dark:text-gray-300')) }}">
                    <div class="flex items-center gap-3">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold {{ $isUserChoice && $isCorrectChoice ? 'bg-emerald-600 text-white' : ($isUserChoice && ! $isCorrectChoice ? 'bg-rose-600 text-white' : ($isCorrectChoice ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300')) }}">
                            {{ $optLabel }}
                        </span>
                        <span>{{ $optText }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($isUserChoice && $isCorrectChoice)
                        <span class="rounded bg-emerald-600 px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">
                            Pilihan Responden &bull; Kunci Benar
                        </span>
                        @elseif($isUserChoice && ! $isCorrectChoice)
                        <span class="rounded bg-rose-600 px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">
                            Pilihan Responden (Salah)
                        </span>
                        @elseif($isCorrectChoice)
                        <span class="rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                            Kunci Jawaban yang Benar
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Komparasi Jawaban Pre vs Post (Jika Ada) --}}
            @if($evaluation->type === 'post_test' && $item['comparison_answer'] !== null)
            <div class="mt-4 rounded-xl bg-gray-50 p-3 text-xs dark:bg-gray-800/50 flex flex-wrap items-center justify-between gap-2 border border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-2">
                    <span class="text-gray-400">Jawaban Pre-Test:</span>
                    <span class="font-bold {{ $item['comparison_is_correct'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $item['comparison_answer'] }} ({{ $item['comparison_is_correct'] ? 'Benar' : 'Salah' }})
                    </span>
                    <span class="text-gray-400">&rarr;</span>
                    <span class="text-gray-400">Jawaban Post-Test:</span>
                    <span class="font-bold {{ $item['is_correct'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $item['user_answer'] }} ({{ $item['is_correct'] ? 'Benar' : 'Salah' }})
                    </span>
                </div>
                @if(! $item['comparison_is_correct'] && $item['is_correct'])
                <span class="rounded bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.5 text-[10px] font-bold">
                    + Peningkatan Kognitif Terbukti
                </span>
                @endif
            </div>
            @endif

            {{-- Pembahasan Medis / Penjelasan Soal --}}
            @if(! empty($item['explanation']))
            <div class="mt-4 rounded-xl bg-amber-50/60 p-3.5 text-xs text-amber-900 dark:bg-amber-950/20 dark:text-amber-300 border border-amber-200 dark:border-amber-900/40">
                <div class="flex items-start gap-2">
                    <svg class="h-4 w-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                    <div>
                        <span class="font-bold uppercase tracking-wider text-[10px] text-amber-700 dark:text-amber-400 block mb-0.5">
                            Pembahasan Ilmiah &amp; Analisis Medis:
                        </span>
                        <p class="leading-relaxed">{{ $item['explanation'] }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
