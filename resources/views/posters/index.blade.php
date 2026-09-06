@extends('layouts.admin')

@section('title', 'Projek Visual Poster Kaspro')
@section('page-title', 'Pojok Visual Kaspro (KesproFeed)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Galeri Poster Edukasi Kaspro</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Unduh poster kualitas HD ke galeri Anda atau bagikan langsung ke WhatsApp Status dan Instagram Story.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 p-4 text-xs font-semibold text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-300">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($posters as $poster)
        <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden dark:border-gray-800 dark:bg-white/[0.03] flex flex-col justify-between">
            <div>
                {{-- Preview Card Visual --}}
                <div class="relative bg-gradient-to-br from-brand-100 to-brand-50 p-8 text-center flex flex-col items-center justify-center min-h-[190px] dark:from-brand-950 dark:to-gray-900">
                    <span class="absolute top-3 right-3 rounded-full bg-white/80 px-2.5 py-0.5 text-[10px] font-bold text-brand-700 dark:bg-gray-800 dark:text-brand-300">
                        {{ $poster->category }}
                    </span>
                    <div class="h-14 w-14 rounded-2xl bg-white flex items-center justify-center text-brand-600 shadow-theme-sm dark:bg-gray-800 dark:text-brand-400 mb-2">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-800 dark:text-white mt-1 px-4 leading-snug">{{ $poster->title }}</p>
                </div>

                {{-- Caption & Info --}}
                <div class="p-5">
                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ $poster->caption }}</p>
                    <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 text-[11px] text-gray-400">
                        <span>📥 {{ $poster->download_count }} diunduh</span>
                        <span>&bull;</span>
                        <span>📤 {{ $poster->share_count }} dibagikan</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="p-5 pt-0 grid grid-cols-2 gap-2">
                <form method="POST" action="{{ route('posters.download', $poster->id) }}">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                        Unduh HD
                    </button>
                </form>

                <a
                    href="https://wa.me/?text={{ urlencode($poster->share_text . ' ' . url('/posters')) }}"
                    target="_blank"
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-success-500 py-2 text-xs font-semibold text-white hover:bg-success-600 transition"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06-.01.24-.02.27z"/></svg>
                    WhatsApp
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
