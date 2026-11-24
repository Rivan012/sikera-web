@extends('layouts.auth')
@section('title', 'Formulir Pendaftaran Mahasiswa')

@section('content')
@php
    $selectedFakultas = old('fakultas', 'Fakultas Keguruan dan Ilmu Pendidikan (FKIP)');
    $daftarFakultas = [
        'Fakultas Keguruan dan Ilmu Pendidikan (FKIP)',
        'Fakultas Hukum (FH)',
        'Fakultas Ekonomi dan Bisnis (FEB)',
        'Fakultas Ilmu Sosial dan Ilmu Politik (FISIP)',
        'Fakultas Pertanian (FP)',
        'Fakultas Matematika dan Ilmu Pengetahuan Alam (FMIPA)',
        'Fakultas Teknik (FT)',
        'Fakultas Kedokteran dan Ilmu Kesehatan (FKIK)',
    ];
@endphp

<div
    class="w-full"
    x-data="{
        name: @js(old('name', '')),
        initials: @js(old('initials', '')),
        gender: @js(old('gender', 'P')),
        showPassword: false,
        submitting: false,
        get autoInitials() {
            const parts = this.name.trim().split(/\s+/).filter(Boolean);
            if (!parts.length) return '';
            return parts.map(p => p.charAt(0).toUpperCase()).join('').slice(0, 6);
        }
    }"
