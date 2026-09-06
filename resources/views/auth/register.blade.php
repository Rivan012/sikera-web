@extends('layouts.auth')
@section('title', 'Formulir Biodata Mahasiswa Baru')

@section('content')
<div class="w-full max-w-xl">
    <div class="mb-6 text-center">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500 text-white font-bold text-xl shadow-theme-xs mb-3">
            S
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Formulir Pendaftaran Mahasiswa</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silakan lengkapi data demografi untuk membuat akun SIKERA.</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
        @if($errors->any())
        <div class="mb-5 rounded-xl bg-error-50 p-4 text-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- 1. Nama & Inisial --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Nama Lengkap / Nama Panggilan <span class="text-error-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                        placeholder="Contoh: Aisyah Putri">
                </div>
                <div>
                    <label for="initials" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Inisial Responden</label>
                    <input type="text" id="initials" name="initials" value="{{ old('initials') }}" maxlength="10"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                        placeholder="APM">
                </div>
            </div>

            {{-- 2. Usia & Jenis Kelamin --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="usia" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Usia (Tahun) <span class="text-error-500">*</span></label>
                    <input type="number" id="usia" name="usia" value="{{ old('usia', 18) }}" min="15" max="40" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                        placeholder="18">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin <span class="text-error-500">*</span></label>
                    <div class="flex gap-4 pt-1.5">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="radio" name="gender" value="P" {{ old('gender', 'P') === 'P' ? 'checked' : '' }} required
                                class="h-4 w-4 border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600">
                            Perempuan
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="radio" name="gender" value="L" {{ old('gender') === 'L' ? 'checked' : '' }}
                                class="h-4 w-4 border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600">
                            Laki-laki
                        </label>
                    </div>
                </div>
            </div>

            {{-- 3. Agama & Pendidikan Terakhir --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="agama" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Agama <span class="text-error-500">*</span></label>
                    <select id="agama" name="agama" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="">Pilih Agama</option>
                        <option value="Islam" {{ old('agama', 'Islam') === 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen Protestan" {{ old('agama') === 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                        <option value="Katolik" {{ old('agama') === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('agama') === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ old('agama') === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Khonghucu" {{ old('agama') === 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                        <option value="Lainnya" {{ old('agama') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="pendidikan_terakhir" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Pendidikan Terakhir <span class="text-error-500">*</span></label>
                    <select id="pendidikan_terakhir" name="pendidikan_terakhir" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="SMA/SMK/MA/Sederajat" {{ old('pendidikan_terakhir', 'SMA/SMK/MA/Sederajat') === 'SMA/SMK/MA/Sederajat' ? 'selected' : '' }}>SMA / SMK / MA / Sederajat</option>
                        <option value="Diploma (D3/D4)" {{ old('pendidikan_terakhir') === 'Diploma (D3/D4)' ? 'selected' : '' }}>Diploma (D3/D4)</option>
                        <option value="Sarjana (S1)" {{ old('pendidikan_terakhir') === 'Sarjana (S1)' ? 'selected' : '' }}>Sarjana (S1)</option>
                    </select>
                </div>
            </div>

            {{-- 4. Program Studi & Fakultas --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="prodi" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Program Studi <span class="text-error-500">*</span></label>
                    <input type="text" id="prodi" name="prodi" value="{{ old('prodi') }}" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                        placeholder="Pendidikan Biologi / Informatika / dll">
                </div>

                <div>
                    <label for="fakultas" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Fakultas</label>
                    <select id="fakultas" name="fakultas"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="Fakultas Keguruan dan Ilmu Pendidikan (FKIP)">FKIP</option>
                        <option value="Fakultas Hukum (FH)">Fakultas Hukum</option>
                        <option value="Fakultas Ekonomi dan Bisnis (FEB)">FEB</option>
                        <option value="Fakultas Ilmu Sosial dan Ilmu Politik (FISIP)">FISIP</option>
                        <option value="Fakultas Pertanian (FP)">Fakultas Pertanian</option>
                        <option value="Fakultas Matematika dan Ilmu Pengetahuan Alam (FMIPA)">FMIPA</option>
                        <option value="Fakultas Teknik (FT)">Fakultas Teknik</option>
                    </select>
                </div>
            </div>

            {{-- 5. Email & NIM & Password --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="nim" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">NIM Mahasiswa</label>
                    <input type="text" id="nim" name="nim" value="{{ old('nim') }}"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                        placeholder="A1D026XXX">
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Alamat Email <span class="text-error-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                        placeholder="nama@unib.ac.id">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Kata Sandi <span class="text-error-500">*</span></label>
                    <input type="password" id="password" name="password" required minlength="6"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                        placeholder="Min. 6 karakter">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Konfirmasi Kata Sandi <span class="text-error-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                        placeholder="Ulangi kata sandi">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition shadow-theme-xs">
                    Daftar &amp; Masuk ke Menu Utama
                </button>
            </div>
        </form>

        <div class="mt-5 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Sudah pernah mendaftar?
                <a href="{{ route('login') }}" class="font-semibold text-brand-500 hover:text-brand-600 dark:text-brand-400">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
