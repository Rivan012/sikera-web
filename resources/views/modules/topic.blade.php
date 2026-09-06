@extends('layouts.admin')
@section('title', $topic->title)
@section('page-title', 'Materi ' . $topic->topic_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('modules.show', $module->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-brand-500 dark:text-gray-400">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Kembali ke Modul {{ $module->module_number }}
    </a>

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
            <div class="flex items-center gap-2 mb-2">
                <span class="rounded bg-brand-50 px-2 py-0.5 text-xs font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $topic->topic_code }}</span>
                <span class="rounded bg-success-50 px-2 py-0.5 text-xs font-semibold text-success-600 dark:bg-success-500/10 dark:text-success-400">✓ Selesai dibaca (+10 pts)</span>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $topic->title }}</h2>
        </div>

        @if($topic->youtube_video_id)
        <div class="px-6 pt-5">
            <div class="relative w-full overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-black" style="padding-bottom: 56.25%;">
                <iframe
                    class="absolute inset-0 h-full w-full"
                    src="https://www.youtube.com/embed/{{ $topic->youtube_video_id }}"
                    title="{{ $topic->title }}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
        @endif

        <div class="p-6 prose prose-sm max-w-none text-gray-700 dark:text-gray-300 dark:prose-invert leading-relaxed">
            {!! $topic->content_html !!}
        </div>

        {{-- Navigasi Bawah --}}
        <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
            @if($prevTopic)
            <a href="{{ route('modules.topic', [$module->id, $prevTopic->id]) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                Materi {{ $prevTopic->topic_code }}
            </a>
            @else
            <div></div>
            @endif

            @if($nextTopic)
            <a href="{{ route('modules.topic', [$module->id, $nextTopic->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-xs font-bold text-white hover:bg-brand-600 transition shadow-theme-xs">
                <span>Materi {{ $nextTopic->topic_code }}</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
            </a>
            @elseif(auth()->user()->isMahasiswa())
            <a href="{{ route('modules.posttest.show', $module->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-warning-500 px-5 py-2 text-xs font-bold text-white hover:bg-warning-600 transition shadow-theme-xs animate-pulse">
                <span>Lanjut ke Post-Test Modul {{ $module->module_number }}</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
            </a>
            @else
            <a href="{{ route('modules.index') }}" class="rounded-lg bg-success-500 px-4 py-2 text-xs font-semibold text-white hover:bg-success-600">Modul Selesai</a>
            @endif
        </div>
    </div>
</div>
@endsection