>
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-8">
        {{-- Kolom Panduan & Navigasi Bagian Formulir --}}
        <aside aria-label="Panduan Pendaftaran Responden" class="flex flex-col gap-5 lg:sticky lg:top-6 lg:col-span-4">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">Pendaftaran Responden Baru</p>
                <h1 class="mt-1 text-xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-2xl">
                    Formulir Biodata Mahasiswa
                </h1>
                <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                    Lengkapi data demografi dan afiliasi studi untuk membuat akun pembelajaran serta evaluasi di SIKERA.
                </p>

                <nav aria-label="Bagian formulir pendaftaran" class="mt-5 border-t border-slate-200 pt-4 dark:border-slate-700">
                    <p class="mb-2 text-xs font-semibold text-slate-700 dark:text-slate-200">Struktur Pengisian</p>
                    <ol class="flex flex-col gap-1">
                        <li>
                            <a
                                href="#bagian-demografi"
                                class="flex min-h-11 items-center gap-3 rounded-lg px-2.5 py-2 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-200 dark:hover:bg-slate-700/70 dark:focus-visible:outline-teal-400"
                            >
                                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-teal-50 text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-200">01</span>
                                <span>Identitas &amp; Data Demografi</span>
                            </a>
                        </li>
                        <li>
                            <a
                                href="#bagian-akademik"
                                class="flex min-h-11 items-center gap-3 rounded-lg px-2.5 py-2 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-200 dark:hover:bg-slate-700/70 dark:focus-visible:outline-teal-400"
                            >
                                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-teal-50 text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-200">02</span>
                                <span>Afiliasi Akademik Kampus</span>
                            </a>
                        </li>
                        <li>
                            <a
                                href="#bagian-akun"
                                class="flex min-h-11 items-center gap-3 rounded-lg px-2.5 py-2 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-200 dark:hover:bg-slate-700/70 dark:focus-visible:outline-teal-400"
                            >
                                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-teal-50 text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-200">03</span>
                                <span>Kredensial Akun SIKERA</span>
                            </a>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="rounded-xl bg-teal-900 p-5 text-white shadow-sm dark:border dark:border-slate-700 dark:bg-slate-800">
                <p class="inline-flex items-center gap-2 text-xs font-semibold text-amber-300">
                    <span class="h-2 w-2 rounded-xs bg-amber-400" aria-hidden="true"></span>
                    <span>Etika &amp; Privasi Responden</span>
                </p>
                <p class="mt-2 text-xs leading-relaxed text-teal-50 dark:text-slate-300">
                    Pada lembar rekapitulasi evaluasi riset dan ruang konsultasi anonim, identitas Anda dicatat menggunakan inisial responden untuk menjaga kenyamanan privasi mahasiswa.
                </p>
                <div class="mt-4 border-t border-teal-800 pt-3 dark:border-slate-700">
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex min-h-11 items-center rounded-lg px-1 text-xs font-semibold text-amber-300 underline decoration-amber-300/50 underline-offset-4 transition-colors hover:text-amber-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300"
                    >
                        Sudah memiliki akun? Masuk di sini
                    </a>
                </div>
            </div>
        </aside>

        {{-- Kolom Utama: Instrumen Formulir Pendaftaran (Fokus Utama Layar) --}}
        <section aria-label="Formulir Pendaftaran Mahasiswa Baru" class="lg:col-span-8">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex h-1.5 w-full" aria-hidden="true">
                    <span class="w-20 bg-rose-600 dark:bg-rose-500"></span>
                    <span class="grow bg-teal-700 dark:bg-teal-500"></span>
                </div>

                <div class="p-6 sm:p-8">
                    @if($errors->any())
                    <div role="alert" class="mb-6 rounded-lg border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900 dark:border-rose-700 dark:bg-rose-950/80 dark:text-rose-100">
                        <p class="font-semibold">Mohon periksa kembali isian formulir berikut:</p>
                        <ul class="mt-2 flex list-disc flex-col gap-1 pl-5 text-xs font-medium">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" @submit="submitting = true" class="flex flex-col gap-8">
                        @csrf

                        {{-- BAGIAN 01: IDENTITAS & DEMOGRAFI --}}
                        <fieldset id="bagian-demografi" class="scroll-mt-6 flex flex-col gap-5">
                            <div class="border-b border-slate-200 pb-3 dark:border-slate-700">
                                <legend class="text-base font-semibold text-slate-900 dark:text-white">
                                    01 &middot; Identitas &amp; Data Demografi
                                </legend>
                                <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                                    Kolom bertanda <span class="font-bold text-rose-700 dark:text-rose-300">*</span> wajib dilengkapi.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div class="flex flex-col gap-1.5 sm:col-span-2">
                                    <label for="name" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Nama Lengkap / Nama Panggilan <span class="text-rose-700 dark:text-rose-300">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name') }}"
                                        x-model="name"
                                        autocomplete="name"
                                        required
                                        @error('name') aria-invalid="true" @enderror
                                        class="min-h-11 w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30 {{ $errors->has('name') ? 'border-rose-600 dark:border-rose-400' : 'border-slate-300 dark:border-slate-600' }}"
                                        placeholder="Contoh: Aisyah Putri"
                                    >
                                    @error('name')
                                    <p class="text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-col gap-1.5">
                                    <label for="initials" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Inisial Responden
                                    </label>
                                    <input
                                        type="text"
                                        id="initials"
                                        name="initials"
                                        value="{{ old('initials') }}"
                                        x-model="initials"
                                        maxlength="10"
                                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30"
                                        :placeholder="autoInitials ? 'Otomatis: ' + autoInitials : 'Contoh: AP'"
                                        placeholder="Contoh: AP"
                                    >
                                    <p class="text-[11px] text-slate-600 dark:text-slate-300">Opsional, dibuat otomatis bila kosong.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
                                <div class="flex flex-col gap-1.5 sm:col-span-4">
                                    <label for="usia" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Usia (Tahun) <span class="text-rose-700 dark:text-rose-300">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="usia"
                                        name="usia"
                                        value="{{ old('usia', 18) }}"
                                        min="15"
                                        max="40"
                                        required
                                        @error('usia') aria-invalid="true" @enderror
                                        class="min-h-11 w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30 {{ $errors->has('usia') ? 'border-rose-600 dark:border-rose-400' : 'border-slate-300 dark:border-slate-600' }}"
                                        placeholder="18"
                                    >
                                    @error('usia')
                                    <p class="text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-col gap-1.5 sm:col-span-8">
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Jenis Kelamin <span class="text-rose-700 dark:text-rose-300">*</span>
                                    </span>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label
                                            :class="gender === 'P'
                                                ? 'border-rose-600 bg-rose-50/70 text-slate-900 dark:border-rose-400 dark:bg-rose-950/40 dark:text-white'
                                                : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800'"
                                            class="flex min-h-11 cursor-pointer items-center gap-2.5 rounded-lg border px-3.5 py-2.5 text-sm font-medium transition-colors duration-150 focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-teal-700 dark:focus-within:outline-teal-400"
                                        >
                                            <input
                                                type="radio"
                                                name="gender"
                                                value="P"
                                                x-model="gender"
                                                {{ old('gender', 'P') === 'P' ? 'checked' : '' }}
                                                required
                                                class="h-4 w-4 border-slate-300 text-rose-600 dark:border-slate-500"
                                            >
                                            <span>Perempuan</span>
                                        </label>

                                        <label
                                            :class="gender === 'L'
                                                ? 'border-teal-700 bg-teal-50/70 text-slate-900 dark:border-teal-400 dark:bg-teal-950/40 dark:text-white'
                                                : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800'"
                                            class="flex min-h-11 cursor-pointer items-center gap-2.5 rounded-lg border px-3.5 py-2.5 text-sm font-medium transition-colors duration-150 focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-teal-700 dark:focus-within:outline-teal-400"
                                        >
                                            <input
                                                type="radio"
                                                name="gender"
                                                value="L"
                                                x-model="gender"
                                                {{ old('gender') === 'L' ? 'checked' : '' }}
                                                class="h-4 w-4 border-slate-300 text-teal-700 dark:border-slate-500"
                                            >
                                            <span>Laki-laki</span>
                                        </label>
                                    </div>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-300" aria-live="polite">
                                        <span x-show="gender === 'P'">Fitur Pelacak Siklus Haid dan Kalkulator IMT diaktifkan untuk responden perempuan.</span>
                                        <span x-cloak x-show="gender === 'L'">Fitur Kalkulator IMT, modul kesehatan reproduksi, dan konsultasi diaktifkan pada akun Anda.</span>
                                    </p>
                                </div>
                            </div>

                            <x-profile-input name="menarche_age" label="Usia saat haid pertama (tahun)" type="number" min="8" max="25" />

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="flex flex-col gap-1.5">
                                    <label for="agama" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Agama <span class="text-rose-700 dark:text-rose-300">*</span>
                                    </label>
                                    <select
                                        id="agama"
                                        name="agama"
                                        required
                                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:focus:border-teal-400 dark:focus:outline-teal-400/30"
                                    >
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

                                <div class="flex flex-col gap-1.5">
                                    <label for="pendidikan_terakhir" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Pendidikan Terakhir <span class="text-rose-700 dark:text-rose-300">*</span>
                                    </label>
                                    <select
                                        id="pendidikan_terakhir"
                                        name="pendidikan_terakhir"
                                        required
                                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:focus:border-teal-400 dark:focus:outline-teal-400/30"
                                    >
                                        <option value="SMA/SMK/MA/Sederajat" {{ old('pendidikan_terakhir', 'SMA/SMK/MA/Sederajat') === 'SMA/SMK/MA/Sederajat' ? 'selected' : '' }}>SMA / SMK / MA / Sederajat</option>
                                        <option value="Diploma (D3/D4)" {{ old('pendidikan_terakhir') === 'Diploma (D3/D4)' ? 'selected' : '' }}>Diploma (D3/D4)</option>
                                        <option value="Sarjana (S1)" {{ old('pendidikan_terakhir') === 'Sarjana (S1)' ? 'selected' : '' }}>Sarjana (S1)</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        {{-- BAGIAN 02: AFILIASI AKADEMIK --}}
                        <fieldset id="bagian-akademik" class="scroll-mt-6 flex flex-col gap-5">
                            <div class="border-b border-slate-200 pb-3 dark:border-slate-700">
                                <legend class="text-base font-semibold text-slate-900 dark:text-white">
                                    02 &middot; Afiliasi Akademik Universitas Bengkulu
                                </legend>
                                <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                                    Data program studi dan fakultas digunakan untuk agregasi partisipasi kelas bimbingan.
                                </p>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="fakultas" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                    Fakultas
                                </label>
                                <select
                                    id="fakultas"
                                    name="fakultas"
                                    class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:focus:border-teal-400 dark:focus:outline-teal-400/30"
                                >
                                    @foreach($daftarFakultas as $fakultasItem)
                                    <option value="{{ $fakultasItem }}" {{ $selectedFakultas === $fakultasItem ? 'selected' : '' }}>
                                        {{ $fakultasItem }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
                                <div class="flex flex-col gap-1.5 sm:col-span-7">
                                    <label for="prodi" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Program Studi <span class="text-rose-700 dark:text-rose-300">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="prodi"
                                        name="prodi"
                                        value="{{ old('prodi') }}"
                                        required
                                        @error('prodi') aria-invalid="true" @enderror
                                        class="min-h-11 w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30 {{ $errors->has('prodi') ? 'border-rose-600 dark:border-rose-400' : 'border-slate-300 dark:border-slate-600' }}"
                                        placeholder="Contoh: Pendidikan Biologi / Informatika"
                                    >
                                    @error('prodi')
                                    <p class="text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-col gap-1.5 sm:col-span-5">
                                    <label for="nim" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        NIM Mahasiswa <span class="font-normal text-slate-600 dark:text-slate-300">(Opsional)</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="nim"
                                        name="nim"
                                        value="{{ old('nim') }}"
                                        maxlength="20"
                                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30"
                                        placeholder="Contoh: A1D026001"
                                    >
                                </div>
                            </div>
                        </fieldset>

                        {{-- BAGIAN 03: KREDENSIAL AKUN --}}
                        <fieldset id="bagian-akun" class="scroll-mt-6 flex flex-col gap-5">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-3 dark:border-slate-700">
                                <div>
                                    <legend class="text-base font-semibold text-slate-900 dark:text-white">
                                        03 &middot; Kredensial Akun SIKERA
                                    </legend>
                                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                                        Gunakan alamat email aktif untuk masuk kembali ke akun Anda.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    :aria-pressed="showPassword.toString()"
                                    class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
                                >
                                    <svg x-show="!showPassword" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg x-cloak x-show="showPassword" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.83M9.88 5.09A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-5.41 1.499a9.98 9.98 0 01-6.588-2.91A10.05 10.05 0 012.458 12a9.96 9.96 0 013.362-4.78" />
                                    </svg>
                                    <span x-text="showPassword ? 'Sembunyikan Sandi' : 'Tampilkan Sandi'">Tampilkan Sandi</span>
                                </button>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="email" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                    Alamat Email <span class="text-rose-700 dark:text-rose-300">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    required
                                    @error('email') aria-invalid="true" @enderror
                                    class="min-h-11 w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30 {{ $errors->has('email') ? 'border-rose-600 dark:border-rose-400' : 'border-slate-300 dark:border-slate-600' }}"
                                    placeholder="nama@unib.ac.id"
                                >
                                @error('email')
                                <p class="text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="flex flex-col gap-1.5">
                                    <label for="password" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Kata Sandi <span class="text-rose-700 dark:text-rose-300">*</span>
                                    </label>
                                    <input
                                        :type="showPassword ? 'text' : 'password'"
                                        type="password"
                                        id="password"
                                        name="password"
                                        autocomplete="new-password"
                                        required
                                        minlength="6"
                                        @error('password') aria-invalid="true" @enderror
                                        class="min-h-11 w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30 {{ $errors->has('password') ? 'border-rose-600 dark:border-rose-400' : 'border-slate-300 dark:border-slate-600' }}"
                                        placeholder="Minimal 6 karakter"
                                    >
                                    @error('password')
                                    <p class="text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-col gap-1.5">
                                    <label for="password_confirmation" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Konfirmasi Kata Sandi <span class="text-rose-700 dark:text-rose-300">*</span>
                                    </label>
                                    <input
                                        :type="showPassword ? 'text' : 'password'"
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        required
                                        minlength="6"
                                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30"
                                        placeholder="Ketik ulang kata sandi"
                                    >
                                </div>
                            </div>
                        </fieldset>

                        <div class="flex flex-col gap-3 border-t border-slate-200 pt-5 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs text-slate-600 dark:text-slate-300">
                                Setelah pendaftaran selesai, Anda langsung diarahkan ke Menu Utama Mahasiswa.
                            </p>

                            <button
                                type="submit"
                                :disabled="submitting"
                                class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-lg bg-teal-700 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 disabled:cursor-not-allowed disabled:opacity-75 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                            >
                                <span x-show="!submitting">Simpan Biodata &amp; Buat Akun</span>
                                <span x-cloak x-show="submitting" class="inline-flex items-center gap-2">
                                    <span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" aria-hidden="true"></span>
                                    <span>Menyimpan pendaftaran...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
