<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKERA | Sistem Informasi Kesehatan Reproduksi Remaja Universitas Bengkulu</title>
    <meta name="description" content="Platform edukasi ilmiah kesehatan reproduksi remaja, pelacak kesehatan mandiri, dan instrumen evaluasi riset Pre-Test serta Post-Test mahasiswa baru Universitas Bengkulu.">
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
    :class="{ 'dark bg-slate-900': darkMode === true }"
    class="font-jakarta bg-slate-50 text-slate-900 antialiased"
>
    @php
        $modules = [
            [
                'number' => '01',
                'title' => 'Anatomi, Fisiologi, & Higienitas Reproduksi',
                'duration' => '15 Menit',
                'subtitle' => 'Pemahaman dasar struktur biologis, proses pubertas, siklus menstruasi, dan kebersihan diri.',
                'description' => 'Membahas sistem reproduksi pria dan wanita secara ilmiah tanpa vulgaritas, variasi warna darah haid yang fisiologis dibandingkan kondisi patologis, serta penanganan mandiri nyeri haid (dismenore).',
                'topics' => [
                    ['code' => '1.1', 'title' => 'Pengenalan Anatomi Organ Reproduksi Pria & Wanita'],
                    ['code' => '1.2', 'title' => 'Pubertas & Mekanisme Hormonal (Mimpi Basah & Siklus Haid)'],
                    ['code' => '1.3', 'title' => 'Variasi Karakteristik Darah Menstruasi'],
                    ['code' => '1.4', 'title' => 'Higienitas Genitalia & Penanganan Dismenore (Skala NRS)'],
                ],
            ],
            [
                'number' => '02',
                'title' => 'Batasan Pergaulan, Pacaran Sehat, & Dinamika Kampus',
                'duration' => '12 Menit',
                'subtitle' => 'Membangun relasi yang saling menghargai, komunikasi asertif, dan pencegahan kekerasan relasional.',
                'description' => 'Membekali mahasiswa baru dengan pemahaman batasan fisik dan emosional (boundaries), analisis risiko kehidupan indekos (living together), serta teknik berkata TIDAK secara asertif.',
                'topics' => [
                    ['code' => '2.1', 'title' => 'Batasan Fisik & Emosional (Healthy Dating vs Relasi Toksik)'],
                    ['code' => '2.2', 'title' => 'Bedah Kasus Lingkungan Mahasiswa (Living Together & Kos-kosan)'],
                    ['code' => '2.3', 'title' => 'Keterampilan Asertif Remaja (Teknik Berkata "TIDAK")'],
                ],
            ],
            [
                'number' => '03',
                'title' => 'Risiko Medis Seks Bebas & Perlindungan Diri',
                'duration' => '15 Menit',
                'subtitle' => 'Edukasi pencegahan IMS, HIV/AIDS, bahaya aborsi ilegal, dan perlindungan keamanan digital.',
                'description' => 'Tinjauan ilmiah mengenai transmisi infeksi menular seksual, konsekuensi medis aborsi tanpa pengawasan tenaga kesehatan, serta mitigasi Kekerasan Berbasis Gender Online (KBGO).',
                'topics' => [
                    ['code' => '3.1', 'title' => 'Infeksi Menular Seksual (IMS) & HIV/AIDS'],
                    ['code' => '3.2', 'title' => 'Kehamilan Tidak Diinginkan (KTD) & Bahaya Aborsi Tidak Aman'],
                    ['code' => '3.3', 'title' => 'Keamanan Digital Remaja (Cybersexuality, Sexting, & KBGO)'],
                ],
            ],
            [
                'number' => '04',
                'title' => 'Mitigasi Pernikahan Dini & Kesiapan Berkeluarga',
                'duration' => '10 Menit',
                'subtitle' => 'Empat pilar kesiapan menikah BKKBN, risiko kehamilan remaja, dan pencegahan stunting.',
                'description' => 'Mempersiapkan generasi muda melalui pematangan usia perkawinan ideal BKKBN (21 tahun wanita dan 25 tahun pria), mitigasi risiko panggul sempit (CPD), dan pemenuhan gizi remaja.',
                'topics' => [
                    ['code' => '4.1', 'title' => 'Empat Pilar Kesiapan Menikah (BKKBN: Usia 21 Wanita & 25 Pria)'],
                    ['code' => '4.2', 'title' => 'Dampak Medis Kehamilan Usia Dini (CPD, Preeklamsia, Kematian Maternal)'],
                    ['code' => '4.3', 'title' => 'Pencegahan Stunting sejak Usia Remaja (Gizi & Anemia)'],
                ],
            ],
        ];

        $bloodGuide = [
            [
                'label' => 'Merah Terang',
                'status' => 'Fisiologis (Normal)',
                'swatch' => 'bg-rose-600',
                'badgeClass' => 'bg-teal-50 text-teal-800 dark:bg-teal-950 dark:text-teal-200',
                'note' => 'Darah segar dengan aliran lancar, umum terjadi pada hari ke-1 sampai ke-3 siklus haid.',
            ],
            [
                'label' => 'Cokelat Gelap / Kehitaman',
                'status' => 'Fisiologis (Normal)',
                'swatch' => 'bg-amber-900',
                'badgeClass' => 'bg-teal-50 text-teal-800 dark:bg-teal-950 dark:text-teal-200',
                'note' => 'Darah sisa di akhir siklus yang mengalami oksidasi alami karena keluar lebih perlahan.',
            ],
            [
                'label' => 'Merah Muda Pucat',
                'status' => 'Perlu Diamati',
                'swatch' => 'bg-rose-300',
                'badgeClass' => 'bg-amber-50 text-amber-900 dark:bg-amber-950 dark:text-amber-200',
                'note' => 'Dapat muncul saat kadar estrogen relatif rendah atau sebagai bercak awal siklus (spotting).',
            ],
            [
                'label' => 'Gumpalan Tebal & Berbau',
                'status' => 'Rujuk Medis',
                'swatch' => 'bg-rose-900',
                'badgeClass' => 'bg-rose-50 text-rose-900 dark:bg-rose-950 dark:text-rose-200',
                'note' => 'Bila gumpalan lebih besar dari koin atau disertai demam dan aroma menyengat, periksa ke faskes.',
            ],
        ];

        $hotlines = [
            [
                'category' => 'PPKS Kampus',
                'name' => 'Satgas PPKS Universitas Bengkulu',
                'address' => 'Gedung Rektorat UNIB Lt. 2, Jl. WR Supratman, Kandang Limun, Bengkulu',
                'hours' => 'Senin - Jumat (08.00 - 16.00 WIB) & Hotline Darurat 24 Jam',
                'phone' => '0812-7890-1234',
                'tel' => '081278901234',
                'description' => 'Layanan resmi pelaporan dan pendampingan kasus kekerasan seksual di lingkungan civitas akademika UNIB dengan kerahasiaan pelapor terjaga.',
            ],
            [
                'category' => 'Konseling Psikologi',
                'name' => 'Unit Layanan Bimbingan & Konseling Mahasiswa UNIB',
                'address' => 'Pusat Layanan Mahasiswa UNIB, Kampus Utama',
                'hours' => 'Senin - Jumat (08.30 - 15.30 WIB)',
                'phone' => '0736-21170',
                'tel' => '073621170',
                'description' => 'Konseling tatap muka maupun daring bersama psikolog dan konselor untuk isu akademik, relasi, dan kesehatan mental mahasiswa.',
            ],
            [
                'category' => 'Faskes & Rumah Sakit',
                'name' => 'IGD RSUD M. Yunus Bengkulu (Rujukan Medis)',
                'address' => 'Jl. Bhayangkara, Sidomulyo, Kec. Gading Cempaka, Kota Bengkulu',
                'hours' => '24 Jam Setiap Hari',
                'phone' => '0736-52004',
                'tel' => '073652004',
                'description' => 'Fasilitas gawat darurat medis terpadu dan layanan rujukan resmi pemeriksaan klinis di Kota Bengkulu.',
            ],
        ];
    @endphp

    <div class="flex min-h-screen flex-col bg-slate-50 text-slate-900 transition-colors duration-150 dark:bg-slate-900 dark:text-slate-100">
        {{-- Sticky Header --}}
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-slate-50/95 dark:border-slate-800 dark:bg-slate-900/95">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <a
                    href="#beranda"
                    class="inline-flex min-h-11 items-center gap-3 rounded-lg px-1.5 py-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:focus-visible:outline-teal-400"
                >
                    <span class="flex h-8 w-1.5 shrink-0 flex-col overflow-hidden rounded-sm" aria-hidden="true">
                        <span class="h-5 w-full bg-teal-700 dark:bg-teal-400"></span>
                        <span class="h-3 w-full bg-rose-600 dark:bg-rose-500"></span>
                    </span>
                    <span class="flex flex-col">
                        <span class="text-base font-bold leading-tight tracking-tight text-slate-900 dark:text-white">SIKERA</span>
                        <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Universitas Bengkulu</span>
                    </span>
                </a>

                <nav aria-label="Navigasi utama" class="hidden items-center gap-1 lg:flex">
                    <a
                        href="#kurikulum"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-200/70 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white dark:focus-visible:outline-teal-400"
                    >
                        Kurikulum
                    </a>
                    <a
                        href="#instrumen-mandiri"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-200/70 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white dark:focus-visible:outline-teal-400"
                    >
                        Pemantauan &amp; Kasus
                    </a>
                    <a
                        href="#layanan-darurat"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-200/70 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white dark:focus-visible:outline-teal-400"
                    >
                        Layanan Darurat
                    </a>
                </nav>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="darkMode = !darkMode"
                        :aria-pressed="darkMode.toString()"
                        :aria-label="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
                        class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
                    >
                        <span x-show="!darkMode" class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 shrink-0 fill-current text-slate-700" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                            </svg>
                            <span class="hidden sm:inline">Mode Gelap</span>
                        </span>
                        <span x-cloak x-show="darkMode" class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 shrink-0 fill-current text-amber-400" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 3a1 1 0 011 1v1a1 1 0 11-2 0V4a1 1 0 011-1zm0 15a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm9-8a1 1 0 110 2h-1a1 1 0 110-2h1zM5 11a1 1 0 110 2H4a1 1 0 110-2h1zm14.071-5.071a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM7.05 16.95a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zm12.021.707a1 1 0 01-1.414 0l-.707-.707a1 1 0 111.414-1.414l.707.707a1 1 0 010 1.414zM7.05 7.05a1 1 0 01-1.414 0l-.707-.707A1 1 0 116.343 4.93l.707.707a1 1 0 010 1.414zM12 8a4 4 0 100 8 4 4 0 000-8z"/>
                            </svg>
                            <span class="hidden sm:inline">Mode Terang</span>
                        </span>
                    </button>

                    <div class="hidden items-center gap-2 md:flex">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="inline-flex min-h-11 items-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                            >
                                Buka Dashboard
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-800 shadow-sm transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
                            >
                                Masuk ke Portal
                            </a>
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex min-h-11 items-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
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
                        aria-label="Buka menu navigasi"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-800 shadow-sm transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400 lg:hidden"
                    >
                        <svg x-show="!mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                        <svg x-cloak x-show="mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Drawer --}}
            <div
                id="mobile-nav"
                x-cloak
                x-show="mobileMenuOpen"
                class="border-t border-slate-200 bg-white px-4 py-4 dark:border-slate-800 dark:bg-slate-900 lg:hidden"
            >
                <nav aria-label="Navigasi seluler" class="mx-auto flex max-w-6xl flex-col gap-1">
                    <a
                        href="#kurikulum"
                        @click="mobileMenuOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-100 dark:hover:bg-slate-800 dark:focus-visible:outline-teal-400"
                    >
                        Kurikulum 4 Modul
                    </a>
                    <a
                        href="#instrumen-mandiri"
                        @click="mobileMenuOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-100 dark:hover:bg-slate-800 dark:focus-visible:outline-teal-400"
                    >
                        Pemantauan Mandiri &amp; Kasus Kampus
                    </a>
                    <a
                        href="#layanan-darurat"
                        @click="mobileMenuOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-100 dark:hover:bg-slate-800 dark:focus-visible:outline-teal-400"
                    >
                        Layanan Darurat Bengkulu
                    </a>

                    <div class="mt-2 grid grid-cols-1 gap-2 border-t border-slate-200 pt-3 dark:border-slate-800 sm:grid-cols-2">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                            >
                                Buka Dashboard
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                            >
                                Masuk ke Portal
                            </a>
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                            >
                                Daftar Responden
                            </a>
                        @endauth
                    </div>
                </nav>
            </div>
        </header>

        <main class="flex-1">
            {{-- Section 1: Hero Editorial --}}
            <section id="beranda" class="mx-auto w-full max-w-6xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
                <div class="max-w-4xl">
                    <p class="inline-flex items-center gap-2 text-xs font-semibold text-teal-700 dark:text-teal-300">
                        <span class="h-2 w-2 rounded-xs bg-rose-600 dark:bg-rose-400" aria-hidden="true"></span>
                        <span>Sistem Informasi Kesehatan Reproduksi Remaja &middot; Universitas Bengkulu</span>
                    </p>

                    <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight text-slate-900 dark:text-white sm:text-4xl lg:text-5xl">
                        Edukasi ilmiah kesehatan reproduksi remaja dan instrumen evaluasi kognitif terukur.
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-relaxed text-slate-600 dark:text-slate-300 sm:text-base">
                        SIKERA mendampingi mahasiswa baru Universitas Bengkulu memahami anatomi, siklus haid, batasan relasi sehat, serta perlindungan diri tanpa vulgaritas, sekaligus mengukur peningkatan pemahaman lewat sistem gating Pre-Test, Post-Test, dan indeks N-Gain.
                    </p>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                            >
                                Buka Dashboard Utama
                            </a>
                        @else
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                            >
                                Isi Formulir Responden Baru
                            </a>
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
                            >
                                Masuk ke Portal SIKERA
                            </a>
                        @endauth

                        <a
                            href="#kurikulum"
                            class="inline-flex min-h-11 items-center justify-center rounded-lg px-3 py-2.5 text-sm font-semibold text-teal-700 underline decoration-teal-700/40 underline-offset-4 transition-colors hover:text-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-teal-300 dark:decoration-teal-300/40 dark:hover:text-teal-200 dark:focus-visible:outline-teal-400"
                        >
                            Lihat Silabus 4 Modul
                        </a>
                    </div>

                    <dl class="mt-8 grid grid-cols-1 gap-6 border-t border-slate-200 pt-6 dark:border-slate-800 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs font-semibold text-slate-600 dark:text-slate-300">Kurikulum Terstruktur</dt>
                            <dd class="mt-1 text-base font-bold text-slate-900 dark:text-white">4 Modul &middot; 13 Submateri</dd>
                            <dd class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">Teks ilmiah dan video YouTube</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-600 dark:text-slate-300">Pemantauan &amp; Konseling</dt>
                            <dd class="mt-1 text-base font-bold text-slate-900 dark:text-white">Kalender Haid, IMT, &amp; Forum Anonim</dd>
                            <dd class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">Alat self-care mandiri dan konsultasi konselor</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-600 dark:text-slate-300">Evaluasi Riset</dt>
                            <dd class="mt-1 text-base font-bold text-slate-900 dark:text-white">Pre-Test, Post-Test, N-Gain</dd>
                            <dd class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">Ekspor siap olah Excel dan SPSS</dd>
                        </div>
                    </dl>
                </div>
            </section>

            {{-- Section 2: Master-Detail Silabus 4 Modul --}}
            <section
                id="kurikulum"
                class="scroll-mt-20 border-t border-slate-200 bg-white py-12 dark:border-slate-800 dark:bg-slate-900/50 sm:py-16"
                x-data="{
                    modules: @js($modules),
                    activeIdx: 0,
                    get activeModule() {
                        return this.modules[this.activeIdx];
                    }
                }"
            >
                <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">Silabus Pembelajaran &amp; Sistem Gating</p>
                        <h2 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                            Empat modul inti dengan evaluasi Pre-Test dan Post-Test per modul.
                        </h2>
                        <p class="mt-2.5 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                            Setiap modul mengharuskan mahasiswa menyelesaikan Pre-Test terlebih dahulu sebelum materi teks dan video terbuka, lalu menutup sesi belajar dengan Post-Test untuk menghitung indeks N-Gain.
                        </p>
                    </div>

                    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-8">
                        <div class="flex flex-col gap-2.5 lg:col-span-5" role="listbox" aria-label="Pilih modul pembelajaran SIKERA">
                            <template x-for="(mod, idx) in modules" :key="mod.number">
                                <button
                                    type="button"
                                    @click="activeIdx = idx"
                                    :aria-pressed="(activeIdx === idx).toString()"
                                    :class="activeIdx === idx
                                        ? 'border-teal-700 bg-teal-50/80 text-slate-900 shadow-sm dark:border-teal-400 dark:bg-teal-950/60 dark:text-white'
                                        : 'border-slate-200 bg-slate-50 text-slate-800 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700/80'"
                                    class="flex min-h-11 w-full items-start gap-3.5 rounded-xl border p-4 text-left transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:focus-visible:outline-teal-400"
                                >
                                    <span
                                        :class="activeIdx === idx
                                            ? 'bg-teal-700 text-white dark:bg-teal-400 dark:text-slate-950'
                                            : 'bg-white text-teal-800 border border-slate-200 dark:bg-slate-900 dark:text-teal-300 dark:border-slate-700'"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold"
                                        x-text="mod.number"
                                    ></span>
                                    <span class="flex flex-1 flex-col">
                                        <span class="text-sm font-semibold leading-snug" x-text="mod.title"></span>
                                        <span class="mt-1 text-xs font-medium text-slate-600 dark:text-slate-300" x-text="'Estimasi ' + mod.duration + ' · ' + mod.topics.length + ' Submateri'"></span>
                                    </span>
                                </button>
                            </template>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-8 lg:col-span-7">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-4 dark:border-slate-700">
                                <span class="text-xs font-bold text-teal-700 dark:text-teal-300" x-text="'MODUL ' + activeModule.number"></span>
                                <span class="rounded-md bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200" x-text="'Durasi ' + activeModule.duration"></span>
                            </div>

                            <h3 class="mt-4 text-xl font-bold text-slate-900 dark:text-white" x-text="activeModule.title"></h3>
                            <p class="mt-1.5 text-sm font-medium text-slate-700 dark:text-slate-200" x-text="activeModule.subtitle"></p>
                            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300" x-text="activeModule.description"></p>

                            <div class="mt-6">
                                <h4 class="text-xs font-semibold text-slate-700 dark:text-slate-200">Rincian Topik Materi</h4>
                                <ol class="mt-3 flex flex-col gap-2.5">
                                    <template x-for="topic in activeModule.topics" :key="topic.code">
                                        <li class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                            <span class="shrink-0 rounded-md bg-teal-50 px-2 py-0.5 text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-200" x-text="topic.code"></span>
                                            <span class="font-medium leading-snug" x-text="topic.title"></span>
                                        </li>
                                    </template>
                                </ol>
                            </div>

                            <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-5 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs text-slate-600 dark:text-slate-300">
                                    Alur akses: Pre-Test Pembuka, Materi &amp; Video, lalu Post-Test Evaluasi.
                                </p>
                                <a
                                    href="{{ auth()->check() ? route('modules.index') : route('register') }}"
                                    class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                                >
                                    {{ auth()->check() ? 'Buka Daftar Modul' : 'Mulai Belajar di Portal SIKERA' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 3: Deep Teal Band - Referensi Klinis & Studi Kasus Kampus --}}
            <section id="instrumen-mandiri" class="scroll-mt-20 bg-teal-900 py-12 text-white dark:border-y dark:border-slate-800 dark:bg-slate-800 sm:py-16">
                <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10">
                        {{-- Kolom Kiri: Panduan Klinis Haid & Self-Care --}}
                        <div class="lg:col-span-6">
                            <p class="inline-flex items-center gap-2 text-xs font-semibold text-amber-300">
                                <span class="h-2 w-2 rounded-xs bg-amber-400" aria-hidden="true"></span>
                                <span>Instrumen Self-Care &amp; Panduan Klinis</span>
                            </p>
                            <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">
                                Pelacak siklus haid, skala nyeri NRS, dan spektrum darah menstruasi.
                            </h2>
                            <p class="mt-3 text-sm leading-relaxed text-teal-50 dark:text-slate-300">
                                Mahasiswa perempuan dapat mencatat hari pertama haid, memprediksi masa subur dan ovulasi, serta menilai derajat dismenore pada skala 0 hingga 10 disertai panduan higienitas genitalia.
                            </p>

                            <div class="mt-6 overflow-hidden rounded-xl border border-teal-800 bg-white text-slate-900 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-700">
                                    <h3 class="text-xs font-bold text-slate-800 dark:text-white">
                                        Referensi Visual Karakteristik Darah Haid
                                    </h3>
                                </div>
                                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @foreach($bloodGuide as $item)
                                    <div class="flex items-start gap-3.5 px-4 py-3.5">
                                        <span class="mt-1 h-4 w-4 shrink-0 rounded-sm border border-slate-300 dark:border-slate-600 {{ $item['swatch'] }}" aria-hidden="true"></span>
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item['label'] }}</span>
                                                <span class="rounded-md px-2 py-0.5 text-[11px] font-semibold {{ $item['badgeClass'] }}">
                                                    {{ $item['status'] }}
                                                </span>
                                            </div>
                                            <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ $item['note'] }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Studi Kasus Nyata & Forum Anonim --}}
                        <div class="flex flex-col gap-5 lg:col-span-6">
                            <div>
                                <p class="text-xs font-semibold text-amber-300">Sosial, Hukum, &amp; Konseling Kampus</p>
                                <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">
                                    Bedah kasus kehidupan kampus dan tanya jawab anonim.
                                </h2>
                                <p class="mt-3 text-sm leading-relaxed text-teal-50 dark:text-slate-300">
                                    Kasus riil di lingkungan mahasiswa dibedah dari perspektif medis, psikososial, dan perlindungan hukum agar mahasiswa mampu mengambil keputusan yang aman.
                                </p>
                            </div>

                            <div class="rounded-xl border border-teal-700 bg-teal-950/60 p-5 dark:border-slate-700 dark:bg-slate-900">
                                <span class="inline-block rounded-md bg-amber-950 px-2.5 py-1 text-[11px] font-semibold text-amber-200">
                                    Kasus Indekos &middot; Living Together
                                </span>
                                <h3 class="mt-2.5 text-base font-bold text-white">
                                    Dilema Relasi Kos-Kosan &amp; Tekanan Tinggal Bersama
                                </h3>
                                <p class="mt-2 text-xs leading-relaxed text-teal-50 dark:text-slate-300">
                                    Mengulas kerentanan mahasiswa saat kohabitasi tanpa ikatan perkawinan: ketimpangan beban ekonomi, isolasi pergaulan, risiko kehamilan tidak diinginkan (KTD), serta tidak adanya perlindungan hukum keperdataan.
                                </p>
                            </div>

                            <div class="rounded-xl border border-teal-700 bg-teal-950/60 p-5 dark:border-slate-700 dark:bg-slate-900">
                                <span class="inline-block rounded-md bg-rose-950 px-2.5 py-1 text-[11px] font-semibold text-rose-200">
                                    Perlindungan Digital &middot; UU TPKS No. 12 Tahun 2022
                                </span>
                                <h3 class="mt-2.5 text-base font-bold text-white">
                                    Ancaman Penyebaran Foto Pribadi (Sextortion &amp; KBGO)
                                </h3>
                                <p class="mt-2 text-xs leading-relaxed text-teal-50 dark:text-slate-300">
                                    Ancaman penyebaran konten intim tanpa persetujuan diatur dalam Pasal 14 UU TPKS sebagai Kekerasan Seksual Berbasis Elektronik (ancaman pidana penjara hingga 4 tahun). Dokumentasikan bukti dan segera lapor ke Satgas PPKS UNIB.
                                </p>
                            </div>

                            <div class="rounded-xl border border-teal-700/80 bg-teal-950/40 p-4 text-xs leading-relaxed text-teal-50 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-300">
                                <strong class="font-semibold text-white">Forum Tanya Jawab Anonim &amp; KesproFeed:</strong> Identitas penanya disamarkan saat berkonsultasi dengan Dosen PA dan konselor, dilengkapi galeri poster edukatif siap unduh dan bagikan ke WhatsApp.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 4: Direktori Rujukan Darurat Bengkulu & CTA Penutup --}}
            <section id="layanan-darurat" class="scroll-mt-20 border-t border-slate-200 bg-white py-12 dark:border-slate-800 dark:bg-slate-900/50 sm:py-16">
                <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                        <div class="max-w-2xl">
                            <p class="text-xs font-semibold text-rose-700 dark:text-rose-300">Rujukan Cepat &amp; Perlindungan Mahasiswa</p>
                            <h2 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                                Direktori layanan konseling dan darurat di Bengkulu.
                            </h2>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                                Akses langsung ke saluran resmi kampus dan rumah sakit rujukan bila mahasiswa membutuhkan pendampingan psikologis, pelaporan kekerasan seksual, atau pertolongan medis.
                            </p>
                        </div>
                    </div>

                    <div class="mt-7 divide-y divide-slate-200 overflow-hidden rounded-xl border border-slate-200 bg-slate-50 dark:divide-slate-700 dark:border-slate-700 dark:bg-slate-800">
                        @foreach($hotlines as $hotline)
                        <div class="flex flex-col justify-between gap-4 p-5 sm:p-6 lg:flex-row lg:items-center">
                            <div class="max-w-2xl">
                                <span class="inline-block rounded-md bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-900 dark:bg-rose-950 dark:text-rose-200">
                                    {{ $hotline['category'] }}
                                </span>
                                <h3 class="mt-2 text-base font-bold text-slate-900 dark:text-white">{{ $hotline['name'] }}</h3>
                                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ $hotline['description'] }}</p>
                                <p class="mt-2 text-xs text-slate-700 dark:text-slate-200">
                                    <span class="font-semibold">Lokasi:</span> {{ $hotline['address'] }} &middot; <span class="font-semibold">Jam Layanan:</span> {{ $hotline['hours'] }}
                                </p>
                            </div>

                            <div class="shrink-0">
                                <a
                                    href="tel:{{ $hotline['tel'] }}"
                                    class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400 lg:w-auto"
                                >
                                    Hubungi {{ $hotline['phone'] }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Closing CTA Banner --}}
                    <div class="mt-10 rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-8">
                        <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
                            <div class="max-w-2xl">
                                <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-2xl">
                                    Siap memulai pembelajaran dan evaluasi kesehatan reproduksi di SIKERA?
                                </h2>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                                    Mahasiswa baru dapat langsung mengisi formulir biodata responden, sedangkan Dosen PA dan pengelola riset dapat masuk dengan akun masing-masing.
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                                @auth
                                    <a
                                        href="{{ url('/dashboard') }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                                    >
                                        Lanjutkan ke Dashboard
                                    </a>
                                @else
                                    <a
                                        href="{{ route('register') }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                                    >
                                        Isi Formulir Pendaftaran Mahasiswa
                                    </a>
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
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

        {{-- Institutional 2-Row Footer --}}
        <footer class="border-t border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900">
            <div class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex flex-col justify-between gap-6 border-b border-slate-200 pb-6 dark:border-slate-800 md:flex-row md:items-center">
                    <div class="flex items-center gap-3">
                        <span class="flex h-7 w-1.5 shrink-0 flex-col overflow-hidden rounded-sm" aria-hidden="true">
                            <span class="h-4 w-full bg-teal-700 dark:bg-teal-400"></span>
                            <span class="h-3 w-full bg-rose-600 dark:bg-rose-500"></span>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">SIKERA</p>
                            <p class="text-xs text-slate-600 dark:text-slate-300">Sistem Informasi Kesehatan Reproduksi Remaja</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        <a href="#kurikulum" class="inline-flex min-h-11 items-center rounded-md px-1 hover:text-teal-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:hover:text-teal-300">Kurikulum</a>
                        <a href="#instrumen-mandiri" class="inline-flex min-h-11 items-center rounded-md px-1 hover:text-teal-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:hover:text-teal-300">Pemantauan</a>
                        <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center rounded-md px-1 hover:text-teal-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:hover:text-teal-300">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center rounded-md px-1 hover:text-teal-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:hover:text-teal-300">Daftar Responden</a>
                    </div>
                </div>

                <div class="mt-5 flex flex-col justify-between gap-2 text-xs text-slate-600 dark:text-slate-300 sm:flex-row">
                    <span>Universitas Bengkulu &middot; Kandang Limun, Kota Bengkulu</span>
                    <span>Platform Edukasi Ilmiah &amp; Evaluasi Intervensi Riset</span>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
