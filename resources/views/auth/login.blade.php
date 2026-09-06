@extends('layouts.auth')
@section('title', 'Halaman Masuk - SIKERA')

@section('content')
<div class="w-full max-w-md">
    <div class="mb-6 text-center">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500 text-white font-bold text-xl shadow-theme-xs mb-3">
            S
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">SIKERA</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Sistem Informasi Kesehatan Reproduksi Remaja</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03] sm:p-7">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Masuk ke Akun</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Masukkan email dan kata sandi Anda. Sistem akan mengarahkan ke dashboard secara otomatis.</p>
        </div>

        @if(session('success'))
        <div class="mb-4 rounded-lg bg-success-50 p-3 text-xs font-semibold text-success-700 dark:bg-success-500/10 dark:text-success-400">
            {{ session('success') }}
        </div>
        @endif

        @if(session('info'))
        <div class="mb-4 rounded-lg bg-brand-50 p-3 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
            {{ session('info') }}
        </div>
        @endif

        @if(session('warning'))
        <div class="mb-4 rounded-lg bg-warning-50 p-3 text-xs font-semibold text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
            {{ session('warning') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 rounded-lg bg-error-50 p-3 text-xs font-semibold text-error-700 dark:bg-error-500/10 dark:text-error-400">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                    placeholder="nama@unib.ac.id"
                >
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Kata Sandi</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white"
                    placeholder="Masukkan kata sandi"
                >
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-gray-600 dark:text-gray-400">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition shadow-theme-xs">
                Masuk
            </button>
        </form>

        {{-- Quick Demo Login Helper --}}
        <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800">
            <p class="text-[11px] text-gray-400 dark:text-gray-500 text-center mb-2 font-medium">Akun Demo Cepat (Klik untuk Mengisi)</p>
            <div class="grid grid-cols-3 gap-1.5" x-data>
                <button
                    type="button"
                    @click="document.getElementById('email').value = 'mhs@unib.ac.id'; document.getElementById('password').value = 'password';"
                    class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2 text-[10px] font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-center"
                >
                    Mahasiswa
                </button>
                <button
                    type="button"
                    @click="document.getElementById('email').value = 'dosen@unib.ac.id'; document.getElementById('password').value = 'password';"
                    class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2 text-[10px] font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-center"
                >
                    Dosen PA
                </button>
                <button
                    type="button"
                    @click="document.getElementById('email').value = 'admin@sikera.id'; document.getElementById('password').value = 'password';"
                    class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2 text-[10px] font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-center"
                >
                    Super Admin
                </button>
            </div>
        </div>

        <div class="mt-5 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Mahasiswa baru belum memiliki akun?
                <a href="{{ route('register') }}" class="font-semibold text-brand-500 hover:text-brand-600 dark:text-brand-400">Isi Formulir Biodata</a>
            </p>
        </div>
    </div>

    <p class="mt-5 text-center text-xs text-gray-400 dark:text-gray-500">Universitas Bengkulu &middot; Riset Evaluasi Kesehatan Reproduksi Remaja</p>
</div>
@endsection
