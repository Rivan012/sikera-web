@extends('layouts.admin')
@section('title', 'Edit profil')
@section('page-title', 'Edit profil lengkap')

@section('content')
@php($mahasiswa = $user->isMahasiswa())
<form method="POST" action="{{ route('profile.update') }}" class="mx-auto max-w-3xl space-y-6 rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
    @csrf
    @method('PUT')

    <fieldset>
        <legend class="text-lg font-bold">Identitas akun</legend>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Kolom bertanda * wajib diisi.</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <x-profile-input name="name" label="Nama lengkap" :value="$user->name" required maxlength="255" autocomplete="name" />
            <x-profile-input name="initials" label="Inisial responden" :value="$user->initials" maxlength="50" />
            @if($user->google_id !== null)
                <x-profile-input name="email" label="Email" type="email" :value="$user->email" required readonly
                    hint="Email dan foto profil mengikuti akun Google Anda." />
            @else
                <x-profile-input name="email" label="Email" type="email" :value="$user->email" required maxlength="255" autocomplete="email" />
                <x-profile-input name="current_password" label="Kata sandi saat ini" type="password" autocomplete="current-password"
                    hint="Isi hanya jika Anda mengubah email." />
                <x-profile-input name="avatar" label="Tautan foto profil" type="url" :value="$user->avatar" maxlength="2048"
                    hint="Gunakan tautan HTTPS. Kosongkan untuk menampilkan inisial." />
            @endif
        </div>
    </fieldset>

    <fieldset class="border-t border-slate-200 dark:border-slate-700">
        <legend class="pr-3 text-lg font-bold">Biodata pribadi</legend>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <x-profile-input name="usia" label="Usia (tahun)" type="number" :value="$user->usia" :required="$mahasiswa" :min="$mahasiswa ? 15 : 10" :max="$mahasiswa ? 40 : 100" />
            <x-profile-input name="gender" label="Jenis kelamin" :value="$user->gender" :required="$mahasiswa" :options="['P' => 'Perempuan', 'L' => 'Laki-laki']" />
            <x-profile-input name="agama" label="Agama" :value="$user->agama" :required="$mahasiswa" maxlength="255" />
            <x-profile-input name="menarche_age" label="Usia saat haid pertama (tahun)" type="number" :value="$user->menarche_age" min="8" max="25" />
        </div>
    </fieldset>

    <fieldset class="border-t border-slate-200 dark:border-slate-700">
        <legend class="pr-3 text-lg font-bold">Data akademik</legend>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <x-profile-input name="nim" label="Nomor induk mahasiswa (NIM)" :value="$user->nim" maxlength="50" />
            <x-profile-input name="fakultas" label="Fakultas" :value="$user->fakultas" maxlength="255" />
            <x-profile-input name="prodi" label="Program studi" :value="$user->prodi" :required="$mahasiswa" maxlength="255" />
            <x-profile-input name="pendidikan_terakhir" label="Pendidikan terakhir" :value="$user->pendidikan_terakhir" :required="$mahasiswa" maxlength="255" />
        </div>
    </fieldset>

    <div class="flex flex-wrap items-center gap-4">
        <button type="submit" class="min-h-11 rounded-lg bg-teal-800 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-300 dark:text-slate-950 dark:hover:bg-teal-200 dark:focus-visible:outline-teal-400">Simpan perubahan</button>
        <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center rounded px-2 text-sm font-medium underline focus-visible:outline-2">Batal</a>
    </div>
</form>
@endsection
