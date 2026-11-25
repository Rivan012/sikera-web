import { Head, Link, router, usePage } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { ProgressBar } from '@/components/ui/progress-bar';
import { formatDateId } from '@/constants/akademik';
import { useCountdown } from '@/hooks/use-countdown';
import {
    BookOpenIcon,
    CheckIcon,
    ClipboardCheckIcon,
    ClockIcon,
    FileTextIcon,
    HeartPulseIcon,
    LockIcon,
    MessageSquareIcon,
    SparklesIcon,
} from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { PosttestGate, SharedProps } from '@/types/sikera';
import { cn } from '@/utils';

interface DashboardModule {
    id: number;
    module_number: number;
    title: string;
    estimated_time?: string | number | null;
    topics_count: number;
    completed_topics: number;
    has_pretest: boolean;
}

interface DashboardPoster {
    id: number;
    title: string;
    category: string;
    caption: string;
}

interface DashboardThread {
    id: number;
    title: string;
    topic: string;
    replies_count: number;
    is_answered_by_counselor: boolean;
}

interface DashboardCase {
    id: number;
    title: string;
    category: string;
    narrative: string;
}

interface MahasiswaDashboardProps {
    modules: DashboardModule[];
    posters: DashboardPoster[];
    latestPeriod: {
        start_date: string;
        cycle_length: number;
        flow_level: string;
        nrs_pain_score: number;
    } | null;
    latestBmi: {
        bmi_value: number;
        category: string;
        advice: string;
    } | null;
    recentThreads: DashboardThread[];
    caseStudies: DashboardCase[];
    avgPreScore: number;
    posttestGate: PosttestGate;
}

