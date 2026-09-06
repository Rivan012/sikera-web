@extends('layouts.admin')

@section('title', 'Kelola & Unggah Poster Kaspro')
@section('page-title', 'Unggah & Kelola Poster Kaspro (KesproFeed)')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Poster Kaspro</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Unggah poster visual edukatif baru atau kelola galeri poster Kaspro untuk mahasiswa.</p>
        </div>
        <a href="{{ route('posters.index') }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
            Lihat Galeri Mahasiswa &rarr;
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 p-4 text-xs font-semibold text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-300">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Form Upload Poster Kaspro --}}
        <div class="lg:col-span-5">
            <div class="rounded-2xl border border-warning-200 bg-white p-6 shadow-theme-xs dark:border-warning-900/50 dark:bg-white/[0.03]">
                <div class="flex items-center gap-2 mb-1">
                    <span class="rounded-lg bg-warning-50 p-1.5 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                    </span>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Unggah Poster Kaspro Baru</h3>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Tambahkan materi visual / pamflet edukatif untuk Pojok Visual Kaspro.</p>

                <form method="POST" action="{{ route('admin.posters.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Judul Poster <span class="text-error-500">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Panduan Gizi Remaja Cegah Stunting" class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Kategori Poster <span class="text-error-500">*</span></label>
                        <select name="category" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="Higienitas">Higienitas Genitalia</option>
                            <option value="Kesehatan Medis">Kesehatan Medis &amp; Haid</option>
                            <option value="Relasi Sehat">Relasi Sehat &amp; Antikekerasan</option>
                            <option value="Pencegahan Stunting">Pencegahan Stunting &amp; Gizi</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Keterangan / Caption Edukasi <span class="text-error-500">*</span></label>
                        <textarea name="caption" rows="3" required placeholder="Pesan penting yang disampaikan poster..." class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white"></textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Teks Share WhatsApp / Media Sosial</label>
                        <input type="text" name="share_text" placeholder="Yuk simak poster edukasi kesehatan dari SIKERA UNIB!" class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-warning-500 py-2.5 text-xs font-bold text-white hover:bg-warning-600 transition shadow-theme-xs">
                        Simpan &amp; Publikasikan Poster Kaspro
                    </button>
                </form>
            </div>
        </div>

        {{-- Daftar Poster yang Sudah Ada --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Daftar Poster Kaspro Aktif ({{ $posters->count() }})</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Poster yang tampil di galeri visual mahasiswa.</p>

                <div class="space-y-3">
                    @forelse($posters as $p)
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 p-4 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="h-10 w-10 rounded-xl bg-warning-50 text-warning-600 flex items-center justify-center dark:bg-warning-950 dark:text-warning-400 flex-shrink-0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="rounded bg-warning-50 px-2 py-0.5 text-[10px] font-bold text-warning-700 dark:bg-warning-950 dark:text-warning-300">{{ $p->category }}</span>
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white mt-1 truncate">{{ $p->title }}</h4>
                                <p class="text-[11px] text-gray-400 mt-0.5">📥 {{ $p->download_count }} diunduh &bull; 📤 {{ $p->share_count }} dibagikan</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <form method="POST" action="{{ route('admin.posters.destroy', $p->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus poster ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg p-1.5 text-gray-400 hover:text-error-600 hover:bg-error-50 dark:hover:bg-error-950/50 transition" title="Hapus Poster">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 text-center py-8">Belum ada poster Kaspro yang diunggah.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
