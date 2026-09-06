@extends('layouts.admin')

@section('title', 'Kelola & Unggah Modul Edukasi')
@section('page-title', 'Kelola Modul Edukasi Kesehatan Reproduksi')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Modul &amp; Submateri Edukasi</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Unggah modul pembelajaran baru, tambahkan submateri ilmiah, atau kelola materi yang telah dipublikasikan.</p>
        </div>
        <a href="{{ route('modules.index') }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
            Lihat Katalog Mahasiswa &rarr;
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 p-4 text-xs font-semibold text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-300">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Form Upload Modul Baru --}}
        <div class="lg:col-span-5">
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-theme-xs dark:border-brand-900/50 dark:bg-white/[0.03]">
                <div class="flex items-center gap-2 mb-1">
                    <span class="rounded-lg bg-brand-50 p-1.5 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    </span>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Unggah Modul Edukasi Baru</h3>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Tambahkan modul pembelajaran kesehatan reproduksi baru beserta materi dan video YouTube.</p>

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
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Daftar Modul &amp; Submateri Edukasi Aktif</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Admin dapat mengelola, menambah submateri baru, atau menghapus materi yang tidak relevan.</p>

                <div class="space-y-4" x-data="{ addingTopicTo: null }">
                    @foreach($modules as $mod)
                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/30">
                        {{-- Header Modul --}}
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="rounded bg-brand-100 px-2 py-0.5 text-xs font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">Modul {{ $mod->module_number }}</span>
                                    <span class="text-xs text-gray-400">&bull;</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $mod->estimated_time }}</span>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mt-1">{{ $mod->title }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $mod->subtitle }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="addingTopicTo = (addingTopicTo === {{ $mod->id }} ? null : {{ $mod->id }})"
                                    class="rounded-lg border border-brand-300 bg-brand-50 px-2.5 py-1 text-[11px] font-semibold text-brand-700 hover:bg-brand-100 dark:border-brand-700 dark:bg-brand-950 dark:text-brand-300"
                                >
                                    + Submateri
                                </button>
                                <form method="POST" action="{{ route('admin.modules.destroy', $mod->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Modul {{ $mod->module_number }} beserta seluruh submaterinya?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-error-500 hover:text-error-700 text-xs p-1" title="Hapus Modul">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                    </button>
                                </form>
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
                            @foreach($mod->topics as $tp)
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
                                    <form method="POST" action="{{ route('admin.topics.destroy', $tp->id) }}" onsubmit="return confirm('Hapus submateri {{ $tp->topic_code }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-error-500 p-0.5" title="Hapus Submateri">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
