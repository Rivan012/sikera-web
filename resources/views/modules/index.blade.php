@extends('layouts.admin')

@section('title', 'Modul Edukasi')
@section('page-title', 'Modul Pembelajaran Kesehatan Reproduksi')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header Katalog Modul dengan Editorial Index Bar --}}
    <section aria-labelledby="catalog-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex h-1.5 w-full" aria-hidden="true">
            <span class="w-20 bg-rose-600 dark:bg-rose-500"></span>
            <span class="grow bg-teal-700 dark:bg-teal-500"></span>
        </div>

        <div class="flex flex-col justify-between gap-4 p-5 sm:p-6 sm:flex-row sm:items-center">
            <div class="max-w-2xl">
                <h2 id="catalog-heading" class="text-xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-2xl">
                    Katalog Modul Pembelajaran
                </h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300 sm:text-sm">
                    Kerjakan Pre-Test pada awal modul untuk membuka akses flipbook PDF setiap subbab. Setelah seluruh subbab dibaca, selesaikan Post-Test untuk mencatat skor N-Gain.
                </p>
            </div>

            @if(auth()->user()->isAdmin())
            <a
                href="{{ route('admin.modules.index') }}"
                class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
            >
                Kelola &amp; Unggah Modul PDF
            </a>
            @endif
        </div>
    </section>

    {{-- Grid Modul Responsif --}}
    @if($modules->isEmpty())
    <section class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-slate-700 dark:bg-slate-800">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum ada modul pembelajaran tersedia</h3>
        <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
            Kurikulum modul edukasi akan muncul di halaman ini setelah dipublikasikan oleh pengelola riset.
        </p>
    </section>
    @else
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @foreach($modules as $module)
        @php
            $stat = $moduleStats[$module->id] ?? [
                'completed' => 0,
                'total' => 0,
                'pdf_count' => 0,
                'percentage' => 0,
                'has_pretest' => false,
                'all_topics_done' => false,
                'has_posttest' => false,
                'posttest_score' => null,
                'n_gain' => null,
            ];
            $bannerUrl = $module->banner_image ?: '/images/banners/banner-module-' . (($module->module_number % 4) ?: 4) . '.svg';
        @endphp
        <article class="flex flex-col justify-between overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div>
                <div class="relative aspect-[5/2] w-full overflow-hidden bg-slate-900">
                    <img
                        src="{{ $bannerUrl }}"
                        alt="Banner Modul {{ $module->module_number }}: {{ $module->title }}"
                        width="800"
                        height="320"
                        @if($loop->first) fetchpriority="high" @else loading="lazy" @endif
                        class="h-full w-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>

                    <div class="absolute top-3 right-3">
                        @if($stat['has_posttest'])
                        <span class="rounded-md bg-teal-700 px-2.5 py-1 text-[11px] font-semibold text-white">
                            Selesai (N-Gain: {{ $stat['n_gain'] }})
                        </span>
                        @elseif($stat['all_topics_done'])
                        <span class="rounded-md bg-amber-700 px-2.5 py-1 text-[11px] font-semibold text-white">
                            Siap Post-Test
                        </span>
                        @elseif($stat['has_pretest'])
                        <span class="rounded-md bg-slate-900/90 px-2.5 py-1 text-[11px] font-semibold text-white">
                            Akses Terbuka
                        </span>
                        @else
                        <span class="rounded-md bg-slate-950/90 px-2.5 py-1 text-[11px] font-semibold text-slate-100">
                            Pre-Test Wajib
                        </span>
                        @endif
                    </div>
                </div>

                <div class="p-5 pb-4">
                    <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">
                        Modul {{ str_pad((string) $module->module_number, 2, '0', STR_PAD_LEFT) }} &middot; {{ $module->estimated_time }}
                    </p>
                    <h3 class="mt-1 text-base font-bold leading-snug text-slate-900 dark:text-white">{{ $module->title }}</h3>
                    @if($module->subtitle)
                    <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ $module->subtitle }}</p>
                    @endif

                    <div class="mt-4 border-t border-slate-200 pt-3 dark:border-slate-700">
                        <div class="mb-1.5 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <span class="font-medium text-slate-600 dark:text-slate-300">
                                {{ $stat['completed'] }}/{{ $stat['total'] }} subbab selesai
                                @if($stat['pdf_count'] > 0)
                                &middot; {{ $stat['pdf_count'] }} Flipbook PDF
                                @endif
                            </span>
                            <span class="font-bold text-teal-700 dark:text-teal-300">{{ $stat['percentage'] }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700" role="progressbar" aria-valuenow="{{ $stat['percentage'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progres Modul {{ $module->module_number }}">
                            <div class="h-full rounded-full bg-teal-700 dark:bg-teal-400" style="width: {{ $stat['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-5 pb-5">
                @if(! $stat['has_pretest'] && auth()->user()->isMahasiswa())
                <a
                    href="{{ route('modules.pretest.show', $module->id) }}"
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-xs font-semibold text-white transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                >
                    Mulai Pre-Test Modul {{ $module->module_number }}
                </a>
                @elseif($stat['all_topics_done'] && ! $stat['has_posttest'] && auth()->user()->isMahasiswa())
                <a
                    href="{{ route('modules.posttest.show', $module->id) }}"
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-amber-700 px-4 py-2.5 text-xs font-semibold text-white transition-colors hover:bg-amber-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700 dark:bg-amber-500 dark:text-slate-950 dark:hover:bg-amber-400"
                >
                    Kerjakan Post-Test Modul {{ $module->module_number }}
                </a>
                @else
                <a
                    href="{{ route('modules.show', $module->id) }}"
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-slate-300 bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-900 transition-colors hover:border-teal-700 hover:bg-teal-50 hover:text-teal-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:hover:border-teal-400 dark:hover:bg-slate-700"
                >
                    Buka Daftar Subbab Modul {{ $module->module_number }}
                </a>
                @endif
            </div>
        </article>
        @endforeach
    </div>
    @endif
</div>
@endsection
