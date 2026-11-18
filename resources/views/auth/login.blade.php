@extends('layouts.auth')
@section('title', 'Masuk ke Portal')

@section('content')
<div
    class="w-full"
    x-data="{
        email: @js(old('email', '')),
        password: '',
        showPassword: false,
        submitting: false,
        selectedDemo: '',
        fillDemo(role, demoEmail) {
            this.email = demoEmail;
            this.password = 'password';
            this.selectedDemo = role;
        }
    }"
>
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-8">
        {{-- Kolom Utama: Kartu Masuk (Fokus Utama Layar, tampil pertama di perangkat seluler) --}}
        <section aria-labelledby="login-heading" class="order-1 lg:order-2 lg:col-span-7">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex h-1.5 w-full" aria-hidden="true">
                    <span class="w-20 bg-rose-600 dark:bg-rose-500"></span>
                    <span class="grow bg-teal-700 dark:bg-teal-500"></span>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="mb-6">
                        <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">Portal Autentikasi Terpadu</p>
                        <h1 id="login-heading" class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                            Masuk ke Portal SIKERA
                        </h1>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                            Masukkan alamat email dan kata sandi akun Anda. Sistem mengarahkan ke ruang kerja sesuai peran secara otomatis.
                        </p>
                    </div>

                    @if(session('success'))
                    <div role="status" class="mb-5 rounded-lg border border-teal-300 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-900 dark:border-teal-700 dark:bg-teal-950/80 dark:text-teal-100">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('info'))
                    <div role="status" class="mb-5 rounded-lg border border-slate-300 bg-slate-100 px-4 py-3 text-sm font-medium text-slate-800 dark:border-slate-600 dark:bg-slate-700/70 dark:text-slate-100">
                        {{ session('info') }}
                    </div>
                    @endif

                    @if(session('warning'))
                    <div role="alert" class="mb-5 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900 dark:border-amber-700 dark:bg-amber-950/80 dark:text-amber-100">
                        {{ session('warning') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div role="alert" class="mb-5 rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-900 dark:border-rose-700 dark:bg-rose-950/80 dark:text-rose-100">
                        <ul class="flex flex-col gap-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" @submit="submitting = true" class="flex flex-col gap-5">
                        @csrf

                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                Alamat Email
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                x-model="email"
                                autocomplete="email"
                                required
                                autofocus
                                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                                class="min-h-11 w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30 {{ $errors->has('email') ? 'border-rose-600 dark:border-rose-400' : 'border-slate-300 dark:border-slate-600' }}"
                                placeholder="nama@unib.ac.id"
                            >
                            @error('email')
                            <p id="email-error" class="text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="password" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                Kata Sandi
                            </label>
                            <div class="relative flex items-center">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    type="password"
                                    id="password"
                                    name="password"
                                    x-model="password"
                                    autocomplete="current-password"
                                    required
                                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                                    class="min-h-11 w-full rounded-lg border bg-white py-2.5 pr-12 pl-3.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-teal-700 focus:outline-2 focus:outline-offset-0 focus:outline-teal-700/30 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:outline-teal-400/30 {{ $errors->has('password') ? 'border-rose-600 dark:border-rose-400' : 'border-slate-300 dark:border-slate-600' }}"
                                    placeholder="Masukkan kata sandi"
                                >
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    :aria-pressed="showPassword.toString()"
                                    :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                    class="absolute right-1 inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg text-slate-600 transition-colors hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-teal-700 dark:text-slate-300 dark:hover:text-white dark:focus-visible:outline-teal-400"
                                >
                                    <svg x-show="!showPassword" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg x-cloak x-show="showPassword" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.83M9.88 5.09A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-5.41 1.499a9.98 9.98 0 01-6.588-2.91A10.05 10.05 0 012.458 12a9.96 9.96 0 013.362-4.78" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                            <p id="password-error" class="text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="remember" class="inline-flex min-h-11 cursor-pointer items-center gap-2.5 pr-2 text-sm text-slate-700 dark:text-slate-200">
                                <input
                                    type="checkbox"
                                    id="remember"
                                    name="remember"
                                    {{ old('remember') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-slate-300 text-teal-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-teal-500 dark:focus-visible:outline-teal-400"
                                >
                                <span>Ingat sesi pada perangkat ini</span>
                            </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="submitting"
                            class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 disabled:cursor-not-allowed disabled:opacity-75 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                        >
                            <span x-show="!submitting">Masuk ke Portal SIKERA</span>
                            <span x-cloak x-show="submitting" class="inline-flex items-center gap-2">
                                <span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" aria-hidden="true"></span>
                                <span>Memverifikasi kredensial...</span>
                            </span>
                        </button>
                    </form>

                    {{-- Pengisi Cepat Akun Pengujian Riset --}}
                    <div class="mt-6 border-t border-slate-200 pt-5 dark:border-slate-700">
                        <div class="mb-2.5 flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                Akun Pengujian Demo (Isi Otomatis)
                            </p>
                            <span
                                x-cloak
                                x-show="selectedDemo !== ''"
                                class="text-xs font-medium text-teal-700 dark:text-teal-300"
                                aria-live="polite"
                            >
                                Kredensial siap dikirim
                            </span>
                        </div>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                            <button
                                type="button"
                                @click="fillDemo('mahasiswa', 'mhs@unib.ac.id')"
                                :class="selectedDemo === 'mahasiswa'
                                    ? 'border-teal-700 bg-teal-50 text-teal-900 dark:border-teal-400 dark:bg-teal-950/70 dark:text-teal-100'
                                    : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-700/70'"
                                class="flex min-h-11 flex-col items-start justify-center rounded-lg border px-3 py-2 text-left transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:focus-visible:outline-teal-400"
                            >
                                <span class="text-xs font-semibold">Mahasiswa</span>
                                <span class="text-[11px] text-slate-600 dark:text-slate-300">mhs@unib.ac.id</span>
                            </button>

                            <button
                                type="button"
                                @click="fillDemo('dosen', 'dosen@unib.ac.id')"
                                :class="selectedDemo === 'dosen'
                                    ? 'border-teal-700 bg-teal-50 text-teal-900 dark:border-teal-400 dark:bg-teal-950/70 dark:text-teal-100'
                                    : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-700/70'"
                                class="flex min-h-11 flex-col items-start justify-center rounded-lg border px-3 py-2 text-left transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:focus-visible:outline-teal-400"
                            >
                                <span class="text-xs font-semibold">Dosen PA</span>
                                <span class="text-[11px] text-slate-600 dark:text-slate-300">dosen@unib.ac.id</span>
                            </button>

                            <button
                                type="button"
                                @click="fillDemo('admin', 'admin@sikera.id')"
                                :class="selectedDemo === 'admin'
                                    ? 'border-teal-700 bg-teal-50 text-teal-900 dark:border-teal-400 dark:bg-teal-950/70 dark:text-teal-100'
                                    : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-700/70'"
                                class="flex min-h-11 flex-col items-start justify-center rounded-lg border px-3 py-2 text-left transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:focus-visible:outline-teal-400"
                            >
                                <span class="text-xs font-semibold">Super Admin</span>
                                <span class="text-[11px] text-slate-600 dark:text-slate-300">admin@sikera.id</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-2 rounded-lg bg-slate-50 px-4 py-3 text-xs text-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                        <span>Mahasiswa baru belum terdaftar sebagai responden?</span>
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex min-h-11 items-center rounded-lg px-2 font-semibold text-teal-700 underline decoration-teal-700/40 underline-offset-4 transition-colors hover:text-teal-800 hover:decoration-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-teal-300 dark:decoration-teal-300/40 dark:hover:text-teal-200 dark:focus-visible:outline-teal-400"
                        >
                            Isi Formulir Biodata Mahasiswa
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Kolom Konteks Editorial & Panduan Peran (Kiri di layar lebar, bawah di seluler) --}}
        <aside aria-label="Informasi Platform SIKERA" class="order-2 flex flex-col gap-5 lg:order-1 lg:col-span-5">
            <div class="rounded-xl bg-teal-900 p-6 text-white shadow-sm dark:border dark:border-slate-700 dark:bg-slate-800 sm:p-7">
                <p class="inline-flex items-center gap-2 text-xs font-semibold text-amber-300">
                    <span class="h-2 w-2 rounded-xs bg-amber-400" aria-hidden="true"></span>
                    <span>Edukasi Ilmiah &amp; Instrumen Riset</span>
                </p>
                <h2 class="mt-2.5 text-lg font-semibold leading-snug text-white sm:text-xl">
                    Ruang belajar kesehatan reproduksi yang empatik, terstruktur, dan berbasis bukti ilmiah.
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-teal-50 dark:text-slate-300">
                    Dirancang khusus bagi sivitas akademika Universitas Bengkulu untuk mendampingi literasi kesehatan reproduksi remaja melalui materi bertahap, pemantauan kesehatan mandiri, dan diskusi konseling yang terjaga kerahasiaannya.
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Cakupan Akses Berdasarkan Peran
                </h3>
                <dl class="mt-4 flex flex-col gap-4">
                    <div class="border-b border-slate-100 pb-3.5 dark:border-slate-700/70">
                        <dt class="text-xs font-semibold text-teal-700 dark:text-teal-300">Mahasiswa Responden</dt>
                        <dd class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                            Modul materi dengan evaluasi Pre-Test dan Post-Test, pelacak siklus haid dan kalkulator IMT, serta forum tanya jawab anonim.
                        </dd>
                    </div>
                    <div class="border-b border-slate-100 pb-3.5 dark:border-slate-700/70">
                        <dt class="text-xs font-semibold text-slate-800 dark:text-slate-100">Dosen Pembimbing Akademik &amp; Konselor</dt>
                        <dd class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                            Pemantauan partisipasi kelas bimbingan per fakultas, rujukan materi konseling, dan tanggapan diskusi mahasiswa.
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-slate-800 dark:text-slate-100">Pengelola Riset (Super Admin)</dt>
                        <dd class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                            Pengelolaan modul, kurasi poster edukasi Kaspro, serta rekapitulasi skor evaluasi dan indeks N-Gain responden.
                        </dd>
                    </div>
                </dl>
            </div>
        </aside>
    </div>
</div>
@endsection
