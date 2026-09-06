@extends('layouts.admin')
@section('title', 'Modul Edukasi')
@section('page-title', 'Modul Pembelajaran Kesehatan Reproduksi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Katalog Modul Pembelajaran</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Pre-Test wajib dikerjakan sebelum membuka materi modul. Post-Test dikerjakan setelah seluruh materi modul selesai dipelajari.</p>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.modules.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-600 transition shadow-theme-xs">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Kelola &amp; Unggah Modul Baru
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 p-4 text-xs font-semibold text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-300">
        {{ session('success') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="rounded-xl border border-warning-200 bg-warning-50 p-4 text-xs font-semibold text-warning-700 dark:border-warning-800 dark:bg-warning-950 dark:text-warning-300">
        {{ session('warning') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        @foreach($modules as $module)
        @php
            $stat = $moduleStats[$module->id] ?? [
                'completed' => 0,
                'total' => 0,
                'percentage' => 0,
                'has_pretest' => false,
                'all_topics_done' => false,
                'has_posttest' => false,
                'posttest_score' => null,
                'n_gain' => null,
            ];
            $bannerUrl = $module->banner_image ?: '/images/banners/banner-module-' . (($module->module_number % 4) ?: 4) . '.svg';
        @endphp
        <div class="rounded-2xl border {{ $stat['has_posttest'] ? 'border-success-200 dark:border-success-900/50' : (!$stat['has_pretest'] ? 'border-gray-200 dark:border-gray-800' : 'border-brand-200 dark:border-brand-900/50') }} bg-white overflow-hidden shadow-theme-xs dark:bg-white/[0.03] flex flex-col justify-between group">
            <div>
                {{-- Banner Modul Visual --}}
                <div class="relative w-full h-40 bg-gray-900 overflow-hidden">
                    <img src="{{ $bannerUrl }}" alt="{{ $module->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

                    {{-- Badge Status di Atas Banner --}}
                    <div class="absolute top-3 left-3 right-3 flex items-center justify-between">
                        <span class="rounded-lg bg-black/60 backdrop-blur-sm px-2.5 py-1 text-[11px] font-bold text-white shadow-theme-xs">
                            Modul {{ $module->module_number }}
                        </span>

                        @if($stat['has_posttest'])
                        <span class="rounded-full bg-success-500/90 backdrop-blur-sm px-2.5 py-0.5 text-[10px] font-bold text-white shadow-theme-xs">
                            ✓ Selesai (Gain: {{ $stat['n_gain'] }})
                        </span>
                        @elseif($stat['all_topics_done'])
                        <span class="rounded-full bg-warning-500/90 backdrop-blur-sm px-2.5 py-0.5 text-[10px] font-bold text-white shadow-theme-xs animate-pulse">
                            ⚡ Siap Post-Test
                        </span>
                        @elseif($stat['has_pretest'])
                        <span class="rounded-full bg-brand-500/90 backdrop-blur-sm px-2.5 py-0.5 text-[10px] font-bold text-white shadow-theme-xs">
                            📖 Sedang Belajar
                        </span>
                        @else
                        <span class="rounded-full bg-black/70 backdrop-blur-sm px-2.5 py-0.5 text-[10px] font-bold text-gray-200">
                            🔒 Pre-Test Wajib
                        </span>
                        @endif
                    </div>

                    {{-- Estimasi Waktu di Kanan Bawah Banner --}}
                    <div class="absolute bottom-2.5 right-3 text-[11px] font-semibold text-white/90 drop-shadow">
                        ⏱️ {{ $module->estimated_time }}
                    </div>
                </div>

                {{-- Deskripsi Modul --}}
                <div class="p-5 pb-3">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug">{{ $module->title }}</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">{{ $module->subtitle }}</p>

                    {{-- Progress Bar --}}
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-1.5 text-xs">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">{{ $stat['completed'] }}/{{ $stat['total'] }} submateri selesai</span>
                            <span class="font-bold {{ $stat['has_posttest'] ? 'text-success-600 dark:text-success-400' : 'text-brand-600 dark:text-brand-400' }}">{{ $stat['percentage'] }}%</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-1.5 rounded-full {{ $stat['has_posttest'] ? 'bg-success-500' : 'bg-brand-500' }} transition-all duration-300" style="width: {{ $stat['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="p-5 pt-0 flex items-center justify-between">
                @if(!$stat['has_pretest'] && auth()->user()->isMahasiswa())
                <a href="{{ route('modules.pretest.show', $module->id) }}" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-500 py-2.5 text-xs font-bold text-white hover:bg-brand-600 transition shadow-theme-xs">
                    <span>Mulai Pre-Test Modul {{ $module->module_number }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                </a>
                @elseif($stat['all_topics_done'] && !$stat['has_posttest'] && auth()->user()->isMahasiswa())
                <a href="{{ route('modules.posttest.show', $module->id) }}" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-warning-500 py-2.5 text-xs font-bold text-white hover:bg-warning-600 transition shadow-theme-xs animate-pulse">
                    <span>Kerjakan Post-Test Modul {{ $module->module_number }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                </a>
                @else
                <a href="{{ route('modules.show', $module->id) }}" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-gray-50 py-2.5 text-xs font-bold text-gray-800 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition">
                    <span>Buka Materi Modul</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
