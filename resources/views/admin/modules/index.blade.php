@extends('layouts.admin')

@section('title', 'Kelola & Unggah Modul Edukasi')
@section('page-title', 'Kelola Modul Edukasi Kesehatan Reproduksi')

@section('content')
<div
    x-data="{
        addingTopicTo: null,
        deleteModalOpen: false,
        moduleToDelete: { id: null, number: '', title: '' },
        uploadExcelModalOpen: false,
        selectedExcelFileName: '',
        isUploading: false
    }"
    @keydown.escape.window="deleteModalOpen = false; uploadExcelModalOpen = false"
    class="space-y-6"
>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Modul &amp; Submateri Edukasi</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Unggah modul pembelajaran baru, tambahkan submateri ilmiah, unduh atau unggah format Excel, atau kelola modul yang aktif.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a
                href="{{ route('admin.modules.template') }}"
                class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 px-3.5 py-2 text-xs font-bold text-emerald-800 hover:bg-emerald-100 dark:border-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 transition shadow-theme-xs"
                title="Download Format Template Modul Excel (.xlsx)"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                </svg>
                Format Modul (Excel)
            </a>
            <button
                type="button"
                @click="uploadExcelModalOpen = true"
                class="inline-flex items-center gap-1.5 rounded-xl border border-teal-600 bg-teal-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-teal-700 dark:border-teal-500 dark:bg-teal-600 dark:hover:bg-teal-700 transition shadow-theme-xs"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                Unggah Lewat Excel
            </button>
            <a
                href="{{ route('modules.index') }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition"
            >
                Lihat Katalog &rarr;
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 p-4 text-xs font-semibold text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-300">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-xl border border-error-200 bg-error-50 p-4 text-xs font-semibold text-error-700 dark:border-error-800 dark:bg-error-950 dark:text-error-300">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Form Upload Modul Baru --}}
        <div class="lg:col-span-5">
            {{-- Card Quick Upload Excel --}}
            <div class="mb-6 rounded-2xl border border-teal-200 bg-gradient-to-br from-teal-50/70 to-emerald-50/50 p-5 shadow-theme-xs dark:border-teal-900/50 dark:from-teal-950/40 dark:to-emerald-950/20">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-600 text-white shadow-sm dark:bg-teal-500">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="12" y1="18" x2="12" y2="12"></line>
                            <line x1="9" y1="15" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Import Modul Sekaligus via Excel</h4>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                            Unggah banyak modul, submateri ilmiah, dan butir kuesioner sekaligus menggunakan file spreadsheet <code>.xlsx</code>.
                        </p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                @click="uploadExcelModalOpen = true"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition shadow-sm"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                Unggah File Excel
                            </button>
                            <a
                                href="{{ route('admin.modules.template') }}"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-teal-700 hover:text-teal-800 dark:text-teal-300 dark:hover:text-teal-200 underline"
                            >
                                Unduh Template Format
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-theme-xs dark:border-brand-900/50 dark:bg-white/[0.03]">
                <div class="flex items-center gap-2 mb-1">
                    <span class="rounded-lg bg-brand-50 p-1.5 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    </span>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Unggah Modul Edukasi Baru</h3>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Tambahkan modul pembelajaran kesehatan reproduksi baru beserta submateri dan video YouTube.</p>

                <form method="POST" action="{{ route('admin.modules.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Nomor Modul <span class="text-error-500">*</span></label>
                            <input type="number" name="module_number" value="{{ $modules->count() + 1 }}" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Estimasi Waktu <span class="text-error-500">*</span></label>
                            <input type="text" name="estimated_time" value="15 Menit" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Judul Modul <span class="text-error-500">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Penguatan Keterampilan Hidup Sehat" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Subjudul / Ringkasan</label>
                        <input type="text" name="subtitle" placeholder="Ringkasan pengantar materi" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Deskripsi Lengkap <span class="text-error-500">*</span></label>
                        <textarea name="description" rows="2" required placeholder="Tujuan dan manfaat materi modul..." class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white"></textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">URL / Path Banner Gambar (Opsional)</label>
                        <input type="text" name="banner_image" placeholder="Contoh: /images/banners/banner-module-1.svg" class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-1.5 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        <p class="text-[10px] text-gray-400 mt-0.5">Biarkan kosong untuk menggunakan template banner otomatis SIKERA.</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50 space-y-3">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-white uppercase tracking-wider">Submateri Pertama</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="mb-1 block text-[11px] font-medium text-gray-600 dark:text-gray-400">Kode</label>
                                <input type="text" name="topic_code" value="{{ $modules->count() + 1 }}.1" required class="w-full rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                            </div>
                            <div class="col-span-2">
                                <label class="mb-1 block text-[11px] font-medium text-gray-600 dark:text-gray-400">Judul Submateri</label>
                                <input type="text" name="topic_title" required placeholder="Judul topik" class="w-full rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-medium text-gray-600 dark:text-gray-400">YouTube Video ID (Opsional)</label>
                            <input type="text" name="youtube_video_id" placeholder="Misal: v3F4QW_h32M" class="w-full rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-medium text-gray-600 dark:text-gray-400">Isi Teks Ilmiah / HTML</label>
                            <textarea name="content_html" rows="4" required placeholder="<p>Materi ilmiah komprehensif...</p>" class="w-full rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs font-mono text-gray-800 outline-none focus:border-brand-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-brand-500 py-2.5 text-xs font-bold text-white hover:bg-brand-600 transition shadow-theme-xs">
                        Unggah &amp; Publikasikan Modul
                    </button>
                </form>
            </div>
        </div>

        {{-- Daftar Modul & Submateri yang Aktif --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Daftar Modul &amp; Submateri Edukasi Aktif</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Admin dapat mengelola, menambah submateri baru, atau menghapus modul.</p>
                    </div>
                    <span class="rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-bold text-brand-700 dark:bg-brand-900/50 dark:text-brand-300">
                        {{ $modules->count() }} Modul
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse($modules as $mod)
                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/30">
                        {{-- Header Modul --}}
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="rounded bg-brand-100 px-2 py-0.5 text-xs font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">Modul {{ $mod->module_number }}</span>
                                    <span class="text-xs text-gray-400">&bull;</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $mod->estimated_time }}</span>
                                    <span class="text-xs text-gray-400">&bull;</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $mod->topics->count() }} Submateri</span>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mt-1">{{ $mod->title }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $mod->subtitle }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                {{-- Tombol Tambah Submateri --}}
                                <button
                                    type="button"
                                    @click="addingTopicTo = (addingTopicTo === {{ $mod->id }} ? null : {{ $mod->id }})"
                                    class="rounded-lg border border-brand-300 bg-brand-50 px-2.5 py-1 text-[11px] font-semibold text-brand-700 hover:bg-brand-100 dark:border-brand-700 dark:bg-brand-950 dark:text-brand-300 transition"
                                >
                                    + Submateri
                                </button>

                                {{-- Tombol Hapus Modul --}}
                                <button
                                    type="button"
                                    @click="deleteModalOpen = true; moduleToDelete = { id: {{ $mod->id }}, number: '{{ $mod->module_number }}', title: '{{ addslashes($mod->title) }}' }"
                                    class="inline-flex items-center gap-1 rounded-lg border border-error-200 bg-error-50 px-2.5 py-1 text-[11px] font-semibold text-error-600 hover:bg-error-100 dark:border-error-800 dark:bg-error-950/40 dark:text-error-400 transition"
                                    title="Hapus Modul Ini"
                                >
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>

                        {{-- Form Tambah Submateri ke Modul ini --}}
                        <div x-show="addingTopicTo === {{ $mod->id }}" x-transition class="mt-4 rounded-lg border border-brand-200 bg-white p-4 dark:border-brand-800 dark:bg-gray-900">
                            <h5 class="text-xs font-bold text-brand-600 dark:text-brand-400 mb-2">+ Tambah Submateri Baru ke Modul {{ $mod->module_number }}</h5>
                            <form method="POST" action="{{ route('admin.modules.topics.store', $mod->id) }}" class="space-y-3">
                                @csrf
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-medium text-gray-500">Kode Topik</label>
                                        <input type="text" name="topic_code" value="{{ $mod->module_number }}.{{ $mod->topics->count() + 1 }}" required class="w-full rounded border border-gray-300 px-2.5 py-1 text-xs outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-medium text-gray-500">Judul Submateri</label>
                                        <input type="text" name="title" required placeholder="Judul topik" class="w-full rounded border border-gray-300 px-2.5 py-1 text-xs outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500">YouTube Video ID (Opsional)</label>
                                    <input type="text" name="youtube_video_id" placeholder="Misal: e7zP-b-YnS0" class="w-full rounded border border-gray-300 px-2.5 py-1 text-xs outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500">Isi Konten Ilmiah (HTML)</label>
                                    <textarea name="content_html" rows="3" required placeholder="<p>Materi edukasi...</p>" class="w-full rounded border border-gray-300 px-2.5 py-1 text-xs font-mono outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="addingTopicTo = null" class="rounded px-3 py-1 text-xs text-gray-500 hover:bg-gray-100">Batal</button>
                                    <button type="submit" class="rounded bg-brand-500 px-3 py-1 text-xs font-semibold text-white hover:bg-brand-600">Simpan Submateri</button>
                                </div>
                            </form>
                        </div>

                        {{-- Submateri List --}}
                        <div class="mt-3 space-y-1.5 border-t border-gray-200/60 pt-3 dark:border-gray-700/60">
                            @forelse($mod->topics as $tp)
                            <div class="flex items-center justify-between rounded-lg bg-white p-2.5 text-xs dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $tp->topic_code }}</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $tp->title }}</span>
                                    @if($tp->youtube_video_id)
                                    <span class="rounded bg-error-50 px-1.5 py-0.2 text-[9px] font-bold text-error-600 dark:bg-error-950 dark:text-error-400">YouTube</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('modules.topic', [$mod->id, $tp->id]) }}" target="_blank" class="text-brand-600 hover:text-brand-700 font-semibold text-[11px]" title="Lihat Tampilan Mahasiswa">
                                        Lihat &rarr;
                                    </a>
                                    <form method="POST" action="{{ route('admin.topics.destroy', $tp->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus submateri {{ $tp->topic_code }} ({{ addslashes($tp->title) }})?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-error-500 p-0.5" title="Hapus Submateri">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <p class="text-[11px] text-gray-400 italic py-1">Belum ada submateri yang ditambahkan pada modul ini.</p>
                            @endforelse
                        </div>
                    </div>
                    @empty
                    <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-xs text-gray-400 dark:border-gray-700">
                        Belum ada modul edukasi yang tersedia. Silakan unggah modul baru di formulir sebelah kiri.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS MODUL --}}
    <div
        x-show="deleteModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.outside="deleteModalOpen = false"
            class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex items-center gap-3 mb-3">
                <span class="rounded-xl bg-error-50 p-2.5 text-error-600 dark:bg-error-950 dark:text-error-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Hapus Modul Edukasi</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Konfirmasi tindakan penghapusan modul.</p>
                </div>
            </div>

            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed mb-4">
                Apakah Anda yakin ingin menghapus <strong>Modul <span x-text="moduleToDelete.number"></span>: "<span x-text="moduleToDelete.title"></span>"</strong>? Tindakan ini akan <strong>menghapus seluruh submateri</strong> dan kuesioner evaluasi yang terhubung dengan modul ini. Tindakan ini tidak dapat dibatalkan.
            </p>

            <form method="POST" :action="'{{ url('/admin/modules') }}/' + moduleToDelete.id" class="flex items-center justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModalOpen = false" class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-error-600 px-4 py-2 text-xs font-bold text-white hover:bg-error-700 transition shadow-theme-xs">
                    Ya, Hapus Modul
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL UPLOAD / IMPORT EXCEL --}}
    <div
        x-show="uploadExcelModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.outside="uploadExcelModalOpen = false"
            class="relative w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <span class="rounded-xl bg-teal-50 p-2.5 text-teal-600 dark:bg-teal-950 dark:text-teal-400">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Import Modul dari Excel</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Unggah file .xlsx untuk menambahkan atau memperbarui modul edukasi.</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="uploadExcelModalOpen = false"
                    class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('admin.modules.import') }}"
                enctype="multipart/form-data"
                @submit="isUploading = true"
                class="space-y-4"
            >
                @csrf

                {{-- Alert info template --}}
                <div class="flex items-start gap-2.5 rounded-xl border border-teal-200 bg-teal-50/70 p-3.5 text-xs text-teal-800 dark:border-teal-800 dark:bg-teal-950/60 dark:text-teal-300">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="shrink-0 mt-0.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    <div class="leading-relaxed">
                        Pastikan file Excel menggunakan format resmi SIKERA yang terdiri dari 3 sheet data (<strong>Modul Utama</strong>, <strong>Submateri</strong>, dan <strong>Bank Soal</strong>).
                        <a href="{{ route('admin.modules.template') }}" class="font-bold underline ml-1 text-teal-900 dark:text-teal-200 hover:text-teal-950">Unduh Format Excel</a>
                    </div>
                </div>

                {{-- File Input Area --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Pilih File Excel (.xlsx / .xls) <span class="text-error-500">*</span>
                    </label>
                    <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 p-6 text-center hover:border-teal-500 dark:border-gray-700 dark:bg-gray-800/50 transition cursor-pointer">
                        <input
                            type="file"
                            name="excel_file"
                            accept=".xlsx, .xls"
                            required
                            @change="selectedExcelFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        >
                        <div class="flex flex-col items-center">
                            <div class="rounded-full bg-teal-100 p-3 text-teal-600 dark:bg-teal-900/50 dark:text-teal-300 mb-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="12" y1="18" x2="12" y2="12"></line>
                                    <line x1="9" y1="15" x2="15" y2="15"></line>
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-200">
                                <span class="text-teal-600 dark:text-teal-400 font-bold">Klik untuk memilih file</span> atau seret file ke sini
                            </p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Format didukung: .xlsx, .xls (Maksimal 15 MB)</p>
                            <p x-show="selectedExcelFileName" class="mt-2 text-xs font-bold text-teal-700 dark:text-teal-300 bg-teal-100/70 dark:bg-teal-900/50 px-2.5 py-1 rounded-md">
                                File terpilih: <span x-text="selectedExcelFileName"></span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Option Checkbox --}}
                <div class="flex items-center gap-2 pt-1">
                    <input
                        type="checkbox"
                        name="overwrite"
                        id="overwrite_checkbox"
                        value="1"
                        checked
                        class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800"
                    >
                    <label for="overwrite_checkbox" class="text-xs font-medium text-gray-700 dark:text-gray-300">
                        Perbarui (update) data jika nomor modul sudah ada
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button
                        type="button"
                        @click="uploadExcelModalOpen = false"
                        class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="isUploading"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-teal-600 px-4 py-2 text-xs font-bold text-white hover:bg-teal-700 transition shadow-theme-xs disabled:opacity-50"
                    >
                        <span x-show="!isUploading" class="inline-flex items-center gap-1.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            Mulai Impor Data
                        </span>
                        <span x-show="isUploading" x-cloak class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses File...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
