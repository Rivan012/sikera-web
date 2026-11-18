<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKERA | Publikasi Edukasi &amp; Instrumen Riset Kesehatan Reproduksi Universitas Bengkulu</title>
    <meta name="description" content="Publikasi edukasi ilmiah kesehatan reproduksi remaja, referensi klinis menstruasi FIGO, galeri poster Kaspro, dan instrumen evaluasi kognitif Pre-Test serta Post-Test mahasiswa Universitas Bengkulu.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body
    x-data="{
        darkMode: false,
        mobileMenuOpen: false
    }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode')) === true;
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
    "
    @keydown.escape.window="mobileMenuOpen = false"
    :class="{ 'dark bg-night': darkMode === true }"
    class="font-jakarta bg-paper text-ink antialiased"
>
    <a
        href="#konten-utama"
        class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-pine-700 focus:px-4 focus:py-2.5 focus:text-sm focus:font-semibold focus:text-white focus:outline-2 focus:outline-offset-2 focus:outline-clay-600 dark:focus:bg-pine-400 dark:focus:text-pine-950"
    >
        Lewati ke konten utama
    </a>

    <div class="flex min-h-screen flex-col bg-paper text-ink transition-colors duration-150 dark:bg-night dark:text-night-ink">
        <header class="sticky top-0 z-40 border-b border-rule bg-paper dark:border-night-rule dark:bg-night">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-2 px-4 py-2.5 sm:px-6 lg:px-8">
                <a
                    href="#beranda"
                    class="inline-flex min-h-11 items-center gap-3 rounded-lg px-1.5 py-1 transition-colors hover:bg-parchment active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:hover:bg-night-surface dark:focus-visible:outline-pine-300"
                >
                    <span class="flex h-8 w-1.5 shrink-0 flex-col overflow-hidden rounded-xs" aria-hidden="true">
                        <span class="h-5 w-full bg-pine-700 dark:bg-pine-400"></span>
                        <span class="h-3 w-full bg-clay-600 dark:bg-clay-300"></span>
                    </span>
                    <span class="flex flex-col">
                        <span class="font-editorial text-lg font-bold leading-none tracking-tight text-ink dark:text-night-ink">SIKERA</span>
                        <span class="mt-0.5 text-[11px] font-medium text-ink-body dark:text-night-body">Universitas Bengkulu</span>
                    </span>
                </a>

                <nav aria-label="Navigasi utama" class="hidden items-center gap-1 lg:flex">
                    <a
                        href="#kurikulum"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-xs font-semibold text-ink-body transition-colors hover:bg-parchment hover:text-ink active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-body dark:hover:bg-night-surface dark:hover:text-night-ink dark:focus-visible:outline-pine-300"
                    >
                        Silabus Modul
                    </a>
                    <a
                        href="#spektrum-klinis"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-xs font-semibold text-ink-body transition-colors hover:bg-parchment hover:text-ink active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-body dark:hover:bg-night-surface dark:hover:text-night-ink dark:focus-visible:outline-pine-300"
                    >
                        Spektrum Klinis &amp; Kasus
                    </a>
                    <a
                        href="#kaspro"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-xs font-semibold text-ink-body transition-colors hover:bg-parchment hover:text-ink active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-body dark:hover:bg-night-surface dark:hover:text-night-ink dark:focus-visible:outline-pine-300"
                    >
                        Galeri Kaspro
                    </a>
                    <a
                        href="#rujukan"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-xs font-semibold text-ink-body transition-colors hover:bg-parchment hover:text-ink active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-body dark:hover:bg-night-surface dark:hover:text-night-ink dark:focus-visible:outline-pine-300"
                    >
                        Direktori Rujukan
                    </a>
                </nav>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="darkMode = !darkMode"
                        :aria-pressed="darkMode.toString()"
                        :aria-label="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
                        class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-control-border bg-white px-3 py-2 text-xs font-semibold text-ink transition-colors hover:bg-parchment active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                    >
                        <span x-show="!darkMode" class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 shrink-0 fill-current text-pine-700" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                            </svg>
                            <span class="hidden sm:inline">Mode Gelap</span>
                        </span>
                        <span x-cloak x-show="darkMode" class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 shrink-0 fill-current text-ochre-300" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 3a1 1 0 011 1v1a1 1 0 11-2 0V4a1 1 0 011-1zm0 15a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm9-8a1 1 0 110 2h-1a1 1 0 110-2h1zM5 11a1 1 0 110 2H4a1 1 0 110-2h1zm14.071-5.071a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM7.05 16.95a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zm12.021.707a1 1 0 01-1.414 0l-.707-.707a1 1 0 111.414-1.414l.707.707a1 1 0 010 1.414zM7.05 7.05a1 1 0 01-1.414 0l-.707-.707A1 1 0 116.343 4.93l.707.707a1 1 0 010 1.414zM12 8a4 4 0 100 8 4 4 0 000-8z"/>
                            </svg>
                            <span class="hidden sm:inline">Mode Terang</span>
                        </span>
                    </button>

                    <div class="hidden items-center gap-2 md:flex">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="inline-flex min-h-11 items-center rounded-lg bg-pine-700 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-pine-900 active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                            >
                                Buka Ruang Kerja
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex min-h-11 items-center rounded-lg border border-control-border bg-white px-3.5 py-2 text-xs font-semibold text-ink transition-colors hover:bg-parchment active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                            >
                                Masuk Portal
                            </a>
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex min-h-11 items-center rounded-lg bg-pine-700 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-pine-900 active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                            >
                                Daftar Responden
                            </a>
                        @endauth
                    </div>

                    <button
                        type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        :aria-expanded="mobileMenuOpen.toString()"
                        aria-controls="mobile-nav"
                        class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-control-border bg-white px-3 py-2 text-xs font-semibold text-ink transition-colors hover:bg-parchment active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300 lg:hidden"
                    >
                        <svg x-show="!mobileMenuOpen" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                        <svg x-cloak x-show="mobileMenuOpen" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12"/>
                        </svg>
                        <span x-text="mobileMenuOpen ? 'Tutup' : 'Menu'">Menu</span>
                    </button>
                </div>
            </div>

            <div
                id="mobile-nav"
                x-cloak
                x-show="mobileMenuOpen"
                class="border-t border-rule bg-white px-4 py-4 dark:border-night-rule dark:bg-night-surface lg:hidden"
            >
                <nav aria-label="Navigasi seluler" class="mx-auto flex max-w-6xl flex-col gap-1">
                    <a
                        href="#kurikulum"
                        @click="mobileMenuOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-sm font-semibold text-ink hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                    >
                        Silabus Modul Pembelajaran
                    </a>
                    <a
                        href="#spektrum-klinis"
                        @click="mobileMenuOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-sm font-semibold text-ink hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                    >
                        Spektrum Klinis FIGO &amp; Bedah Kasus
                    </a>
                    <a
                        href="#kaspro"
                        @click="mobileMenuOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-sm font-semibold text-ink hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                    >
                        Galeri Poster Kaspro
                    </a>
                    <a
                        href="#rujukan"
                        @click="mobileMenuOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-sm font-semibold text-ink hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                    >
                        Direktori Rujukan Bengkulu
                    </a>

                    <div class="mt-2 grid grid-cols-1 gap-2 border-t border-rule pt-3 dark:border-night-rule sm:grid-cols-2 md:hidden">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300"
                            >
                                Buka Ruang Kerja
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg border border-control-border bg-white px-4 py-2.5 text-sm font-semibold text-ink hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:bg-night-raised dark:text-night-ink"
                            >
                                Masuk ke Portal SIKERA
                            </a>
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300"
                            >
                                Isi Biodata Responden
                            </a>
                        @endauth
                    </div>
                </nav>
            </div>
        </header>

        <main id="konten-utama" tabindex="-1" class="flex-1 focus:outline-none">
            @if($dataLoadError)
                <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6 lg:px-8">
                    <div role="alert" class="flex flex-col justify-between gap-4 rounded-xl border border-clay-600 bg-clay-50 p-5 text-clay-700 dark:border-clay-300 dark:bg-clay-950 dark:text-clay-200 sm:flex-row sm:items-center">
                        <div>
                            <p class="text-sm font-bold">Gagal memuat sebagian data basis data SIKERA</p>
                            <p class="mt-1 text-xs leading-relaxed">
                                Koneksi ke basis data kurikulum sedang mengalami gangguan. Referensi klinis tetap dapat dibaca di bawah, atau silakan muat ulang halaman dan masuk langsung ke portal.
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            <a
                                href="{{ route('home') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg border border-clay-700 bg-white px-4 py-2 text-xs font-semibold text-clay-700 hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-clay-700 dark:border-clay-200 dark:bg-night-surface dark:text-clay-200"
                            >
                                Muat Ulang Halaman
                            </a>
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-pine-700 px-4 py-2 text-xs font-semibold text-white hover:bg-pine-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950"
                            >
                                Masuk ke Portal
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <section id="beranda" class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8 lg:py-16">
                <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12 lg:gap-10">
                    <div class="lg:col-span-7">
                        <h1 class="font-editorial text-[1.9rem] font-semibold leading-[1.16] tracking-tight text-ink dark:text-night-ink sm:text-4xl lg:text-[2.75rem]">
                            Literasi ilmiah kesehatan reproduksi remaja dan evaluasi kognitif terukur bagi mahasiswa.
                        </h1>

                        <p class="mt-4 max-w-2xl text-sm leading-relaxed text-ink-body dark:text-night-body sm:text-base">
                            Sistem Informasi Kesehatan Reproduksi Remaja (SIKERA) Universitas Bengkulu menyajikan kurikulum anatomi, fisiologi haid, batasan relasi sehat, serta perlindungan hukum kampus secara ilmiah tanpa vulgaritas, terintegrasi dengan pengukuran Pre-Test dan Post-Test berbasis indeks N-Gain.
                        </p>

                        <div
                            x-data="{ loadingAction: '' }"
                            class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center"
                        >
                            @auth
                                <a
                                    href="{{ url('/dashboard') }}"
                                    @click="loadingAction = 'dashboard'"
                                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-pine-900 active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                                >
                                    <span x-show="loadingAction !== 'dashboard'">Buka Ruang Kerja SIKERA</span>
                                    <span x-cloak x-show="loadingAction === 'dashboard'">Membuka ruang kerja...</span>
                                </a>
                            @else
                                <a
                                    href="{{ route('register') }}"
                                    @click="loadingAction = 'register'"
                                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-pine-900 active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                                >
                                    <span x-show="loadingAction !== 'register'">Isi Biodata Responden Baru</span>
                                    <span x-cloak x-show="loadingAction === 'register'">Membuka formulir biodata...</span>
                                </a>
                                <a
                                    href="{{ route('login') }}"
                                    @click="loadingAction = 'login'"
                                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-control-border bg-white px-5 py-2.5 text-sm font-semibold text-ink transition-colors hover:bg-parchment active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                                >
                                    <span x-show="loadingAction !== 'login'">Masuk ke Portal SIKERA</span>
                                    <span x-cloak x-show="loadingAction === 'login'">Membuka halaman masuk...</span>
                                </a>
                            @endauth

                            <a
                                href="#kurikulum"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg px-3 py-2.5 text-sm font-semibold text-pine-700 underline decoration-pine-700/50 underline-offset-4 transition-colors hover:text-pine-900 active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-pine-300 dark:decoration-pine-300/50 dark:hover:text-pine-200 dark:focus-visible:outline-pine-300"
                            >
                                Baca Silabus Kurikulum
                            </a>
                        </div>

                        <dl class="mt-8 grid grid-cols-1 gap-5 border-t border-rule pt-6 dark:border-night-rule sm:grid-cols-3">
                            <div>
                                <dt class="text-xs font-semibold text-pine-700 dark:text-pine-300">Mahasiswa Responden</dt>
                                <dd class="mt-1 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                    Mempelajari modul bertahap, mencatat siklus haid serta IMT mandiri, dan berdiskusi secara anonim.
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-pine-700 dark:text-pine-300">Dosen PA &amp; Konselor</dt>
                                <dd class="mt-1 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                    Memantau ketuntasan kelas bimbingan per fakultas dan menanggapi pertanyaan konseling mahasiswa.
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-pine-700 dark:text-pine-300">Tim Pengelola Riset</dt>
                                <dd class="mt-1 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                    Mengelola bank soal modul, kurasi poster Kaspro, dan ekspor data skor ke lembar kerja analisis.
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <aside aria-labelledby="protokol-heading" class="lg:col-span-5">
                        <div class="overflow-hidden rounded-xl border border-rule bg-white shadow-sm dark:border-night-rule dark:bg-night-surface">
                            <div class="flex h-1.5 w-full" aria-hidden="true">
                                <span class="w-24 bg-clay-600 dark:bg-clay-300"></span>
                                <span class="grow bg-pine-700 dark:bg-pine-400"></span>
                            </div>

                            <div class="p-5 sm:p-6">
                                <div class="flex items-baseline justify-between gap-2 border-b border-rule pb-3.5 dark:border-night-rule">
                                    <h2 id="protokol-heading" class="font-editorial text-lg font-semibold text-ink dark:text-night-ink">
                                        Protokol Intervensi &amp; Gating Riset
                                    </h2>
                                    @if(count($modules) > 0)
                                        <span class="text-xs font-semibold text-pine-700 dark:text-pine-300">
                                            {{ count($modules) }} Modul &middot; {{ $totalTopicsCount }} Topik
                                        </span>
                                    @endif
                                </div>

                                <ol class="mt-4 flex flex-col gap-3.5">
                                    <li class="flex items-start gap-3">
                                        <span class="font-editorial inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-pine-50 text-xs font-bold text-pine-700 dark:bg-pine-dark-badge dark:text-pine-200">01</span>
                                        <div>
                                            <p class="text-xs font-bold text-ink dark:text-night-ink">Registrasi Demografi &amp; Inisial</p>
                                            <p class="mt-0.5 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                Pencatatan afiliasi fakultas dan inisial responden agar kerahasiaan data evaluasi terjaga.
                                            </p>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="font-editorial inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-pine-50 text-xs font-bold text-pine-700 dark:bg-pine-dark-badge dark:text-pine-200">02</span>
                                        <div>
                                            <p class="text-xs font-bold text-ink dark:text-night-ink">Pre-Test Pembuka per Modul</p>
                                            <p class="mt-0.5 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                Materi terkunci sebelum mahasiswa menyelesaikan kuesioner pengetahuan awal pada modul terkait.
                                            </p>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="font-editorial inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-pine-50 text-xs font-bold text-pine-700 dark:bg-pine-dark-badge dark:text-pine-200">03</span>
                                        <div>
                                            <p class="text-xs font-bold text-ink dark:text-night-ink">Eksplorasi Materi Ilmiah &amp; Video</p>
                                            <p class="mt-0.5 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                Pembacaan submateri berurutan dilengkapi tayangan video edukasi dan panduan praktik mandiri.
                                            </p>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="font-editorial inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-clay-50 text-xs font-bold text-clay-700 dark:bg-clay-950 dark:text-clay-200">04</span>
                                        <div>
                                            <p class="text-xs font-bold text-ink dark:text-night-ink">Post-Test &amp; Kalkulasi Skor N-Gain</p>
                                            <p class="mt-0.5 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                Evaluasi akhir otomatis menghitung selisih peningkatan pemahaman kognitif tiap responden.
                                            </p>
                                        </div>
                                    </li>
                                </ol>

                                <div class="mt-5 rounded-lg border border-rule bg-ochre-50 px-4 py-3 dark:border-night-rule dark:bg-ochre-950">
                                    <p class="text-[11px] font-bold text-ochre-800 dark:text-ochre-300">
                                        Rumus Indeks Efektivitas N-Gain (Hake)
                                    </p>
                                    <p class="mt-1 text-xs font-semibold text-ink dark:text-night-ink">
                                        N-Gain = (Skor Post-Test &minus; Skor Pre-Test) / (100 &minus; Skor Pre-Test)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section
                id="kurikulum"
                class="scroll-mt-16 border-y border-rule bg-parchment py-10 dark:border-night-rule dark:bg-night-surface sm:py-14 lg:py-18"
            >
                <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <p class="text-xs font-semibold text-pine-700 dark:text-pine-300">Struktur Kurikulum Basis Data SIKERA</p>
                        <h2 class="font-editorial mt-1.5 text-2xl font-semibold tracking-tight text-ink dark:text-night-ink sm:text-3xl">
                            Silabus pembelajaran terstruktur dengan prasyarat evaluasi per modul.
                        </h2>
                        <p class="mt-2.5 text-sm leading-relaxed text-ink-body dark:text-night-body">
                            Pilih nomor modul di bawah untuk meninjau cakupan bahasan, daftar submateri, serta ketersediaan materi video sebelum masuk ke portal pembelajaran.
                        </p>
                    </div>

                    @if(count($modules) > 0)
                        <div
                            class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-8"
                            x-data="{
                                modules: @js($modules),
                                activeIdx: 0,
                                ready: false,
                                init() {
                                    this.ready = true;
                                },
                                selectTab(idx) {
                                    this.activeIdx = idx;
                                    this.$nextTick(() => {
                                        const btn = document.getElementById('tab-modul-' + idx);
                                        if (btn) {
                                            btn.focus();
                                        }
                                    });
                                },
                                nextTab() {
                                    this.selectTab((this.activeIdx + 1) % this.modules.length);
                                },
                                prevTab() {
                                    this.selectTab((this.activeIdx - 1 + this.modules.length) % this.modules.length);
                                }
                            }"
                        >
                            <div
                                role="tablist"
                                aria-label="Daftar modul pembelajaran SIKERA"
                                aria-orientation="vertical"
                                class="grid grid-cols-1 gap-2.5 md:grid-cols-2 lg:col-span-5 lg:grid-cols-1"
                            >
                                @foreach($modules as $idx => $mod)
                                    <button
                                        type="button"
                                        role="tab"
                                        id="tab-modul-{{ $idx }}"
                                        aria-controls="panel-modul-{{ $idx }}"
                                        :aria-selected="(activeIdx === {{ $idx }}).toString()"
                                        :tabindex="activeIdx === {{ $idx }} ? 0 : -1"
                                        @click="activeIdx = {{ $idx }}"
                                        @keydown.arrow-down.prevent="nextTab()"
                                        @keydown.arrow-right.prevent="nextTab()"
                                        @keydown.arrow-up.prevent="prevTab()"
                                        @keydown.arrow-left.prevent="prevTab()"
                                        @keydown.home.prevent="selectTab(0)"
                                        @keydown.end.prevent="selectTab(modules.length - 1)"
                                        :class="activeIdx === {{ $idx }}
                                            ? 'border-pine-700 bg-white text-ink shadow-sm dark:border-pine-300 dark:bg-night-raised dark:text-night-ink'
                                            : 'border-control-border/60 bg-paper/80 text-ink hover:border-control-border hover:bg-white dark:border-night-control/60 dark:bg-night dark:text-night-ink dark:hover:border-night-control dark:hover:bg-night-raised'"
                                        class="flex min-h-11 w-full items-start gap-3.5 rounded-xl border p-4 text-left transition-colors active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:focus-visible:outline-pine-300"
                                    >
                                        <span
                                            :class="activeIdx === {{ $idx }}
                                                ? 'bg-pine-700 text-white dark:bg-pine-400 dark:text-pine-950'
                                                : 'bg-parchment text-pine-700 border border-rule dark:bg-night-surface dark:text-pine-300 dark:border-night-rule'"
                                            class="font-editorial inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-sm font-bold"
                                        >
                                            {{ $mod['number'] }}
                                        </span>
                                        <span class="flex flex-1 flex-col">
                                            <span class="text-sm font-semibold leading-snug">{{ $mod['title'] }}</span>
                                            <span class="mt-1 text-xs font-medium text-ink-subtle dark:text-night-body">
                                                Estimasi {{ $mod['duration'] }} &middot; {{ count($mod['topics']) }} Submateri
                                            </span>
                                        </span>
                                    </button>
                                @endforeach
                            </div>

                            <div class="lg:col-span-7">
                                <div x-show="!ready" aria-busy="true" class="sr-only">Memuat rincian silabus modul...</div>

                                @foreach($modules as $idx => $mod)
                                    <div
                                        role="tabpanel"
                                        id="panel-modul-{{ $idx }}"
                                        aria-labelledby="tab-modul-{{ $idx }}"
                                        tabindex="0"
                                        @if($idx > 0) x-cloak @endif
                                        x-show="activeIdx === {{ $idx }}"
                                        class="overflow-hidden rounded-xl border border-rule bg-white shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-rule dark:bg-night dark:focus-visible:outline-pine-300"
                                    >
                                        <div class="flex h-1.5 w-full" aria-hidden="true">
                                            <span class="w-20 bg-pine-700 dark:bg-pine-400"></span>
                                            <span class="grow bg-clay-600 dark:bg-clay-300"></span>
                                        </div>

                                        <div class="p-5 sm:p-7">
                                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-rule pb-4 dark:border-night-rule">
                                                <span class="font-editorial text-sm font-bold text-pine-700 dark:text-pine-300">
                                                    Modul {{ $mod['number'] }}
                                                </span>
                                                <span class="rounded-md border border-rule bg-parchment px-2.5 py-1 text-xs font-semibold text-ink-body dark:border-night-rule dark:bg-night-surface dark:text-night-body">
                                                    Waktu baca {{ $mod['duration'] }}
                                                </span>
                                            </div>

                                            <h3 class="font-editorial mt-4 text-xl font-semibold text-ink dark:text-night-ink sm:text-2xl">
                                                {{ $mod['title'] }}
                                            </h3>
                                            @if($mod['subtitle'] !== '')
                                                <p class="mt-1.5 text-sm font-semibold text-ink-body dark:text-night-ink">
                                                    {{ $mod['subtitle'] }}
                                                </p>
                                            @endif
                                            <p class="mt-2.5 text-sm leading-relaxed text-ink-body dark:text-night-body">
                                                {{ $mod['description'] }}
                                            </p>

                                            <div class="mt-6">
                                                <h4 class="text-xs font-bold text-ink dark:text-night-ink">
                                                    Urutan Submateri ({{ count($mod['topics']) }} Topik)
                                                </h4>

                                                @if(count($mod['topics']) > 0)
                                                    <ol class="mt-3 flex flex-col gap-2">
                                                        @foreach($mod['topics'] as $topic)
                                                            <li class="flex flex-wrap items-start justify-between gap-2 rounded-lg border border-rule bg-paper px-3.5 py-3 text-sm text-ink dark:border-night-rule dark:bg-night-surface dark:text-night-ink">
                                                                <div class="flex items-start gap-3">
                                                                    <span class="shrink-0 rounded-md bg-pine-50 px-2 py-0.5 text-xs font-bold text-pine-700 dark:bg-pine-dark-badge dark:text-pine-200">
                                                                        {{ $topic['code'] }}
                                                                    </span>
                                                                    <span class="font-medium leading-snug">{{ $topic['title'] }}</span>
                                                                </div>
                                                                @if($topic['has_video'])
                                                                    <span class="rounded-md border border-rule bg-white px-2 py-0.5 text-[11px] font-semibold text-ink-body dark:border-night-rule dark:bg-night-raised dark:text-night-body">
                                                                        Teks &amp; Video
                                                                    </span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                @else
                                                    <p class="mt-3 rounded-lg border border-dashed border-control-border bg-paper p-4 text-xs text-ink-body dark:border-night-control dark:bg-night-surface dark:text-night-body">
                                                        Submateri untuk modul ini sedang disiapkan oleh tim pengelola kurikulum.
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="mt-6 flex flex-col gap-3 border-t border-rule pt-5 dark:border-night-rule sm:flex-row sm:items-center sm:justify-between">
                                                <p class="text-xs text-ink-body dark:text-night-body">
                                                    Syarat akses: kerjakan Pre-Test Modul {{ $mod['number'] }} untuk membuka seluruh topik.
                                                </p>
                                                <a
                                                    href="{{ auth()->check() ? route('modules.show', $mod['id']) : route('login') }}"
                                                    class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-lg bg-pine-700 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-pine-900 active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                                                >
                                                    {{ auth()->check() ? 'Pelajari Modul ' . $mod['number'] : 'Masuk untuk Mulai Modul ' . $mod['number'] }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-8 rounded-xl border border-dashed border-control-border bg-white p-6 dark:border-night-control dark:bg-night sm:p-8">
                            <h3 class="font-editorial text-lg font-semibold text-ink dark:text-night-ink">
                                Belum ada modul pembelajaran yang dipublikasikan
                            </h3>
                            <p class="mt-1.5 max-w-xl text-sm leading-relaxed text-ink-body dark:text-night-body">
                                Basis data kurikulum saat ini masih kosong. Pengelola riset dapat masuk ke ruang kerja admin untuk mengunggah modul baru atau mengimpor format silabus.
                            </p>
                            <div class="mt-4">
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-pine-700 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-pine-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950"
                                >
                                    Masuk sebagai Pengelola Riset
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <section
                id="spektrum-klinis"
                class="scroll-mt-16 bg-pine-900 py-10 text-night-ink dark:border-b dark:border-night-rule dark:bg-night sm:py-16"
            >
                <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12 lg:gap-10">
                        <div class="lg:col-span-7">
                            <p class="text-xs font-semibold text-ochre-300">
                                Referensi Klinis Ginekologi (FIGO, Kemenkes RI, &amp; WaLIDD)
                            </p>
                            <h2 class="font-editorial mt-1.5 text-2xl font-semibold tracking-tight text-night-ink sm:text-3xl">
                                Spektrum warna darah menstruasi dan klasifikasi volume perdarahan.
                            </h2>
                            <p class="mt-2.5 text-sm leading-relaxed text-pine-100 dark:text-night-body">
                                Di dalam portal, mahasiswi mencatat siklus haid, menilai derajat nyeri dismenore melalui instrumen WaLIDD (skor 0 sampai 12), serta mengenali tanda fisiologis dibandingkan kondisi yang memerlukan rujukan medis.
                            </p>

                            <div class="mt-6 overflow-hidden rounded-xl border border-pine-800 bg-paper text-ink dark:border-night-rule dark:bg-night-surface dark:text-night-ink">
                                <div class="border-b border-rule bg-parchment px-4 py-3 dark:border-night-rule dark:bg-night-raised">
                                    <h3 class="text-xs font-bold text-ink dark:text-night-ink">
                                        1. Indikator Karakteristik Warna &amp; Konsistensi Darah Haid
                                    </h3>
                                </div>
                                <div class="grid grid-cols-1 divide-y divide-rule dark:divide-night-rule md:grid-cols-2 md:divide-x md:divide-y-0">
                                    @foreach($bloodSpectrum as $idx => $item)
                                        <div class="flex items-start gap-3.5 p-4 {{ $idx >= 2 ? 'md:border-t md:border-rule dark:md:border-night-rule' : '' }}">
                                            <span
                                                class="mt-1 h-5 w-5 shrink-0 rounded-sm border border-control-border dark:border-night-control"
                                                style="background-color: {{ $item['swatch_hex'] }};"
                                                aria-hidden="true"
                                            ></span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center justify-between gap-1.5">
                                                    <h4 class="text-sm font-bold text-ink dark:text-night-ink">{{ $item['label'] }}</h4>
                                                    @if($item['tone'] === 'normal')
                                                        <span class="rounded-md bg-pine-50 px-2 py-0.5 text-[11px] font-semibold text-pine-700 dark:bg-pine-dark-badge dark:text-pine-200">
                                                            {{ $item['status'] }}
                                                        </span>
                                                    @elseif($item['tone'] === 'caution')
                                                        <span class="rounded-md bg-ochre-50 px-2 py-0.5 text-[11px] font-semibold text-ochre-800 dark:bg-ochre-950 dark:text-ochre-300">
                                                            {{ $item['status'] }}
                                                        </span>
                                                    @else
                                                        <span class="rounded-md bg-clay-50 px-2 py-0.5 text-[11px] font-semibold text-clay-700 dark:bg-clay-950 dark:text-clay-200">
                                                            {{ $item['status'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="mt-1 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                    {{ $item['note'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="border-t border-rule bg-parchment px-4 py-3 dark:border-night-rule dark:bg-night-raised">
                                    <h3 class="text-xs font-bold text-ink dark:text-night-ink">
                                        2. Klasifikasi Volume Menstruasi per Siklus (Standar FIGO &amp; Skor Higham PBAC)
                                    </h3>
                                </div>
                                <dl class="divide-y divide-rule bg-white dark:divide-night-rule dark:bg-night">
                                    @foreach($figoVolumes as $vol)
                                        <div class="grid grid-cols-1 gap-1 px-4 py-3 sm:grid-cols-12 sm:items-baseline sm:gap-3">
                                            <dt class="text-xs font-bold text-ink dark:text-night-ink sm:col-span-4">
                                                {{ $vol['category'] }}
                                            </dt>
                                            <dd class="text-xs font-semibold text-pine-700 dark:text-pine-300 sm:col-span-3">
                                                {{ $vol['range'] }}
                                            </dd>
                                            <dd class="text-xs leading-relaxed text-ink-body dark:text-night-body sm:col-span-5">
                                                {{ $vol['indicator'] }}
                                            </dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        </div>

                        <div class="flex flex-col gap-5 lg:col-span-5">
                            <div>
                                <p class="text-xs font-semibold text-ochre-300">
                                    Analisis Sosial, Medis, &amp; Perlindungan Hukum
                                </p>
                                <h2 class="font-editorial mt-1.5 text-2xl font-semibold tracking-tight text-night-ink sm:text-3xl">
                                    Bedah kasus nyata kehidupan mahasiswa di lingkungan kampus.
                                </h2>
                                <p class="mt-2.5 text-sm leading-relaxed text-pine-100 dark:text-night-body">
                                    Setiap studi kasus dikaji dari aspek klinis dan payung hukum agar mahasiswa mampu mengenali manipulasi relasional serta mengambil langkah perlindungan yang tepat.
                                </p>
                            </div>

                            @if(count($cases) > 0)
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-1">
                                    @foreach($cases as $case)
                                        <article class="rounded-xl border border-pine-800 bg-pine-950/80 p-5 dark:border-night-rule dark:bg-night-surface">
                                            <span class="inline-block rounded-md bg-ochre-950 px-2.5 py-1 text-[11px] font-semibold text-ochre-300">
                                                {{ $case['category'] }}
                                            </span>
                                            <h3 class="font-editorial mt-2.5 text-lg font-semibold leading-snug text-night-ink">
                                                {{ $case['title'] }}
                                            </h3>
                                            <p class="mt-2 text-xs leading-relaxed text-pine-100 dark:text-night-body">
                                                {{ \Illuminate\Support\Str::limit($case['legal_analysis'], 175) }}
                                            </p>
                                            <p class="mt-2.5 border-t border-pine-800 pt-2.5 text-xs leading-relaxed text-pine-100 dark:border-night-rule dark:text-night-body">
                                                <strong class="font-semibold text-night-ink">Langkah aman:</strong> {{ \Illuminate\Support\Str::limit($case['solution_tips'], 130) }}
                                            </p>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="rounded-xl border border-dashed border-pine-300/60 bg-pine-950/60 p-5 dark:border-night-control dark:bg-night-surface">
                                    <h3 class="text-sm font-bold text-night-ink">Belum ada studi kasus yang dipublikasikan</h3>
                                    <p class="mt-1 text-xs leading-relaxed text-pine-100 dark:text-night-body">
                                        Anda tetap dapat mengajukan pertanyaan sensitif secara rahasia kepada Dosen PA dan konselor melalui forum diskusi anonim di dalam portal.
                                    </p>
                                </div>
                            @endif

                            <div class="rounded-xl border border-pine-800 bg-pine-800/70 p-4 text-xs leading-relaxed text-pine-100 dark:border-night-rule dark:bg-night-raised dark:text-night-body">
                                <strong class="font-semibold text-night-ink">Forum Tanya Jawab Anonim:</strong> Nama asli mahasiswa disembunyikan menjadi kode inisial responden ketika berkonsultasi dengan Dosen Pembimbing Akademik maupun konselor kampus.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section
                id="kaspro"
                class="scroll-mt-16 border-b border-rule bg-paper py-10 dark:border-night-rule dark:bg-night sm:py-14 lg:py-18"
            >
                <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                        <div class="max-w-2xl">
                            <p class="text-xs font-semibold text-clay-600 dark:text-clay-300">Pojok Visual Kaspro (KesproFeed)</p>
                            <h2 class="font-editorial mt-1.5 text-2xl font-semibold tracking-tight text-ink dark:text-night-ink sm:text-3xl">
                                Lembar poster edukasi ringkas untuk dibaca dan dibagikan ke teman sebaya.
                            </h2>
                            <p class="mt-2 text-sm leading-relaxed text-ink-body dark:text-night-body">
                                Materi visual dari basis data SIKERA merangkum poin praktis higienitas, pengenalan tanda klinis, dan komunikasi sehat agar mudah diteruskan melalui WhatsApp.
                            </p>
                        </div>
                        <a
                            href="{{ auth()->check() ? route('posters.index') : route('login') }}"
                            class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-lg border border-control-border bg-white px-4 py-2 text-xs font-semibold text-ink transition-colors hover:bg-parchment active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                        >
                            {{ auth()->check() ? 'Buka Galeri Lengkap Kaspro' : 'Masuk untuk Unduh Poster' }}
                        </a>
                    </div>

                    @if(count($posters) > 0)
                        @php
                            $featuredPoster = $posters[0];
                            $secondaryPosters = array_slice($posters, 1);
                        @endphp
                        <div class="mt-8 grid grid-cols-1 items-stretch gap-6 lg:grid-cols-12">
                            <article class="flex flex-col justify-between overflow-hidden rounded-xl border border-rule bg-white shadow-sm dark:border-night-rule dark:bg-night-surface lg:col-span-7">
                                <div>
                                    @if($featuredPoster['image_url'])
                                        <div class="aspect-3/2 w-full overflow-hidden border-b border-rule bg-parchment dark:border-night-rule dark:bg-night-raised">
                                            <img
                                                src="{{ $featuredPoster['image_url'] }}"
                                                alt="Poster edukasi {{ $featuredPoster['title'] }}"
                                                width="900"
                                                height="600"
                                                loading="lazy"
                                                class="h-full w-full object-cover"
                                            >
                                        </div>
                                    @else
                                        <div class="border-b border-rule bg-parchment p-6 dark:border-night-rule dark:bg-night-raised sm:p-8">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="rounded-md bg-pine-700 px-2.5 py-1 text-[11px] font-semibold text-white dark:bg-pine-400 dark:text-pine-950">
                                                    {{ $featuredPoster['category'] }}
                                                </span>
                                                <span class="font-editorial text-xs font-semibold text-ink-subtle dark:text-night-body">
                                                    Edisi Utama Kaspro &middot; 01
                                                </span>
                                            </div>
                                            <h3 class="font-editorial mt-5 text-2xl font-semibold leading-snug text-ink dark:text-night-ink sm:text-3xl">
                                                {{ $featuredPoster['title'] }}
                                            </h3>
                                            <div class="mt-5 flex h-1 w-24 overflow-hidden rounded-xs" aria-hidden="true">
                                                <span class="w-16 bg-pine-700 dark:bg-pine-400"></span>
                                                <span class="w-8 bg-clay-600 dark:bg-clay-300"></span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="p-5 sm:p-6">
                                        @if($featuredPoster['image_url'])
                                            <span class="inline-block rounded-md bg-pine-50 px-2.5 py-1 text-[11px] font-semibold text-pine-700 dark:bg-pine-dark-badge dark:text-pine-200">
                                                {{ $featuredPoster['category'] }}
                                            </span>
                                            <h3 class="font-editorial mt-2.5 text-xl font-semibold text-ink dark:text-night-ink">
                                                {{ $featuredPoster['title'] }}
                                            </h3>
                                        @endif
                                        <p class="text-sm leading-relaxed text-ink-body dark:text-night-body">
                                            {{ $featuredPoster['caption'] }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-rule bg-paper px-5 py-4 dark:border-night-rule dark:bg-night sm:px-6">
                                    <span class="text-xs font-medium text-ink-subtle dark:text-night-body">
                                        Siap diteruskan ke grup studi mahasiswa
                                    </span>
                                    <a
                                        href="{{ $featuredPoster['share_url'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-pine-700 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-pine-900 active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                                    >
                                        Bagikan Pesan Edukasi via WhatsApp
                                    </a>
                                </div>
                            </article>

                            @if(count($secondaryPosters) > 0)
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:col-span-5 lg:grid-cols-1">
                                    @foreach($secondaryPosters as $sIdx => $poster)
                                        <article class="flex flex-col justify-between rounded-xl border border-rule bg-white p-5 dark:border-night-rule dark:bg-night-surface">
                                            <div>
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="rounded-md bg-clay-50 px-2.5 py-1 text-[11px] font-semibold text-clay-700 dark:bg-clay-950 dark:text-clay-200">
                                                        {{ $poster['category'] }}
                                                    </span>
                                                    <span class="font-editorial text-xs font-bold text-ink-subtle dark:text-night-body">
                                                        0{{ $sIdx + 2 }}
                                                    </span>
                                                </div>
                                                <h3 class="font-editorial mt-3 text-lg font-semibold leading-snug text-ink dark:text-night-ink">
                                                    {{ $poster['title'] }}
                                                </h3>
                                                <p class="mt-2 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                    {{ $poster['caption'] }}
                                                </p>
                                            </div>

                                            <div class="mt-4 border-t border-rule pt-3 dark:border-night-rule">
                                                <a
                                                    href="{{ $poster['share_url'] }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex min-h-11 items-center rounded-lg px-2 text-xs font-semibold text-pine-700 underline decoration-pine-700/50 underline-offset-4 transition-colors hover:text-pine-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-pine-300 dark:decoration-pine-300/50 dark:hover:text-pine-200 dark:focus-visible:outline-pine-300"
                                                >
                                                    Bagikan "{{ \Illuminate\Support\Str::limit($poster['title'], 28) }}" ke WhatsApp
                                                </a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-8 rounded-xl border border-dashed border-control-border bg-white p-6 dark:border-night-control dark:bg-night-surface">
                            <h3 class="font-editorial text-lg font-semibold text-ink dark:text-night-ink">
                                Belum ada poster Kaspro yang diunggah
                            </h3>
                            <p class="mt-1 text-sm text-ink-body dark:text-night-body">
                                Galeri poster edukasi akan tampil otomatis setelah tim pengelola menerbitkan materi visual baru di ruang kerja admin.
                            </p>
                        </div>
                    @endif
                </div>
            </section>

            <section
                id="rujukan"
                class="scroll-mt-16 bg-parchment py-10 dark:bg-night-surface sm:py-14 lg:py-16"
            >
                <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <p class="text-xs font-semibold text-clay-600 dark:text-clay-300">Jejaring Pendampingan &amp; Rujukan Resmi</p>
                        <h2 class="font-editorial mt-1.5 text-2xl font-semibold tracking-tight text-ink dark:text-night-ink sm:text-3xl">
                            Direktori konseling kampus dan fasilitas gawat darurat di Bengkulu.
                        </h2>
                        <p class="mt-2 text-sm leading-relaxed text-ink-body dark:text-night-body">
                            Saluran pendampingan psikologis, pelaporan kekerasan seksual, dan rujukan klinis bagi mahasiswa Universitas Bengkulu. Nomor telepon yang belum terverifikasi ditandai secara terbuka agar mahasiswa merujuk langsung ke alamat layanan resmi.
                        </p>
                    </div>

                    @if(count($hotlines) > 0)
                        <div class="mt-7 divide-y divide-rule overflow-hidden rounded-xl border border-rule bg-white dark:divide-night-rule dark:border-night-rule dark:bg-night">
                            @foreach($hotlines as $hotline)
                                <div class="flex flex-col justify-between gap-4 p-5 sm:p-6 lg:flex-row lg:items-center">
                                    <div class="max-w-2xl">
                                        <span class="inline-block rounded-md bg-clay-50 px-2.5 py-1 text-[11px] font-semibold text-clay-700 dark:bg-clay-950 dark:text-clay-200">
                                            {{ $hotline['category'] }}
                                        </span>
                                        <h3 class="font-editorial mt-2 text-lg font-semibold text-ink dark:text-night-ink">
                                            {{ $hotline['name'] }}
                                        </h3>
                                        <p class="mt-1 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                            {{ $hotline['description'] }}
                                        </p>
                                        <p class="mt-2 text-xs text-ink dark:text-night-ink">
                                            <span class="font-semibold">Lokasi:</span> {{ $hotline['address'] }} &middot; <span class="font-semibold">Jam layanan:</span> {{ $hotline['operating_hours'] }}
                                        </p>
                                    </div>

                                    <div class="shrink-0">
                                        @if($hotline['is_verified_phone'] && $hotline['tel_href'])
                                            <a
                                                href="{{ $hotline['tel_href'] }}"
                                                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg border border-control-border bg-paper px-4 py-2.5 text-xs font-semibold text-ink transition-colors hover:bg-parchment active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300 lg:w-auto"
                                            >
                                                <svg class="h-4 w-4 shrink-0 text-pine-700 dark:text-pine-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                                                </svg>
                                                <span>Hubungi {{ $hotline['phone_display'] }}</span>
                                            </a>
                                        @else
                                            <div class="rounded-lg border border-dashed border-control-border bg-paper px-3.5 py-2.5 text-left dark:border-night-control dark:bg-night-surface lg:max-w-xs">
                                                <p class="text-[11px] font-bold text-ochre-800 dark:text-ochre-300">
                                                    [KONTAK TELEPON BELUM DIVERIFIKASI]
                                                </p>
                                                <p class="mt-0.5 text-[11px] leading-snug text-ink-body dark:text-night-body">
                                                    Kunjungi langsung sekretariat di lokasi kampus yang tertera pada jam kerja.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-7 rounded-xl border border-dashed border-control-border bg-white p-6 dark:border-night-control dark:bg-night">
                            <p class="text-sm font-semibold text-ink dark:text-night-ink">
                                Daftar kontak darurat belum tercatat di basis data.
                            </p>
                            <p class="mt-1 text-xs text-ink-body dark:text-night-body">
                                Untuk keperluan mendesak di lingkungan kampus, silakan mendatangi Pusat Layanan Mahasiswa Universitas Bengkulu di Kandang Limun atau IGD RSUD M. Yunus Bengkulu.
                            </p>
                        </div>
                    @endif

                    <div class="mt-10 overflow-hidden rounded-xl border border-rule bg-white shadow-sm dark:border-night-rule dark:bg-night">
                        <div class="flex h-1.5 w-full" aria-hidden="true">
                            <span class="grow bg-pine-700 dark:bg-pine-400"></span>
                            <span class="w-28 bg-clay-600 dark:bg-clay-300"></span>
                        </div>
                        <div class="flex flex-col justify-between gap-6 p-6 sm:p-8 lg:flex-row lg:items-center">
                            <div class="max-w-2xl">
                                <h2 class="font-editorial text-xl font-semibold tracking-tight text-ink dark:text-night-ink sm:text-2xl">
                                    Mulai sesi pembelajaran atau pemantauan kelas di portal SIKERA.
                                </h2>
                                <p class="mt-1.5 text-sm leading-relaxed text-ink-body dark:text-night-body">
                                    Mahasiswa baru mendaftar satu kali melalui formulir biodata responden, sedangkan Dosen PA serta pengelola riset dapat langsung masuk menggunakan akun terdaftar.
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                                @auth
                                    <a
                                        href="{{ url('/dashboard') }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-pine-900 active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                                    >
                                        Lanjutkan ke Ruang Kerja
                                    </a>
                                @else
                                    <a
                                        href="{{ route('register') }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-pine-900 active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                                    >
                                        Isi Formulir Biodata Mahasiswa
                                    </a>
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-control-border bg-paper px-5 py-2.5 text-sm font-semibold text-ink transition-colors hover:bg-parchment active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                                    >
                                        Masuk ke Portal SIKERA
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-rule bg-paper dark:border-night-rule dark:bg-night">
            <div class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex flex-col justify-between gap-6 border-b border-rule pb-6 dark:border-night-rule md:flex-row md:items-center">
                    <div class="flex items-center gap-3">
                        <span class="flex h-7 w-1.5 shrink-0 flex-col overflow-hidden rounded-xs" aria-hidden="true">
                            <span class="h-4 w-full bg-pine-700 dark:bg-pine-400"></span>
                            <span class="h-3 w-full bg-clay-600 dark:bg-clay-300"></span>
                        </span>
                        <div>
                            <p class="font-editorial text-base font-bold text-ink dark:text-night-ink">SIKERA</p>
                            <p class="text-xs text-ink-body dark:text-night-body">Sistem Informasi Kesehatan Reproduksi Remaja</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold text-ink-body dark:text-night-body">
                        <a href="#kurikulum" class="inline-flex min-h-11 items-center rounded-lg px-2 hover:bg-parchment hover:text-pine-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:hover:bg-night-surface dark:hover:text-pine-300 dark:focus-visible:outline-pine-300">Silabus</a>
                        <a href="#spektrum-klinis" class="inline-flex min-h-11 items-center rounded-lg px-2 hover:bg-parchment hover:text-pine-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:hover:bg-night-surface dark:hover:text-pine-300 dark:focus-visible:outline-pine-300">Spektrum Klinis</a>
                        <a href="#kaspro" class="inline-flex min-h-11 items-center rounded-lg px-2 hover:bg-parchment hover:text-pine-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:hover:bg-night-surface dark:hover:text-pine-300 dark:focus-visible:outline-pine-300">Kaspro</a>
                        <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center rounded-lg px-2 hover:bg-parchment hover:text-pine-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:hover:bg-night-surface dark:hover:text-pine-300 dark:focus-visible:outline-pine-300">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center rounded-lg px-2 hover:bg-parchment hover:text-pine-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:hover:bg-night-surface dark:hover:text-pine-300 dark:focus-visible:outline-pine-300">Daftar Responden</a>
                    </div>
                </div>

                <div class="mt-5 flex flex-col justify-between gap-2 text-xs text-ink-subtle dark:text-night-body sm:flex-row">
                    <span>Universitas Bengkulu &middot; Jl. WR Supratman, Kandang Limun, Kota Bengkulu</span>
                    <span>Publikasi Edukasi Kesehatan Reproduksi &amp; Instrumen Riset N-Gain</span>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
