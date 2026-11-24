@extends('layouts.admin')

@section('title', $topic->title . ' | Flipbook Materi')
@section('page-title', 'Subbab ' . $topic->topic_code . ' · ' . $module->title)

@section('content')
<div class="mx-auto flex max-w-6xl flex-col gap-6">
    {{-- Navigasi Atas --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a
            href="{{ route('modules.show', $module->id) }}"
            class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
        >
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
            </svg>
            Kembali ke Daftar Subbab Modul {{ $module->module_number }}
        </a>

        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex min-h-9 items-center rounded-md border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-800 dark:border-teal-700 dark:bg-teal-950 dark:text-teal-200">
                Subbab {{ $topic->topic_code }} dibaca (+10 poin)
            </span>
            @if(auth()->user()->isAdmin())
            <a
                href="{{ route('admin.modules.index') }}"
                class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
            >
                Kelola PDF di Admin
            </a>
            @endif
        </div>
    </div>

    {{-- Header Identitas Subbab --}}
    <section aria-labelledby="topic-title-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex h-1.5 w-full" aria-hidden="true">
            <span class="w-20 bg-rose-600 dark:bg-rose-500"></span>
            <span class="grow bg-teal-700 dark:bg-teal-500"></span>
        </div>

        <div class="flex flex-col justify-between gap-4 p-5 sm:p-6 lg:flex-row lg:items-center">
            <div class="min-w-0">
                <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">
                    Modul {{ str_pad((string) $module->module_number, 2, '0', STR_PAD_LEFT) }} &middot; Subbab {{ $topic->topic_code }}
                    @if($hasPdfFile)
                    &middot; Flipbook PDF{{ $topic->formattedPdfSize() ? ' (' . $topic->formattedPdfSize() . ')' : '' }}
                    @endif
                </p>
                <h2 id="topic-title-heading" class="mt-1 font-editorial text-2xl font-semibold leading-snug text-slate-900 dark:text-white sm:text-3xl">
                    {{ $topic->title }}
                </h2>
                @if($hasPdfFile && $topic->pdf_original_name)
                <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                    Dokumen: {{ $topic->pdf_original_name }}
                </p>
                @endif
            </div>

            @if($allTopics->count() > 1)
            <div class="shrink-0">
                <label for="topic-quick-switcher" class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-200">
                    Pindah Subbab Modul {{ $module->module_number }}
                </label>
                <select
                    id="topic-quick-switcher"
                    onchange="if (this.value) window.location.href = this.value"
                    class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white sm:w-64"
                >
                    @foreach($allTopics as $item)
                    <option value="{{ route('modules.topic', [$module->id, $item->id]) }}" @selected($item->id === $topic->id)>
                        {{ $item->topic_code }} · {{ \Illuminate\Support\Str::limit($item->title, 42) }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </section>

    {{-- Fokus Utama: Pembaca PDF Flipbook atau Fallback HTML Lama --}}
    @if($hasPdfFile && $pdfUrl)
    <section
        aria-label="Pembaca Flipbook PDF {{ $topic->title }}"
        data-sikera-flipbook
        data-pdf-url="{{ $pdfUrl }}"
        data-title="{{ $topic->title }}"
        class="sikera-flipbook-root overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800"
    >
        {{-- State 1: Loading --}}
        <div
            data-flipbook-loading
            role="status"
            aria-live="polite"
            class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center"
        >
            <p class="text-sm font-semibold text-slate-900 dark:text-white" data-flipbook-loading-text>
                Memuat dokumen PDF flipbook...
            </p>
            <div class="h-2 w-full max-w-sm overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                <div
                    data-flipbook-loading-bar
                    class="h-full rounded-full bg-teal-700 transition-all duration-150 dark:bg-teal-400"
                    style="width: 18%"
                ></div>
            </div>
            <p class="text-xs text-slate-600 dark:text-slate-300">
                Halaman PDF sedang disiapkan agar dapat dibalik seperti buku.
            </p>
        </div>

        {{-- State 2: Error --}}
        <div
            data-flipbook-error
            role="alert"
            class="hidden flex-col items-center justify-center gap-4 px-6 py-14 text-center"
        >
            <div class="max-w-md">
                <p class="text-base font-bold text-rose-800 dark:text-rose-200">
                    Pembaca flipbook tidak dapat menampilkan dokumen ini
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-slate-600 dark:text-slate-300" data-flipbook-error-message>
                    Terjadi kendala saat mengunduh atau merender berkas PDF.
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-2.5">
                <button
                    type="button"
                    data-flipbook-retry
                    class="inline-flex min-h-11 items-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                >
                    Coba Muat Ulang
                </button>
                <a
                    href="{{ $pdfUrl }}"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700"
                >
                    Buka PDF Langsung
                </a>
            </div>
        </div>

        {{-- State 3: Ready Interactive Flipbook --}}
        <div data-flipbook-viewer class="hidden flex-col">
            {{-- Toolbar Kontrol Atas --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                {{-- Lompat Halaman --}}
                <div class="flex items-center gap-2">
                    <label for="flipbook-page-jump" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                        Halaman
                    </label>
                    <input
                        id="flipbook-page-jump"
                        data-flipbook-page-input
                        type="number"
                        min="1"
                        max="1"
                        value="1"
                        class="min-h-11 w-16 rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-center text-xs font-bold text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">
                        dari <strong data-flipbook-page-total class="text-slate-900 dark:text-white">1</strong>
                    </span>
                </div>

                {{-- Mode 1 Halaman vs 2 Halaman + Zoom + Daftar Halaman + Fullscreen --}}
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex rounded-lg border border-slate-300 bg-white p-0.5 dark:border-slate-600 dark:bg-slate-800" role="group" aria-label="Mode tampilan halaman">
                        <button
                            type="button"
                            data-flipbook-layout="single"
                            aria-pressed="false"
                            class="min-h-10 rounded-md px-3 py-1.5 text-xs font-semibold text-slate-800 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-100"
                        >
                            1 Halaman
                        </button>
                        <button
                            type="button"
                            data-flipbook-layout="spread"
                            aria-pressed="true"
                            class="min-h-10 rounded-md px-3 py-1.5 text-xs font-semibold text-slate-800 transition-colors disabled:opacity-45 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-100"
                        >
                            2 Halaman
                        </button>
                    </div>

                    <div class="inline-flex items-center rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-800" role="group" aria-label="Kontrol perbesaran halaman">
                        <button
                            type="button"
                            data-flipbook-zoom-out
                            aria-label="Perkecil halaman"
                            class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-l-lg text-sm font-bold text-slate-800 hover:bg-slate-100 disabled:opacity-40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-100 dark:hover:bg-slate-700"
                        >
                            &minus;
                        </button>
                        <button
                            type="button"
                            data-flipbook-zoom-reset
                            aria-label="Kembalikan perbesaran ke 100 persen"
                            class="min-h-11 border-x border-slate-200 px-2.5 text-xs font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-700"
                        >
                            <span data-flipbook-zoom-label>100%</span>
                        </button>
                        <button
                            type="button"
                            data-flipbook-zoom-in
                            aria-label="Perbesar halaman"
                            class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-r-lg text-sm font-bold text-slate-800 hover:bg-slate-100 disabled:opacity-40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-100 dark:hover:bg-slate-700"
                        >
                            +
                        </button>
                    </div>

                    <button
                        type="button"
                        data-flipbook-thumbs-toggle
                        aria-expanded="false"
                        class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                    >
                        Daftar Halaman
                    </button>

                    <button
                        type="button"
                        data-flipbook-fullscreen
                        aria-pressed="false"
                        class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                    >
                        <span data-fullscreen-label>Layar Penuh</span>
                    </button>

                    <a
                        href="{{ $pdfUrl }}"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex min-h-11 items-center rounded-lg border border-teal-700 bg-teal-50 px-3 py-2 text-xs font-semibold text-teal-800 hover:bg-teal-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-teal-500 dark:bg-teal-950 dark:text-teal-200 dark:hover:bg-teal-900"
                    >
                        Buka PDF
                    </a>
                </div>
            </div>

            {{-- Panel Daftar Halaman (Thumbnail Strip) --}}
            <div
                data-flipbook-thumbs-panel
                class="hidden border-b border-slate-200 bg-slate-100/80 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/80"
            >
                <div
                    data-flipbook-thumbs-grid
                    class="custom-scrollbar flex items-center gap-3 overflow-x-auto pb-1"
                ></div>
            </div>

            {{-- Meja Baca Flipbook --}}
            <div
                data-flipbook-viewport
                class="custom-scrollbar relative flex min-h-[420px] w-full flex-col items-center justify-start overflow-auto bg-parchment px-4 py-6 dark:bg-slate-950 sm:min-h-[520px] sm:py-8"
            >
                <div data-flipbook-zoom-layer class="transition-transform duration-150">
                    <div data-flipbook-mount></div>
                </div>
            </div>

            {{-- Bar Navigasi Halaman Bawah --}}
            <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-4 py-3.5 dark:border-slate-700 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between">
                <button
                    type="button"
                    data-flipbook-prev
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 disabled:opacity-45 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                >
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                    Halaman Sebelumnya
                </button>

                <div class="flex flex-1 flex-col items-center gap-1.5 sm:max-w-md sm:px-4">
                    <label for="flipbook-scrubber" class="text-xs font-semibold text-slate-800 dark:text-slate-100" data-flipbook-page-range>
                        Halaman 1 dari 1
                    </label>
                    <input
                        id="flipbook-scrubber"
                        data-flipbook-scrubber
                        type="range"
                        min="1"
                        max="1"
                        value="1"
                        class="h-2 w-full accent-teal-700 dark:accent-teal-400"
                    >
                    <p class="text-[11px] text-slate-600 dark:text-slate-300">
                        Klik sudut halaman, geser slider, atau tekan tombol panah kiri/kanan.
                    </p>
                    <span class="sr-only" aria-live="polite" data-flipbook-announce></span>
                </div>

                <button
                    type="button"
                    data-flipbook-next
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 disabled:opacity-45 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                >
                    Halaman Berikutnya
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>
    @elseif(filled($topic->content_html))
    {{-- Fallback Konten HTML Lama (Untuk Data Seeded yang Belum Diunggah PDF) --}}
    <section aria-label="Isi Materi Subbab {{ $topic->topic_code }}" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="border-b border-amber-200 bg-amber-50 px-5 py-3 text-xs text-amber-950 dark:border-amber-800 dark:bg-amber-950/60 dark:text-amber-100">
            Subbab ini masih menggunakan format teks HTML lama karena berkas PDF flipbook belum diunggah.
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.modules.index') }}" class="ml-1 font-bold underline">Unggah PDF sekarang</a>
            @endif
        </div>
        <div class="prose prose-sm max-w-none p-6 leading-relaxed text-slate-700 dark:prose-invert dark:text-slate-200">
            {!! $topic->content_html !!}
        </div>
    </section>
    @else
    {{-- Empty / Missing PDF State --}}
    <section role="status" class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center dark:border-slate-700 dark:bg-slate-800">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Berkas PDF subbab belum tersedia</h3>
        <p class="mx-auto mt-1.5 max-w-md text-xs leading-relaxed text-slate-600 dark:text-slate-300">
            Materi PDF flipbook untuk subbab {{ $topic->topic_code }} belum diunggah atau tidak ditemukan. Silakan kembali ke daftar subbab atau hubungi pengelola modul.
        </p>
        <div class="mt-4 flex flex-wrap justify-center gap-2.5">
            <a
                href="{{ route('modules.show', $module->id) }}"
                class="inline-flex min-h-11 items-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 dark:bg-teal-500 dark:text-slate-950"
            >
                Kembali ke Modul {{ $module->module_number }}
            </a>
            @if(auth()->user()->isAdmin())
            <a
                href="{{ route('admin.modules.index') }}"
                class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
            >
                Unggah PDF di Admin
            </a>
            @endif
        </div>
    </section>
    @endif

    {{-- Video Pendamping YouTube (Opsional) --}}
    @if($topic->youtube_video_id)
    <section aria-labelledby="companion-video-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-6">
        <div class="mb-4">
            <h3 id="companion-video-heading" class="text-base font-bold text-slate-900 dark:text-white">
                Video Pendamping Subbab {{ $topic->topic_code }}
            </h3>
            <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                Tonton penjelasan singkat ini untuk melengkapi materi pada flipbook PDF di atas.
            </p>
        </div>
        <div class="relative aspect-video w-full overflow-hidden rounded-lg border border-slate-200 bg-slate-950 dark:border-slate-700">
            <iframe
                class="h-full w-full"
                width="1280"
                height="720"
                src="https://www.youtube.com/embed/{{ $topic->youtube_video_id }}"
                title="Video pendamping {{ $topic->title }}"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>
        </div>
    </section>
    @endif

    {{-- Navigasi Antar Subbab / Lanjut Post-Test --}}
    <nav aria-label="Navigasi antar subbab" class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:flex-row sm:items-center sm:justify-between">
        @if($prevTopic)
        <a
            href="{{ route('modules.topic', [$module->id, $prevTopic->id]) }}"
            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700"
        >
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
            Subbab {{ $prevTopic->topic_code }}: {{ \Illuminate\Support\Str::limit($prevTopic->title, 34) }}
        </a>
        @else
        <a
            href="{{ route('modules.show', $module->id) }}"
            class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700"
        >
            Daftar Subbab Modul {{ $module->module_number }}
        </a>
        @endif

        @if($nextTopic)
        <a
            href="{{ route('modules.topic', [$module->id, $nextTopic->id]) }}"
            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
        >
            Subbab {{ $nextTopic->topic_code }}: {{ \Illuminate\Support\Str::limit($nextTopic->title, 34) }}
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
            </svg>
        </a>
        @elseif(auth()->user()->isMahasiswa())
        <a
            href="{{ route('modules.posttest.show', $module->id) }}"
            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-amber-700 px-5 py-2 text-xs font-semibold text-white hover:bg-amber-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700 dark:bg-amber-500 dark:text-slate-950 dark:hover:bg-amber-400"
        >
            Kerjakan Post-Test Modul {{ $module->module_number }}
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
            </svg>
        </a>
        @else
        <a
            href="{{ route('modules.index') }}"
            class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 dark:bg-teal-500 dark:text-slate-950"
        >
            Kembali ke Katalog Modul
        </a>
        @endif
    </nav>
</div>
@endsection

@if($hasPdfFile && $pdfUrl)
    @push('scripts')
        @vite('resources/js/flipbook.js')
    @endpush
@endif
