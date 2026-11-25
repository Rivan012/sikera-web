import { Head, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { useTheme } from '@/contexts/theme-context';
import { BookOpenIcon, CheckIcon, ClockIcon, MoonIcon, ShareIcon, SunIcon } from '@/icons';
import type { SharedProps } from '@/types/sikera';
import { cn } from '@/utils';

interface WelcomeTopic {
    code: string;
    title: string;
    has_video: boolean;
    has_pdf: boolean;
}

interface WelcomeModule {
    id: number;
    number: string;
    title: string;
    subtitle: string;
    description: string;
    duration: string;
    topics: WelcomeTopic[];
}

interface WelcomePoster {
    id: number;
    title: string;
    category: string;
    caption: string;
    share_url: string;
    image_url: string | null;
}

interface WelcomeCase {
    id: number;
    title: string;
    category: string;
    narrative: string;
    legal_analysis: string;
    medical_analysis: string;
    solution_tips: string;
}

interface WelcomeHotline {
    id: number;
    name: string;
    category: string;
    phone_display: string | null;
    tel_href: string | null;
    is_verified_phone: boolean;
    address: string;
    operating_hours: string;
    description: string;
}

interface BloodSwatch {
    label: string;
    status: string;
    tone: 'normal' | 'caution' | 'urgent';
    swatch_hex: string;
    note: string;
}

interface FigoRow {
    category: string;
    range: string;
    indicator: string;
}

interface WelcomeProps {
    modules: WelcomeModule[];
    totalTopicsCount: number;
    posters: WelcomePoster[];
    cases: WelcomeCase[];
    hotlines: WelcomeHotline[];
    bloodSpectrum: BloodSwatch[];
    figoVolumes: FigoRow[];
    dataLoadError: boolean;
}

export default function Welcome({
    modules,
    totalTopicsCount,
    posters,
    cases,
    hotlines,
    bloodSpectrum,
    figoVolumes,
    dataLoadError,
}: WelcomeProps) {
    const { auth } = usePage<SharedProps>().props;
    const { theme, toggleTheme } = useTheme();
    const isDark = theme === 'dark';
    const [activeModuleIndex, setActiveModuleIndex] = useState(0);

    const activeModule = modules[activeModuleIndex] ?? modules[0] ?? null;

    return (
        <>
            <Head title="Beranda Edukasi Kesehatan Reproduksi" />
            <div className="min-h-svh bg-paper font-jakarta text-ink transition-colors duration-150 dark:bg-night dark:text-night-ink">
                <a
                    href="#konten-utama"
                    className="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-pine-700 focus:px-4 focus:py-2.5 focus:text-sm focus:font-semibold focus:text-white"
                >
                    Lompat ke konten utama
                </a>

                <header className="sticky top-0 z-40 border-b border-rule bg-paper/95 backdrop-blur-xs dark:border-night-rule dark:bg-night/95">
                    <div className="mx-auto flex max-w-7xl items-center justify-between gap-2 px-3 py-2.5 sm:gap-3 sm:px-6 sm:py-3 lg:px-8">
                        <Link
                            href="/"
                            aria-label="Beranda SIKERA"
                            className="inline-flex min-h-11 min-w-0 items-center gap-2 rounded-lg px-1 py-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 sm:gap-3 sm:px-1.5 dark:focus-visible:outline-pine-300"
                        >
                            <img src="/images/sikera-logo.webp" alt="" width={36} height={36} className="size-8 shrink-0 sm:size-10" />
                            <span className="flex min-w-0 flex-col">
                                <span className="font-editorial text-base leading-none font-bold tracking-tight sm:text-lg">SIKERA</span>
                                <span className="hidden text-xs text-ink-subtle sm:block dark:text-night-body">
                                    Sistem Informasi Kesehatan Reproduksi Remaja
                                </span>
                            </span>
                        </Link>

                        <nav aria-label="Navigasi halaman utama" className="hidden items-center gap-1 lg:flex">
                            <a
                                href="#silabus"
                                className="inline-flex min-h-11 items-center rounded-lg px-3 text-sm font-medium text-ink-body hover:bg-parchment hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-body dark:hover:bg-night-surface dark:hover:text-night-ink dark:focus-visible:outline-pine-300"
                            >
                                Silabus Flipbook
                            </a>
                            <a
                                href="#referensi-klinis"
                                className="inline-flex min-h-11 items-center rounded-lg px-3 text-sm font-medium text-ink-body hover:bg-parchment hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-body dark:hover:bg-night-surface dark:hover:text-night-ink dark:focus-visible:outline-pine-300"
                            >
                                Referensi Klinis
                            </a>
                            <a
                                href="#rujukan"
                                className="inline-flex min-h-11 items-center rounded-lg px-3 text-sm font-medium text-ink-body hover:bg-parchment hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:text-night-body dark:hover:bg-night-surface dark:hover:text-night-ink dark:focus-visible:outline-pine-300"
                            >
                                Rujukan Bengkulu
                            </a>
                        </nav>

                        <div className="flex shrink-0 items-center gap-1.5 sm:gap-2">
                            <button
                                type="button"
                                onClick={toggleTheme}
                                aria-pressed={isDark}
                                aria-label={isDark ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'}
                                className="inline-flex min-h-11 min-w-11 items-center justify-center gap-2 rounded-lg border border-control-border bg-white px-2.5 py-2 text-xs font-semibold text-ink hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 sm:px-3 dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                            >
                                {isDark ? <SunIcon className="size-4 text-ochre-300" /> : <MoonIcon className="size-4" />}
                                <span className="hidden sm:inline">{isDark ? 'Terang' : 'Gelap'}</span>
                            </button>

                            {auth.user ? (
                                <Link
                                    href="/dashboard"
                                    className="inline-flex min-h-11 items-center rounded-lg bg-pine-700 px-3 py-2 text-xs font-semibold text-white hover:bg-pine-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 sm:px-4 sm:py-2.5 sm:text-sm dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                                >
                                    <span className="sm:hidden">Dasbor</span>
                                    <span className="hidden sm:inline">Buka Ruang Belajar</span>
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href="/login"
                                        className="inline-flex min-h-11 items-center rounded-lg border border-control-border bg-white px-2.5 py-2 text-xs font-semibold text-ink hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 sm:px-3.5 sm:text-sm dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised dark:focus-visible:outline-pine-300"
                                    >
                                        Masuk
                                    </Link>
                                    <Link
                                        href="/register"
                                        className="inline-flex min-h-11 items-center rounded-lg bg-pine-700 px-3 py-2 text-xs font-semibold text-white hover:bg-pine-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 sm:px-4 sm:py-2.5 sm:text-sm dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300 dark:focus-visible:outline-pine-300"
                                    >
                                        <span className="sm:hidden">Daftar</span>
                                        <span className="hidden sm:inline">Daftar Responden</span>
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </header>

                <main id="konten-utama">
                    {dataLoadError && (
                        <div className="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                            <div
                                role="alert"
                                className="rounded-xl border border-clay-600 bg-clay-50 px-4 py-3 text-sm font-medium text-clay-800 dark:border-clay-300/50 dark:bg-clay-950 dark:text-clay-200"
                            >
                                Sebagian daftar modul dan rujukan belum dapat dimuat saat ini. Anda tetap dapat masuk atau mendaftar ke ruang belajar.
                            </div>
                        </div>
                    )}

                    <section className="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
                        <div className="grid grid-cols-1 items-start gap-8 lg:grid-cols-12 lg:gap-10">
                            <div className="lg:col-span-7">
                                <div className="mb-4 flex items-center gap-3">
                                    <span className="inline-flex h-1.5 w-10 overflow-hidden rounded-sm" aria-hidden="true">
                                        <span className="w-7 bg-pine-700 dark:bg-pine-400" />
                                        <span className="w-3 bg-clay-600 dark:bg-clay-300" />
                                    </span>
                                    <span className="text-xs font-bold tracking-wider text-pine-700 uppercase dark:text-pine-300">
                                        Publikasi Edukasi Kesehatan Kampus Bengkulu
                                    </span>
                                </div>

                                <h1 className="font-editorial text-3xl leading-tight font-bold tracking-tight sm:text-4xl lg:text-5xl">
                                    Literasi kesehatan reproduksi ilmiah, terukur, dan aman bagi mahasiswa baru.
                                </h1>

                                <p className="mt-4 max-w-2xl text-base leading-relaxed text-ink-body sm:text-lg dark:text-night-body">
                                    SIKERA mendampingi mahasiswa lintas kampus di Bengkulu mempelajari anatomi reproduksi, pencatatan siklus menstruasi berbasis FIGO &amp; PBAC, perlindungan hukum UU TPKS, hingga evaluasi pembelajaran terstruktur dalam format flipbook PDF interaktif.
                                </p>

                                <div className="mt-7 flex flex-wrap items-center gap-3">
                                    {auth.user ? (
                                        <Link
                                            href="/modules"
                                            className="inline-flex min-h-12 items-center gap-2 rounded-lg bg-pine-700 px-5 py-3 text-sm font-semibold text-white hover:bg-pine-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300"
                                        >
                                            <BookOpenIcon className="size-4" />
                                            Lanjutkan Modul Flipbook
                                        </Link>
                                    ) : (
                                        <>
                                            <Link
                                                href="/register"
                                                className="inline-flex min-h-12 items-center gap-2 rounded-lg bg-pine-700 px-5 py-3 text-sm font-semibold text-white hover:bg-pine-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300"
                                            >
                                                Mulai Sebagai Responden Mahasiswa
                                            </Link>
                                            <a
                                                href="#silabus"
                                                className="inline-flex min-h-12 items-center rounded-lg border border-control-border bg-white px-5 py-3 text-sm font-semibold text-ink hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised"
                                            >
                                                Tinjau {modules.length || 4} Modul Kurikulum
                                            </a>
                                        </>
                                    )}
                                </div>

                                <dl className="mt-8 grid grid-cols-3 gap-4 border-t border-rule pt-6 dark:border-night-rule">
                                    <div>
                                        <dt className="text-xs font-semibold text-ink-subtle dark:text-night-body">Modul Inti</dt>
                                        <dd className="mt-1 font-editorial text-2xl font-bold sm:text-3xl">{modules.length || 4}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-xs font-semibold text-ink-subtle dark:text-night-body">Subbab PDF</dt>
                                        <dd className="mt-1 font-editorial text-2xl font-bold sm:text-3xl">{totalTopicsCount || 16}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-xs font-semibold text-ink-subtle dark:text-night-body">Retensi Post-Test</dt>
                                        <dd className="mt-1 font-editorial text-2xl font-bold sm:text-3xl">+3 Hari</dd>
                                    </div>
                                </dl>
                            </div>

                            <aside
                                aria-label="Protokol Evaluasi Pembelajaran N-Gain"
                                className="rounded-xl border border-rule bg-white p-6 shadow-sm lg:col-span-5 dark:border-night-rule dark:bg-night-surface"
                            >
                                <div className="flex items-center justify-between gap-2 border-b border-rule pb-4 dark:border-night-rule">
                                    <span className="text-xs font-bold tracking-wider text-pine-700 uppercase dark:text-pine-300">
                                        Protokol Riset &amp; Evaluasi
                                    </span>
                                    <span className="rounded-lg border border-ochre-800/25 bg-ochre-50 px-2.5 py-1 text-xs font-semibold text-ochre-800 dark:border-ochre-300/30 dark:bg-ochre-950 dark:text-ochre-300">
                                        Indeks N-Gain Hake
                                    </span>
                                </div>

                                <ol className="mt-5 flex flex-col gap-4">
                                    <li className="flex items-start gap-3">
                                        <span className="flex size-7 shrink-0 items-center justify-center rounded-lg bg-pine-50 font-editorial text-sm font-bold text-pine-800 dark:bg-pine-dark-badge dark:text-pine-200">
                                            01
                                        </span>
                                        <div>
                                            <p className="text-sm font-bold">Pre-Test Sebelum Materi Tiap Modul</p>
                                            <p className="mt-0.5 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                Setiap modul diawali kuesioner Pre-Test terfokus untuk memetakan pemahaman awal sebelum flipbook PDF dibuka.
                                            </p>
                                        </div>
                                    </li>
                                    <li className="flex items-start gap-3">
                                        <span className="flex size-7 shrink-0 items-center justify-center rounded-lg bg-pine-50 font-editorial text-sm font-bold text-pine-800 dark:bg-pine-dark-badge dark:text-pine-200">
                                            02
                                        </span>
                                        <div>
                                            <p className="text-sm font-bold">Eksplorasi Flipbook PDF &amp; Instrumen Mandiri</p>
                                            <p className="mt-0.5 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                Pelajari seluruh subbab modul secara berurutan disertai kalkulator siklus haid, skor dismenore WaLIDD, dan IMT.
                                            </p>
                                        </div>
                                    </li>
                                    <li className="flex items-start gap-3">
                                        <span className="flex size-7 shrink-0 items-center justify-center rounded-lg bg-clay-50 font-editorial text-sm font-bold text-clay-800 dark:bg-clay-950 dark:text-clay-200">
                                            03
                                        </span>
                                        <div>
                                            <p className="text-sm font-bold">Post-Test Terpadu 3 Hari Setelah Semua Modul Selesai</p>
                                            <p className="mt-0.5 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                Post-Test akhir terbuka otomatis 72 jam sejak subbab terakhir tuntas untuk mengukur retensi pengetahuan nyata.
                                            </p>
                                        </div>
                                    </li>
                                </ol>

                                <div className="mt-5 rounded-lg border border-rule bg-parchment p-3.5 dark:border-night-rule dark:bg-night-raised">
                                    <p className="text-xs font-semibold text-ink-subtle dark:text-night-body">Rumus Efektivitas Total:</p>
                                    <p className="mt-1 font-mono text-xs font-bold text-ink dark:text-night-ink">
                                        N-Gain = (Skor Post-Test - Rata-rata Pre-Test) / (100 - Rata-rata Pre-Test)
                                    </p>
                                </div>
                            </aside>
                        </div>
                    </section>

                    <section id="silabus" className="border-y border-rule bg-parchment py-12 sm:py-16 dark:border-night-rule dark:bg-night-surface/60">
                        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            <div className="max-w-2xl">
                                <span className="text-xs font-bold tracking-wider text-pine-700 uppercase dark:text-pine-300">
                                    Kurikulum Terstruktur
                                </span>
                                <h2 className="mt-1 font-editorial text-2xl font-bold sm:text-3xl">
                                    Penjelajah Silabus &amp; Subbab Flipbook PDF
                                </h2>
                                <p className="mt-2 text-sm leading-relaxed text-ink-body dark:text-night-body">
                                    Pilih nomor modul untuk melihat cakupan subbab materi yang telah dikurasi bersama tim akademik kesehatan reproduksi.
                                </p>
                            </div>

                            {modules.length === 0 ? (
                                <p className="mt-6 rounded-xl border border-rule bg-white p-6 text-sm text-ink-body dark:border-night-rule dark:bg-night-surface dark:text-night-body">
                                    Daftar silabus sedang disiapkan oleh pengelola riset.
                                </p>
                            ) : (
                                <div className="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-12">
                                    <div
                                        role="tablist"
                                        aria-label="Daftar modul kurikulum"
                                        className="flex gap-2 overflow-x-auto pb-1 lg:col-span-5 lg:flex-col lg:overflow-visible"
                                    >
                                        {modules.map((mod, idx) => {
                                            const selected = idx === activeModuleIndex;
                                            return (
                                                <button
                                                    key={mod.id}
                                                    type="button"
                                                    role="tab"
                                                    id={`tab-modul-${mod.id}`}
                                                    aria-selected={selected}
                                                    aria-controls={`panel-modul-${mod.id}`}
                                                    onClick={() => setActiveModuleIndex(idx)}
                                                    className={cn(
                                                        'flex min-h-12 min-w-56 shrink-0 items-start gap-3 rounded-lg border p-3.5 text-left transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 lg:min-w-0 dark:focus-visible:outline-pine-300',
                                                        selected
                                                            ? 'border-pine-700 bg-pine-700 text-white dark:border-pine-400 dark:bg-pine-400 dark:text-pine-950'
                                                            : 'border-rule bg-white text-ink hover:bg-paper dark:border-night-rule dark:bg-night-surface dark:text-night-ink dark:hover:bg-night-raised',
                                                    )}
                                                >
                                                    <span className="font-editorial text-lg font-bold">{mod.number}</span>
                                                    <span className="min-w-0 flex-1">
                                                        <span className="block text-sm font-bold leading-snug">{mod.title}</span>
                                                        <span
                                                            className={cn(
                                                                'mt-1 block text-xs',
                                                                selected ? 'text-pine-100 dark:text-pine-950/80' : 'text-ink-subtle dark:text-night-body',
                                                            )}
                                                        >
                                                            {mod.topics.length} subbab &middot; {mod.duration}
                                                        </span>
                                                    </span>
                                                </button>
                                            );
                                        })}
                                    </div>

                                    {activeModule && (
                                        <div
                                            role="tabpanel"
                                            id={`panel-modul-${activeModule.id}`}
                                            aria-labelledby={`tab-modul-${activeModule.id}`}
                                            className="rounded-xl border border-rule bg-white p-6 shadow-sm lg:col-span-7 dark:border-night-rule dark:bg-night-surface"
                                        >
                                            <div className="flex flex-wrap items-center justify-between gap-2 border-b border-rule pb-4 dark:border-night-rule">
                                                <div>
                                                    <span className="font-editorial text-sm font-bold text-pine-700 dark:text-pine-300">
                                                        Modul {activeModule.number}
                                                    </span>
                                                    <h3 className="mt-0.5 text-xl font-bold">{activeModule.title}</h3>
                                                </div>
                                                <span className="inline-flex items-center gap-1.5 rounded-lg border border-rule bg-parchment px-3 py-1 text-xs font-semibold text-ink-body dark:border-night-rule dark:bg-night-raised dark:text-night-body">
                                                    <ClockIcon className="size-3.5" />
                                                    {activeModule.duration}
                                                </span>
                                            </div>

                                            {activeModule.description && (
                                                <p className="mt-4 text-sm leading-relaxed text-ink-body dark:text-night-body">
                                                    {activeModule.description}
                                                </p>
                                            )}

                                            <ul className="mt-5 divide-y divide-rule dark:divide-night-rule">
                                                {activeModule.topics.map((topic) => (
                                                    <li key={topic.code + topic.title} className="flex items-center justify-between gap-3 py-3 text-sm">
                                                        <div className="flex min-w-0 items-center gap-2.5">
                                                            <span className="rounded bg-pine-50 px-2 py-0.5 font-mono text-xs font-bold text-pine-800 dark:bg-pine-dark-badge dark:text-pine-200">
                                                                {topic.code}
                                                            </span>
                                                            <span className="font-medium">{topic.title}</span>
                                                        </div>
                                                        <span className="shrink-0 text-xs font-semibold text-pine-700 dark:text-pine-300">
                                                            {topic.has_pdf ? 'Flipbook PDF' : 'Ringkasan'}
                                                        </span>
                                                    </li>
                                                ))}
                                            </ul>

                                            <div className="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-rule pt-4 dark:border-night-rule">
                                                <span className="inline-flex items-center gap-1.5 text-xs font-medium text-ink-subtle dark:text-night-body">
                                                    <CheckIcon className="size-4 text-pine-700 dark:text-pine-300" />
                                                    Diawali Pre-Test Modul {activeModule.number} sebelum membuka flipbook
                                                </span>
                                                <Link
                                                    href={auth.user ? `/modules/${activeModule.id}` : '/login'}
                                                    className="inline-flex min-h-11 items-center rounded-lg bg-pine-700 px-4 py-2 text-xs font-semibold text-white hover:bg-pine-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300"
                                                >
                                                    {auth.user ? 'Buka Modul Ini' : 'Masuk untuk Membaca'}
                                                </Link>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    </section>

                    <section id="referensi-klinis" className="bg-pine-900 py-12 text-white sm:py-16 dark:bg-pine-950">
                        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            <div className="grid grid-cols-1 gap-8 lg:grid-cols-12">
                                <div className="lg:col-span-6">
                                    <span className="text-xs font-bold tracking-wider text-ochre-300 uppercase">
                                        Panduan Observasi Mandiri
                                    </span>
                                    <h2 className="mt-1 font-editorial text-2xl font-bold sm:text-3xl">
                                        Spektrum Warna Darah Menstruasi
                                    </h2>
                                    <p className="mt-2 text-sm leading-relaxed text-pine-100">
                                        Kenali variasi warna darah haid yang fisiologis dan tanda peringatan yang memerlukan pemeriksaan klinis.
                                    </p>

                                    <div className="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        {bloodSpectrum.map((item) => (
                                            <div key={item.label} className="rounded-xl border border-pine-800 bg-pine-950/70 p-4">
                                                <div className="flex items-center gap-2.5">
                                                    <span
                                                        className="size-4 shrink-0 rounded-sm border border-white/30"
                                                        style={{ backgroundColor: item.swatch_hex }}
                                                        aria-hidden="true"
                                                    />
                                                    <span className="text-sm font-bold text-white">{item.label}</span>
                                                </div>
                                                <p className="mt-1 text-xs font-semibold text-ochre-300">{item.status}</p>
                                                <p className="mt-1.5 text-xs leading-relaxed text-pine-100">{item.note}</p>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                <div className="lg:col-span-6">
                                    <span className="text-xs font-bold tracking-wider text-ochre-300 uppercase">
                                        Klasifikasi Klinis FIGO &amp; PBAC
                                    </span>
                                    <h2 className="mt-1 font-editorial text-2xl font-bold sm:text-3xl">
                                        Estimasi Volume Pengeluaran Siklus
                                    </h2>
                                    <p className="mt-2 text-sm leading-relaxed text-pine-100">
                                        Acuan kuantitatif untuk mendeteksi risiko perdarahan haid berlebih (Heavy Menstrual Bleeding) sejak dini.
                                    </p>

                                    <div className="mt-6 overflow-x-auto rounded-xl border border-pine-800 bg-pine-950/70">
                                        <table className="w-full min-w-full text-left text-sm">
                                            <thead className="border-b border-pine-800 text-xs font-semibold text-pine-200">
                                                <tr>
                                                    <th scope="col" className="px-4 py-3">Kategori</th>
                                                    <th scope="col" className="px-4 py-3">Volume / Siklus</th>
                                                    <th scope="col" className="px-4 py-3">Indikator Harian</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-pine-800/80 text-xs sm:text-sm">
                                                {figoVolumes.map((row) => (
                                                    <tr key={row.category} className="align-top">
                                                        <td className="px-4 py-3 font-bold text-white">{row.category}</td>
                                                        <td className="px-4 py-3 font-semibold whitespace-nowrap text-ochre-300">{row.range}</td>
                                                        <td className="px-4 py-3 leading-relaxed text-pine-100">{row.indicator}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {(cases.length > 0 || posters.length > 0) && (
                        <section className="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                            <div className="grid grid-cols-1 gap-10 lg:grid-cols-12">
                                {cases.length > 0 && (
                                    <div className="lg:col-span-7">
                                        <span className="text-xs font-bold tracking-wider text-clay-600 uppercase dark:text-clay-300">
                                            Bedah Kasus Kampus &amp; Indekos
                                        </span>
                                        <h2 className="mt-1 font-editorial text-2xl font-bold sm:text-3xl">
                                            Tinjauan Medis &amp; Perlindungan UU TPKS
                                        </h2>
                                        <div className="mt-6 flex flex-col gap-4">
                                            {cases.map((item) => (
                                                <article
                                                    key={item.id}
                                                    className="rounded-xl border border-rule bg-white p-5 dark:border-night-rule dark:bg-night-surface"
                                                >
                                                    <span className="rounded-lg bg-clay-50 px-2.5 py-1 text-xs font-semibold text-clay-800 dark:bg-clay-950 dark:text-clay-200">
                                                        {item.category}
                                                    </span>
                                                    <h3 className="mt-2.5 text-base font-bold">{item.title}</h3>
                                                    <p className="mt-1.5 line-clamp-3 text-sm leading-relaxed text-ink-body dark:text-night-body">
                                                        {item.narrative}
                                                    </p>
                                                </article>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {posters.length > 0 && (
                                    <div className="lg:col-span-5">
                                        <span className="text-xs font-bold tracking-wider text-pine-700 uppercase dark:text-pine-300">
                                            Projek Visual Kaspro
                                        </span>
                                        <h2 className="mt-1 font-editorial text-2xl font-bold sm:text-3xl">
                                            Lembar Edukasi Siap Bagikan
                                        </h2>
                                        <div className="mt-6 flex flex-col gap-4">
                                            {posters.map((poster) => (
                                                <article
                                                    key={poster.id}
                                                    className="rounded-xl border border-rule bg-white p-5 dark:border-night-rule dark:bg-night-surface"
                                                >
                                                    <div className="flex items-start justify-between gap-3">
                                                        <div>
                                                            <span className="text-xs font-semibold text-pine-700 dark:text-pine-300">
                                                                {poster.category}
                                                            </span>
                                                            <h3 className="mt-0.5 text-base font-bold">{poster.title}</h3>
                                                        </div>
                                                        <a
                                                            href={poster.share_url}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            aria-label={`Bagikan poster ${poster.title} ke WhatsApp`}
                                                            className="inline-flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-lg border border-control-border text-ink hover:bg-parchment focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-night-control dark:text-night-ink dark:hover:bg-night-raised"
                                                        >
                                                            <ShareIcon />
                                                        </a>
                                                    </div>
                                                    <p className="mt-2 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                        {poster.caption}
                                                    </p>
                                                </article>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </section>
                    )}

                    <section id="rujukan" className="border-t border-rule bg-parchment py-12 sm:py-16 dark:border-night-rule dark:bg-night-surface/60">
                        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            <div className="max-w-2xl">
                                <span className="text-xs font-bold tracking-wider text-clay-600 uppercase dark:text-clay-300">
                                    Jejaring Pendampingan Resmi
                                </span>
                                <h2 className="mt-1 font-editorial text-2xl font-bold sm:text-3xl">
                                    Direktori Layanan &amp; Rujukan Darurat Bengkulu
                                </h2>
                                <p className="mt-2 text-sm leading-relaxed text-ink-body dark:text-night-body">
                                    Layanan konseling kampus, perlindungan perempuan, serta fasilitas kesehatan terdekat di Kota Bengkulu.
                                </p>
                            </div>

                            {hotlines.length === 0 ? (
                                <p className="mt-6 rounded-xl border border-rule bg-white p-5 text-sm text-ink-body dark:border-night-rule dark:bg-night-surface dark:text-night-body">
                                    Daftar kontak rujukan sedang diperbarui.
                                </p>
                            ) : (
                                <div className="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    {hotlines.map((item) => (
                                        <article
                                            key={item.id}
                                            className="flex flex-col justify-between gap-4 rounded-xl border border-rule bg-white p-5 dark:border-night-rule dark:bg-night-surface"
                                        >
                                            <div>
                                                <span className="text-xs font-bold text-pine-700 dark:text-pine-300">{item.category}</span>
                                                <h3 className="mt-1 text-base font-bold">{item.name}</h3>
                                                {item.description && (
                                                    <p className="mt-1.5 text-xs leading-relaxed text-ink-body dark:text-night-body">
                                                        {item.description}
                                                    </p>
                                                )}
                                                {item.address && (
                                                    <p className="mt-2 text-xs text-ink-subtle dark:text-night-body">
                                                        Lokasi: {item.address}
                                                    </p>
                                                )}
                                                {item.operating_hours && (
                                                    <p className="mt-0.5 text-xs text-ink-subtle dark:text-night-body">
                                                        Jam layanan: {item.operating_hours}
                                                    </p>
                                                )}
                                            </div>

                                            {item.is_verified_phone && item.tel_href && item.phone_display ? (
                                                <a
                                                    href={item.tel_href}
                                                    className="inline-flex min-h-11 items-center justify-center rounded-lg border border-pine-700 bg-pine-50 px-4 py-2 text-xs font-bold text-pine-800 hover:bg-pine-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pine-700 dark:border-pine-400 dark:bg-pine-dark-badge dark:text-pine-200"
                                                >
                                                    Hubungi {item.phone_display}
                                                </a>
                                            ) : (
                                                <span className="inline-flex min-h-11 items-center justify-center rounded-lg border border-rule bg-parchment px-3 py-2 text-xs font-medium text-ink-subtle dark:border-night-rule dark:bg-night-raised dark:text-night-body">
                                                    Kunjungi langsung sesuai alamat layanan
                                                </span>
                                            )}
                                        </article>
                                    ))}
                                </div>
                            )}
                        </div>
                    </section>
                </main>

                <footer className="border-t border-rule bg-paper py-8 dark:border-night-rule dark:bg-night">
                    <div className="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 text-xs text-ink-subtle sm:flex-row sm:px-6 lg:px-8 dark:text-night-body">
                        <p className="font-medium">SIKERA &middot; Sistem Informasi Kesehatan Reproduksi Remaja Universitas Bengkulu</p>
                        <p>Edukasi ilmiah, flipbook PDF, dan evaluasi N-Gain lintas kampus</p>
                    </div>
                </footer>
            </div>
        </>
    );
}
