@extends('layouts.admin')

@section('title', $module->title)
@section('page-title', 'Modul ' . $module->module_number . ' · ' . $module->title)

@section('content')
<div class="mx-auto flex max-w-4xl flex-col gap-6">
    <a
        href="{{ route('modules.index') }}"
        class="inline-flex min-h-11 w-fit items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
    >
        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
        </svg>
        Kembali ke Katalog Modul
    </a>

    @php
        $bannerUrl = $module->banner_image ?: '/images/banners/banner-module-' . (($module->module_number % 4) ?: 4) . '.svg';
        $pdfCount = $module->topics->filter(fn ($t) => $t->hasPdf())->count();
    @endphp

    {{-- Kartu Wadah Modul --}}
    <section aria-labelledby="module-detail-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="relative aspect-[5/2] w-full overflow-hidden bg-slate-900">
            <img
                src="{{ $bannerUrl }}"
                alt="Banner Modul {{ $module->module_number }}: {{ $module->title }}"
                width="800"
                height="320"
                fetchpriority="high"
                class="h-full w-full object-cover"
            >
        </div>

        <div class="flex h-1.5 w-full" aria-hidden="true">
            <span class="w-20 bg-rose-600 dark:bg-rose-500"></span>
            <span class="grow bg-teal-700 dark:bg-teal-500"></span>
        </div>

        <div class="p-5 sm:p-6">
            <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">
                Modul {{ str_pad((string) $module->module_number, 2, '0', STR_PAD_LEFT) }} &middot; {{ $module->estimated_time }} &middot; {{ $module->topics->count() }} Subbab
            </p>
            <h2 id="module-detail-heading" class="mt-1.5 font-editorial text-2xl font-semibold leading-tight text-slate-900 dark:text-white sm:text-3xl">
                {{ $module->title }}
            </h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-700 dark:text-slate-200">{{ $module->description }}</p>

            @if($allTopicsDone && ! $posttest && auth()->user()->isMahasiswa())
            <div class="mt-5 flex flex-col justify-between gap-3 rounded-xl border border-amber-300 bg-amber-50 p-4 dark:border-amber-700 dark:bg-amber-950/60 sm:flex-row sm:items-center">
                <div>
                    <p class="text-sm font-bold text-amber-950 dark:text-amber-100">Seluruh subbab selesai dipelajari</p>
                    <p class="mt-0.5 text-xs text-amber-900 dark:text-amber-200">
                        Kerjakan Post-Test Modul {{ $module->module_number }} untuk mengukur peningkatan pemahaman dan indeks N-Gain Anda.
                    </p>
                </div>
                <a
                    href="{{ route('modules.posttest.show', $module->id) }}"
                    class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-lg bg-amber-700 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700 dark:bg-amber-500 dark:text-slate-950 dark:hover:bg-amber-400"
                >
                    Kerjakan Post-Test Modul {{ $module->module_number }}
                </a>
            </div>
            @elseif($posttest)
            <div class="mt-5 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-teal-300 bg-teal-50 p-4 dark:border-teal-700 dark:bg-teal-950/60">
                <div>
                    <p class="text-sm font-bold text-teal-950 dark:text-teal-100">Post-Test Modul {{ $module->module_number }} Tuntas</p>
                    <p class="mt-0.5 text-xs text-teal-800 dark:text-teal-200">
                        Skor Post-Test: {{ $posttest->total_score }}/100 &middot; Indeks N-Gain: {{ $posttest->n_gain_score }}
                    </p>
                </div>
                <span class="rounded-md bg-teal-700 px-3 py-1 text-xs font-semibold text-white dark:bg-teal-500 dark:text-slate-950">
                    Selesai
                </span>
            </div>
            @endif
        </div>
    </section>

    {{-- Daftar Subbab Flipbook PDF --}}
    <section aria-labelledby="subbab-list-heading" class="flex flex-col gap-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h3 id="subbab-list-heading" class="text-base font-bold text-slate-900 dark:text-white">
                Daftar Subbab Materi ({{ $module->topics->count() }} Subbab)
            </h3>
            @if($pdfCount > 0)
            <span class="text-xs font-semibold text-teal-700 dark:text-teal-300">
                {{ $pdfCount }} subbab siap dibaca sebagai Flipbook PDF
            </span>
            @endif
        </div>

        @forelse($module->topics as $topic)
        @php
            $isDone = in_array($topic->id, $completedTopicIds, true);
            $hasPdf = $topic->hasPdf();
            $pdfSizeLabel = $topic->formattedPdfSize();
        @endphp
        <a
            href="{{ route('modules.topic', [$module->id, $topic->id]) }}"
            class="group flex min-h-11 flex-col justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-xs transition-colors hover:border-teal-700 hover:bg-teal-50/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-teal-400 dark:hover:bg-slate-700/70 sm:flex-row sm:items-center"
        >
            <div class="flex min-w-0 items-start gap-3.5">
                @if($isDone)
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-teal-700 text-white dark:bg-teal-500 dark:text-slate-950" aria-label="Sudah dibaca">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                </span>
                @else
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-slate-50 text-xs font-bold text-slate-800 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                    {{ $topic->topic_code }}
                </span>
                @endif

                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-900 group-hover:text-teal-800 dark:text-white dark:group-hover:text-teal-300">
                        {{ $topic->title }}
                    </p>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                        <span>Subbab {{ $topic->topic_code }}</span>
                        @if($hasPdf)
                        <span aria-hidden="true">&middot;</span>
                        <span class="font-semibold text-teal-700 dark:text-teal-300">
                            Flipbook PDF{{ $pdfSizeLabel ? ' (' . $pdfSizeLabel . ')' : '' }}
                        </span>
                        @endif
                        @if($topic->youtube_video_id)
                        <span aria-hidden="true">&middot;</span>
                        <span class="font-semibold text-rose-700 dark:text-rose-300">Video YouTube</span>
                        @endif
                    </div>
                </div>
            </div>

            <span class="inline-flex min-h-10 shrink-0 items-center self-start rounded-lg border border-teal-700 bg-teal-50 px-3 py-1.5 text-xs font-semibold text-teal-800 group-hover:bg-teal-700 group-hover:text-white dark:border-teal-500 dark:bg-teal-950 dark:text-teal-200 dark:group-hover:bg-teal-500 dark:group-hover:text-slate-950 sm:self-center">
                @if($hasPdf)
                    {{ $isDone ? 'Baca Ulang Flipbook' : 'Buka Flipbook' }}
                @else
                    {{ $isDone ? 'Baca Ulang' : 'Mulai Baca' }}
                @endif
            </span>
        </a>
        @empty
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Belum ada subbab pada modul ini</p>
            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                Pengelola kurikulum belum mengunggah berkas PDF subbab untuk Modul {{ $module->module_number }}.
            </p>
        </div>
        @endforelse
    </section>
</div>
@endsection
