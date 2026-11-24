@extends('layouts.auth')
@section('title', 'Lengkapi Formulir Biodata')

@section('content')
<form method="POST" action="{{ route('biodata.save') }}" class="w-full max-w-3xl space-y-6 rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800 sm:p-6">
    @csrf
    <div>
        <h1 class="text-2xl font-bold">Lengkapi biodata mahasiswa</h1>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Isi kolom bertanda * sebelum memulai pre-test.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <x-profile-input name="name" label="Nama lengkap" :value="$user->name" required maxlength="255" autocomplete="name" />
        <x-profile-input name="initials" label="Inisial responden" :value="$user->initials" maxlength="50" />
    </div>

    <x-biodata-fields :user="$user" :mahasiswa="true" />

    <button type="submit" class="min-h-11 rounded-lg bg-teal-800 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-300 dark:text-slate-950 dark:hover:bg-teal-200">Simpan biodata</button>
</form>
@endsection
