@extends('layouts.auth')
@section('title', 'Lengkapi Formulir Biodata')

@section('content')
<div class="w-full max-w-xl">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lengkapi Biodata Mahasiswa</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Data demografi diperlukan sebelum memulai pengisian Pre-Test.</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
        <form method="POST" action="{{ route('biodata.save') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="initials" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Nama Inisial Responden</label>
                    <input type="text" id="initials" name="initials" value="{{ old('initials', $user->initials ?? $user->name) }}" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="usia" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Usia (Tahun) <span class="text-error-500">*</span></label>
                    <input type="number" id="usia" name="usia" value="{{ old('usia', $user->usia ?? 18) }}" min="15" max="40" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin <span class="text-error-500">*</span></label>
                <div class="flex gap-4 pt-1">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="radio" name="gender" value="P" {{ old('gender', $user->gender ?? 'P') === 'P' ? 'checked' : '' }} required
                            class="h-4 w-4 border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600">
                        Perempuan
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="radio" name="gender" value="L" {{ old('gender', $user->gender ?? 'P') === 'L' ? 'checked' : '' }}
                            class="h-4 w-4 border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600">
                        Laki-laki
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="agama" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Agama <span class="text-error-500">*</span></label>
                    <select id="agama" name="agama" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="Islam" {{ old('agama', $user->agama ?? 'Islam') === 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen Protestan" {{ old('agama', $user->agama) === 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                        <option value="Katolik" {{ old('agama', $user->agama) === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('agama', $user->agama) === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ old('agama', $user->agama) === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Khonghucu" {{ old('agama', $user->agama) === 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                    </select>
                </div>

                <div>
                    <label for="pendidikan_terakhir" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Pendidikan Terakhir <span class="text-error-500">*</span></label>
                    <select id="pendidikan_terakhir" name="pendidikan_terakhir" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="SMA/SMK/MA/Sederajat">SMA / SMK / MA / Sederajat</option>
                        <option value="Diploma (D3/D4)">Diploma (D3/D4)</option>
                        <option value="Sarjana (S1)">Sarjana (S1)</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="prodi" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Program Studi <span class="text-error-500">*</span></label>
                <input type="text" id="prodi" name="prodi" value="{{ old('prodi', $user->prodi) }}" required
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white"
                    placeholder="Pendidikan Bahasa Inggris">
            </div>

            <button type="submit" class="w-full rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition shadow-theme-xs">
                Simpan &amp; Masuk ke Menu Utama
            </button>
        </form>
    </div>
</div>
@endsection