export default function Dashboard({
    modules,
    posters,
    latestPeriod,
    latestBmi,
    recentThreads,
    caseStudies,
    avgPreScore,
    posttestGate,
}: MahasiswaDashboardProps) {
    const { auth } = usePage<SharedProps>().props;
    const user = auth.user;
    const countdown = useCountdown(
        posttestGate.state === 'waiting_delay' ? posttestGate.posttest_available_at : null,
        posttestGate.remaining_seconds,
        () => router.reload({ only: ['posttestGate'] }),
    );

    const overallPct =
        posttestGate.total_topics > 0
            ? Math.round((posttestGate.completed_topics / posttestGate.total_topics) * 100)
            : 0;

    return (
        <AppLayout title="Menu Utama Mahasiswa">
            <Head title="Menu Utama Mahasiswa" />

            <div className="flex flex-col gap-6">
                <section className="grid grid-cols-1 gap-4 lg:grid-cols-12">
                    <Card className="border-teal-200 bg-teal-50/60 lg:col-span-7 dark:border-teal-800/80 dark:bg-teal-950/35">
                        <CardContent className="flex flex-col justify-between gap-5">
                            <div>
                                <div className="flex flex-wrap items-center gap-2">
                                    <Badge color="primary">Responden Aktif</Badge>
                                    {user?.prodi && <Badge color="gray">{user.prodi}</Badge>}
                                </div>
                                <h2 className="mt-3 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl dark:text-white">
                                    Selamat datang, {user?.name}
                                </h2>
                                <p className="mt-1.5 text-sm leading-relaxed text-slate-700 dark:text-slate-200">
                                    Selesaikan Pre-Test pada masing-masing modul sebelum membuka subbab flipbook PDF. Setelah seluruh subbab tuntas, Post-Test akhir akan terbuka otomatis setelah jeda 3 hari.
                                </p>
                            </div>

                            <div className="flex flex-col gap-2">
                                <div className="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-200">
                                    <span>
                                        Progres Subbab Keseluruhan ({posttestGate.completed_topics}/{posttestGate.total_topics} subbab)
                                    </span>
                                    <span>{overallPct}%</span>
                                </div>
                                <ProgressBar value={overallPct} label="Progres keseluruhan subbab modul" />
                            </div>

                            <div className="grid grid-cols-3 gap-3 border-t border-teal-200/80 pt-4 dark:border-teal-800/60">
                                <div>
                                    <p className="text-xs font-medium text-slate-600 dark:text-slate-300">Poin Belajar</p>
                                    <p className="mt-0.5 text-lg font-bold text-slate-900 dark:text-white">{user?.points ?? 0} pt</p>
                                </div>
                                <div>
                                    <p className="text-xs font-medium text-slate-600 dark:text-slate-300">Rata-rata Pre-Test</p>
                                    <p className="mt-0.5 text-lg font-bold text-slate-900 dark:text-white">{avgPreScore}</p>
                                </div>
                                <div>
                                    <p className="text-xs font-medium text-slate-600 dark:text-slate-300">Modul Tuntas</p>
                                    <p className="mt-0.5 text-lg font-bold text-slate-900 dark:text-white">
                                        {posttestGate.completed_modules}/{posttestGate.total_modules}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="lg:col-span-5">
                        <CardHeader>
                            <div className="flex items-center justify-between gap-2">
                                <CardTitle className="flex items-center gap-2 text-base">
                                    <ClipboardCheckIcon className="size-5 text-teal-700 dark:text-teal-300" />
                                    Status Post-Test Akhir
                                </CardTitle>
                                {posttestGate.state === 'completed' && <Badge color="success">Selesai</Badge>}
                                {posttestGate.state === 'ready' && <Badge color="primary" variant="solid">Siap Dikerjakan</Badge>}
                                {posttestGate.state === 'waiting_delay' && <Badge color="warning">Masa Jeda 3 Hari</Badge>}
                                {posttestGate.state === 'locked_incomplete' && <Badge color="gray">Belum Terbuka</Badge>}
                            </div>
                            <CardDescription>
                                Pengukuran peningkatan pengetahuan total (indeks N-Gain).
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="flex flex-1 flex-col justify-between gap-4">
                            {posttestGate.state === 'completed' && posttestGate.latest_posttest && (
                                <div className="rounded-xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-800 dark:bg-emerald-950/40">
                                    <div className="grid grid-cols-3 gap-2 text-center">
                                        <div>
                                            <p className="text-xs text-slate-600 dark:text-slate-300">Skor Akhir</p>
                                            <p className="text-lg font-bold text-emerald-900 dark:text-emerald-200">
                                                {posttestGate.latest_posttest.total_score}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-xs text-slate-600 dark:text-slate-300">N-Gain</p>
                                            <p className="text-lg font-bold text-emerald-900 dark:text-emerald-200">
                                                {posttestGate.latest_posttest.n_gain_score ?? 0}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-xs text-slate-600 dark:text-slate-300">Kategori</p>
                                            <p className="text-sm font-bold text-emerald-900 dark:text-emerald-200">
                                                {posttestGate.latest_posttest.effectiveness_category}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {posttestGate.state === 'waiting_delay' && (
                                <div className="rounded-xl border border-amber-300 bg-amber-50 p-4 dark:border-amber-700 dark:bg-amber-950/50">
                                    <p className="text-xs font-semibold text-amber-950 dark:text-amber-200">
                                        Seluruh modul selesai! Post-Test dibuka pada {formatDateId(posttestGate.posttest_available_at, true)}:
                                    </p>
                                    <p className="mt-1.5 font-mono text-base font-bold text-amber-950 dark:text-amber-100">
                                        {countdown.label}
                                    </p>
                                </div>
                            )}

                            {posttestGate.state === 'ready' && (
                                <p className="text-sm text-slate-700 dark:text-slate-200">
                                    Masa jeda 3 hari telah terpenuhi. Anda dapat mengerjakan kuesioner Post-Test akhir sekarang.
                                </p>
                            )}

                            {posttestGate.state === 'locked_incomplete' && (
                                <p className="text-sm text-slate-600 dark:text-slate-300">
                                    Tuntaskan {posttestGate.total_topics - posttestGate.completed_topics} subbab lagi pada daftar modul agar penghitung waktu 3 hari menuju Post-Test dimulai.
                                </p>
                            )}

                            <Link
                                href="/posttest"
                                className={cn(
                                    buttonVariants({
                                        variant: posttestGate.state === 'ready' ? 'default' : 'outline',
                                    }),
                                    'w-full',
                                )}
                            >
                                {posttestGate.state === 'ready' ? 'Kerjakan Post-Test Sekarang' : 'Lihat Detail Status Post-Test'}
                            </Link>
                        </CardContent>
                    </Card>
                </section>

                <section>
                    <div className="mb-4 flex items-center justify-between gap-2">
                        <h2 className="text-lg font-bold text-slate-900 dark:text-white">Modul Pembelajaran Flipbook PDF</h2>
                        <Link href="/modules" className="text-sm font-semibold text-teal-700 hover:underline dark:text-teal-300">
                            Buka Semua Modul
                        </Link>
                    </div>

                    {modules.length === 0 ? (
                        <EmptyState title="Belum ada modul edukasi" description="Pengelola belum menerbitkan modul pembelajaran." />
                    ) : (
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            {modules.map((mod) => {
                                const pct = mod.topics_count > 0 ? Math.round((mod.completed_topics / mod.topics_count) * 100) : 0;
                                const isDone = mod.topics_count > 0 && mod.completed_topics >= mod.topics_count;
                                return (
                                    <Card key={mod.id} size="sm" className="justify-between">
                                        <div className="flex flex-col gap-3">
                                            <div className="flex items-start justify-between gap-2">
                                                <Badge color={isDone ? 'success' : mod.has_pretest ? 'primary' : 'warning'}>
                                                    {isDone ? 'Modul Tuntas' : mod.has_pretest ? 'Materi Terbuka' : 'Wajib Pre-Test'}
                                                </Badge>
                                                <span className="inline-flex items-center gap-1 text-xs font-medium text-slate-600 dark:text-slate-300">
                                                    <ClockIcon className="size-3.5" />
                                                    {mod.estimated_time || '15 Menit'}
                                                </span>
                                            </div>
                                            <div>
                                                <p className="text-xs font-bold text-teal-700 dark:text-teal-300">Modul {mod.module_number}</p>
                                                <h3 className="mt-0.5 text-base font-bold text-slate-900 dark:text-white">{mod.title}</h3>
                                            </div>
                                            <div className="flex flex-col gap-1.5">
                                                <div className="flex justify-between text-xs text-slate-600 dark:text-slate-300">
                                                    <span>
                                                        {mod.completed_topics}/{mod.topics_count} subbab selesai
                                                    </span>
                                                    <span>{pct}%</span>
                                                </div>
                                                <ProgressBar value={pct} label={`Progres Modul ${mod.module_number}`} />
                                            </div>
                                        </div>

                                        <div className="flex justify-end pt-2">
                                            {mod.has_pretest ? (
                                                <Link
                                                    href={`/modules/${mod.id}`}
                                                    className={buttonVariants({ variant: isDone ? 'outline' : 'default', size: 'sm' })}
                                                >
                                                    {isDone ? (
                                                        <>
                                                            <CheckIcon />
                                                            Ulas Kembali Materi
                                                        </>
                                                    ) : (
                                                        <>
                                                            <BookOpenIcon />
                                                            Baca Flipbook
                                                        </>
                                                    )}
                                                </Link>
                                            ) : (
                                                <Link
                                                    href={`/modules/${mod.id}/pretest`}
                                                    className={buttonVariants({ variant: 'default', size: 'sm' })}
                                                >
                                                    <LockIcon />
                                                    Mulai Pre-Test Modul {mod.module_number}
                                                </Link>
                                            )}
                                        </div>
                                    </Card>
                                );
                            })}
                        </div>
                    )}
                </section>

                <section className="grid grid-cols-1 gap-4 lg:grid-cols-12">
                    <Card className="lg:col-span-5">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2 text-base">
                                <HeartPulseIcon className="size-5 text-rose-700 dark:text-rose-300" />
                                Pemantauan Mandiri &amp; Gizi
                            </CardTitle>
                            <CardDescription>
                                Ringkasan pencatatan terakhir Anda pada instrumen Self-Care.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="flex flex-col gap-3">
                            {user?.gender === 'P' && (
                                <div className="rounded-lg border border-slate-200 p-3.5 dark:border-slate-700">
                                    <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Siklus Haid Terakhir</p>
                                    {latestPeriod ? (
                                        <p className="mt-1 text-sm font-bold text-slate-900 dark:text-white">
                                            Mulai {latestPeriod.start_date} &middot; Siklus {latestPeriod.cycle_length} hari (NRS {latestPeriod.nrs_pain_score}/10)
                                        </p>
                                    ) : (
                                        <p className="mt-1 text-xs text-slate-600 dark:text-slate-300">Belum ada catatan siklus menstruasi.</p>
                                    )}
                                </div>
                            )}

                            <div className="rounded-lg border border-slate-200 p-3.5 dark:border-slate-700">
                                <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Indeks Massa Tubuh (IMT)</p>
                                {latestBmi ? (
                                    <p className="mt-1 text-sm font-bold text-slate-900 dark:text-white">
                                        {latestBmi.bmi_value} kg/m&sup2; ({latestBmi.category})
                                    </p>
                                ) : (
                                    <p className="mt-1 text-xs text-slate-600 dark:text-slate-300">Belum ada pengukuran IMT tercatat.</p>
                                )}
                            </div>

                            <div className="flex flex-wrap gap-2 pt-1">
                                <Link href="/selfcare" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                                    Buka Instrumen Self-Care
                                </Link>
                                <Link href="/trivia" className={buttonVariants({ variant: 'ghost', size: 'sm' })}>
                                    <SparklesIcon />
                                    Trivia Harian
                                </Link>
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="lg:col-span-7">
                        <CardHeader>
                            <div className="flex items-center justify-between gap-2">
                                <CardTitle className="flex items-center gap-2 text-base">
                                    <MessageSquareIcon className="size-5 text-teal-700 dark:text-teal-300" />
                                    Diskusi Anonim &amp; Studi Kasus
                                </CardTitle>
                                <Link href="/forum" className="text-xs font-semibold text-teal-700 hover:underline dark:text-teal-300">
                                    Ke Forum
                                </Link>
                            </div>
                        </CardHeader>
                        <CardContent className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="flex flex-col gap-2.5">
                                <p className="text-xs font-bold tracking-wider text-slate-600 uppercase dark:text-slate-300">Pertanyaan Terbaru</p>
                                {recentThreads.length === 0 ? (
                                    <p className="text-xs text-slate-600 dark:text-slate-300">Belum ada diskusi di forum.</p>
                                ) : (
                                    recentThreads.map((thread) => (
                                        <Link
                                            key={thread.id}
                                            href={`/forum/${thread.id}`}
                                            className="rounded-lg border border-slate-200 p-3 hover:border-teal-700 dark:border-slate-700 dark:hover:border-teal-400"
                                        >
                                            <p className="line-clamp-1 text-sm font-semibold text-slate-900 dark:text-white">{thread.title}</p>
                                            <p className="mt-1 text-xs text-slate-600 dark:text-slate-300">
                                                {thread.topic} &middot; {thread.replies_count} balasan
                                            </p>
                                        </Link>
                                    ))
                                )}
                            </div>

                            <div className="flex flex-col gap-2.5">
                                <p className="text-xs font-bold tracking-wider text-slate-600 uppercase dark:text-slate-300">Studi Kasus Nyata</p>
                                {caseStudies.length === 0 ? (
                                    <p className="text-xs text-slate-600 dark:text-slate-300">Belum ada studi kasus.</p>
                                ) : (
                                    caseStudies.map((cs) => (
                                        <Link
                                            key={cs.id}
                                            href={`/cases/${cs.id}`}
                                            className="rounded-lg border border-slate-200 p-3 hover:border-teal-700 dark:border-slate-700 dark:hover:border-teal-400"
                                        >
                                            <p className="line-clamp-1 text-sm font-semibold text-slate-900 dark:text-white">{cs.title}</p>
                                            <p className="mt-1 inline-flex items-center gap-1 text-xs text-teal-700 dark:text-teal-300">
                                                <FileTextIcon className="size-3.5" />
                                                {cs.category}
                                            </p>
                                        </Link>
                                    ))
                                )}
                            </div>
                        </CardContent>
                    </Card>
                </section>

                {posters.length > 0 && (
                    <section>
                        <div className="mb-3 flex items-center justify-between">
                            <h2 className="text-base font-bold text-slate-900 dark:text-white">Poster Edukasi Kaspro Pilihan</h2>
                            <Link href="/posters" className="text-xs font-semibold text-teal-700 hover:underline dark:text-teal-300">
                                Galeri Poster
                            </Link>
                        </div>
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            {posters.map((poster) => (
                                <Card key={poster.id} size="sm">
                                    <Badge color="info" size="sm">{poster.category}</Badge>
                                    <h3 className="text-sm font-bold text-slate-900 dark:text-white">{poster.title}</h3>
                                    <p className="line-clamp-2 text-xs text-slate-600 dark:text-slate-300">{poster.caption}</p>
                                </Card>
                            ))}
                        </div>
                    </section>
                )}
            </div>
        </AppLayout>
    );
}
