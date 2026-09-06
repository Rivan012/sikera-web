@extends('layouts.admin')
@section('title', 'Buat Pertanyaan')
@section('page-title', 'Buat Pertanyaan Anonim')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('forum.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-brand-500 dark:text-gray-400">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Kembali ke Forum
    </a>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Tulis Pertanyaan Anonim</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Identitas Anda akan disembunyikan. Pertanyaan dapat dijawab oleh Dosen PA atau konselor kampus.</p>

        <form method="POST" action="{{ route('forum.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Pertanyaan</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white"
                    placeholder="Contoh: Apakah wajar mengalami nyeri haid parah setiap bulan?">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Topik</label>
                <select name="topic" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    <option value="">Pilih Topik</option>
                    <option value="Haid" {{ old('topic') === 'Haid' ? 'selected' : '' }}>Haid & Siklus Menstruasi</option>
                    <option value="Hubungan" {{ old('topic') === 'Hubungan' ? 'selected' : '' }}>Hubungan & Relasi</option>
                    <option value="Medis" {{ old('topic') === 'Medis' ? 'selected' : '' }}>Kesehatan Medis</option>
                    <option value="Darurat" {{ old('topic') === 'Darurat' ? 'selected' : '' }}>Situasi Darurat</option>
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Isi Pertanyaan</label>
                <textarea name="content" rows="5" required minlength="10"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white"
                    placeholder="Jelaskan pertanyaan atau situasi yang Anda alami...">{{ old('content') }}</textarea>
            </div>

            <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Kirim Pertanyaan Anonim</button>
        </form>
    </div>
</div>
@endsection
