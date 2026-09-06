@extends('layouts.admin')
@section('title', $module->title)
@section('page-title', 'Modul ' . $module->module_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('modules.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-brand-500 dark:text-gray-400">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Kembali ke Katalog Modul
    </a>

    @php
        $bannerUrl = $module->banner_image ?: '/images/banners/banner-module-' . (($module->module_number % 4) ?: 4) . '.svg';
    @endphp

    {{-- Hero Banner Visual Modul --}}
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="relative w-full h-48 sm:h-56 bg-gray-900 overflow-hidden">
            <img src="{{ $bannerUrl }}" alt="{{ $module->title }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
            
            <div class="absolute bottom-4 left-6 right-6 text-white">
                <span class="rounded-lg bg-white/20 backdrop-blur-sm px-2.5 py-1 text-xs font-bold text-white mb-2 inline-block">
                    Modul {{ $module->module_number }} &bull; {{ $module->estimated_time }}
                </span>
                <h1 class="text-xl sm:text-2xl font-bold leading-tight">{{ $module->title }}</h1>
            </div>
        </div>

        <div class="p-6 sm:p-7">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">Deskripsi Modul Pembelajaran</h3>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $module->description }}</p>

            {{-- Jika Semua Materi Selesai Dibaca & Belum Post-Test --}}
            @if($allTopicsDone && !$posttest && auth()->user()->isMahasiswa())
            <div class="mt-5 rounded-xl border border-warning-200 bg-warning-50 p-4 dark:border-warning-900/50 dark:bg-warning-950/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-warning-800 dark:text-warning-300">🎉 Semua submateri telah selesai dipelajari!</p>
                    <p class="text-[11px] text-warning-700 dark:text-warning-400 mt-0.5">Kerjakan Post-Test sekarang untuk mengukur peningkatan pemahaman dan skor N-Gain Modul {{ $module->module_number }}.</p>
                </div>
                <a href="{{ route('modules.posttest.show', $module->id) }}" class="rounded-lg bg-warning-500 px-4 py-2 text-xs font-bold text-white hover:bg-warning-600 transition shadow-theme-xs whitespace-nowrap text-center animate-pulse">
                    Mulai Post-Test &rarr;
                </a>
            </div>
            @elseif($posttest)
            <div class="mt-5 rounded-xl border border-success-200 bg-success-50 p-4 dark:border-success-900/50 dark:bg-success-950/30 flex items-center justify-between text-xs">
                <div>
                    <p class="font-bold text-success-800 dark:text-success-300">✓ Modul Ini Telah Selesai (Post-Test Tuntas)</p>
                    <p class="text-[11px] text-success-700 dark:text-success-400">Skor Post-Test: {{ $posttest->total_score }}/100 &bull; Indeks Gain: {{ $posttest->n_gain_score }}</p>
                </div>
                <span class="rounded-full bg-success-600 text-white font-bold px-2.5 py-1 text-[11px]">Selesai</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Daftar Submateri --}}
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">Daftar Submateri Ilmiah ({{ $module->topics->count() }} Materi)</h3>
        <div class="space-y-3">
            @foreach($module->topics as $topic)
            @php $isDone = in_array($topic->id, $completedTopicIds); @endphp
            <a href="{{ route('modules.topic', [$module->id, $topic->id]) }}"
                class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 transition hover:border-brand-300 hover:shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-500/50">
                @if($isDone)
                <div class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                </div>
                @else
                <div class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 text-xs font-bold dark:bg-gray-800 dark:text-gray-400">{{ $topic->topic_code }}</div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-brand-600 dark:text-white">{{ $topic->title }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        Materi {{ $topic->topic_code }}
                        @if($topic->youtube_video_id)
                        &bull; <span class="text-error-500 font-semibold">Video YouTube</span>
                        @endif
                    </p>
                </div>
                <span class="text-xs font-semibold text-brand-600 dark:text-brand-400">
                    {{ $isDone ? 'Baca Ulang' : 'Mulai Baca' }} &rarr;
                </span>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
