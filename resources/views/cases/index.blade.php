@extends('layouts.admin')

@section('title', 'Studi Kasus Kampus & Forum Diskusi')
@section('page-title', 'Diskusi Anonim & Studi Kasus Mahasiswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Studi Kasus Dinamika Mahasiswa</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Bedah kasus nyata kehidupan indekos/kampus beserta analisis hukum UU TPKS dan mitigasi medis.</p>
        </div>
        <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-600 transition shadow-theme-xs">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/></svg>
            Buka Forum Tanya Jawab Anonim &rarr;
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        @foreach($caseStudies as $case)
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] flex flex-col justify-between">
            <div>
                <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-300">
                    {{ $case->category }}
                </span>
                <h3 class="text-base font-bold text-gray-900 dark:text-white mt-3">{{ $case->title }}</h3>
                <div class="rounded-xl bg-gray-50 p-4 my-3 dark:bg-gray-800">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Narasi Kasus:</p>
                    <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">{{ $case->narrative }}</p>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="p-3 rounded-lg border border-warning-100 bg-warning-50/50 dark:border-warning-900/50 dark:bg-warning-950/20">
                        <span class="font-bold text-warning-800 dark:text-warning-300">⚖️ Aspek Hukum:</span>
                        <p class="text-gray-700 dark:text-gray-300 mt-0.5">{{ $case->legal_analysis }}</p>
                    </div>
                    <div class="p-3 rounded-lg border border-error-100 bg-error-50/50 dark:border-error-900/50 dark:bg-error-950/20">
                        <span class="font-bold text-error-800 dark:text-error-300">🩺 Tinjauan Medis:</span>
                        <p class="text-gray-700 dark:text-gray-300 mt-0.5">{{ $case->medical_analysis }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs font-semibold text-success-700 dark:text-success-400">💡 Langkah Solusi &amp; Asertif:</p>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">{{ $case->solution_tips }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
