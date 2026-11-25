import { Head, Link } from '@inertiajs/react';
import { lazy, Suspense } from 'react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    TableWrapper,
} from '@/components/ui/table';
import { useTheme } from '@/contexts/theme-context';
import { ClipboardCheckIcon, ClipboardListIcon, DownloadIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

const ReactApexChart = lazy(() => import('react-apexcharts'));

interface ModuleScoreRow {
    modul: string;
    pre: number;
    post: number;
}

interface RecentEvalRow {
    id: number;
    type: 'pre_test' | 'post_test';
    module_number: number | null;
    total_score: number;
    n_gain_score: number | null;
    submitted_at: string | null;
    user_name: string;
    user_nim: string;
}

interface AdminDashboardProps {
    totalMahasiswa: number;
    pretestDone: number;
    posttestDone: number;
    totalDosen: number;
    participationRate: number;
    avgNGain: number;
    nGainCategory: string;
    avgPreScore: number;
    avgPostScore: number;
    scoresByModule: ModuleScoreRow[];
    fakultasStats: { fakultas: string | null; total: number }[];
    recentEvaluations: RecentEvalRow[];
    totalPretestQuestions: number;
    totalPosttestQuestions: number;
    totalThreads: number;
    unansweredThreads: number;
    totalPeriodLogs: number;
    totalDailyActivities: number;
}

export default function Dashboard({
    totalMahasiswa,
    pretestDone,
    posttestDone,
    totalDosen,
    participationRate,
    avgNGain,
    nGainCategory,
    avgPreScore,
    avgPostScore,
    scoresByModule,
    fakultasStats,
    recentEvaluations,
    totalPretestQuestions,
    totalPosttestQuestions,
    totalThreads,
    unansweredThreads,
}: AdminDashboardProps) {
    const { theme } = useTheme();
    const isDark = theme === 'dark';

    const chartOptions: ApexCharts.ApexOptions = {
        chart: {
            type: 'bar',
            toolbar: { show: false },
            fontFamily: 'Plus Jakarta Sans, sans-serif',
            background: 'transparent',
        },
        colors: ['#0f766e', '#10b981'],
        plotOptions: {
            bar: { horizontal: false, columnWidth: '48%', borderRadius: 6 },
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: scoresByModule.map((row) => row.modul),
        },
        yaxis: {
            min: 0,
            max: 100,
            title: { text: 'Skor Rata-rata (0 sampai 100)' },
        },
        legend: { position: 'top' },
        theme: { mode: isDark ? 'dark' : 'light' },
    };

    const chartSeries = [
        { name: 'Pre-Test per Modul', data: scoresByModule.map((row) => row.pre) },
        { name: 'Post-Test Akhir Umum', data: scoresByModule.map((row) => row.post) },
    ];

    return (
        <AppLayout
            title="Dashboard Pengelola Riset SIKERA"
            actions={
                <div className="flex flex-wrap gap-2">
                    <Link href="/admin/pretest-questions" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                        <ClipboardListIcon />
                        Kelola Pre-Test ({totalPretestQuestions})
                    </Link>
                    <Link href="/admin/posttest-questions" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                        <ClipboardCheckIcon />
                        Kelola Post-Test ({totalPosttestQuestions})
                    </Link>
                    <a href="/admin/evaluations/export" className={buttonVariants({ variant: 'default', size: 'sm' })}>
                        <DownloadIcon />
                        Ekspor Excel Post-Test
                    </a>
                </div>
            }
        >
            <Head title="Dashboard Admin" />

            <div className="flex flex-col gap-6">
                <section className="grid grid-cols-1 gap-4 lg:grid-cols-12">
                    <Card className="border-teal-200 bg-teal-50/70 lg:col-span-5 dark:border-teal-800 dark:bg-teal-950/40">
                        <CardHeader>
                            <div className="flex items-center justify-between gap-2">
                                <Badge color="primary">Efektivitas Intervensi</Badge>
                                <Badge color={nGainCategory === 'Tinggi' ? 'success' : nGainCategory === 'Sedang' ? 'warning' : 'gray'}>
                                    Kategori {nGainCategory}
                                </Badge>
                            </div>
                            <CardTitle className="mt-2 text-3xl font-bold tabular-nums">{avgNGain}</CardTitle>
                            <CardDescription className="text-slate-700 dark:text-slate-200">
                                Rata-rata indeks N-Gain total (perbandingan rata-rata Pre-Test seluruh modul terhadap skor total Post-Test berjeda 3 hari).
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="grid grid-cols-2 gap-3 border-t border-teal-200/80 pt-4 dark:border-teal-800/60">
                            <div>
                                <p className="text-xs font-medium text-slate-600 dark:text-slate-300">Rata-rata Pre-Test</p>
                                <p className="text-xl font-bold text-slate-900 dark:text-white">{avgPreScore}</p>
                            </div>
                            <div>
                                <p className="text-xs font-medium text-slate-600 dark:text-slate-300">Rata-rata Post-Test</p>
                                <p className="text-xl font-bold text-slate-900 dark:text-white">{avgPostScore}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:col-span-7">
                        {[
                            { label: 'Mahasiswa Responden', value: totalMahasiswa, sub: `${participationRate}% partisipasi` },
                            { label: 'Pre-Test Dikerjakan', value: pretestDone, sub: `${totalPretestQuestions} soal modul` },
                            { label: 'Post-Test Tuntas', value: posttestDone, sub: `${totalPosttestQuestions} soal umum` },
                            { label: 'Dosen PA Terdaftar', value: totalDosen, sub: 'Konselor akademik' },
                            { label: 'Diskusi Forum', value: totalThreads, sub: `${unansweredThreads} belum dijawab` },
                            { label: 'Sebaran Fakultas', value: fakultasStats.length, sub: 'Afiliasi aktif' },
                        ].map((card) => (
                            <Card key={card.label} size="sm" className="justify-between">
                                <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">{card.label}</p>
                                <div>
                                    <p className="text-2xl font-bold text-slate-900 dark:text-white">{card.value}</p>
                                    <p className="mt-0.5 text-xs text-slate-600 dark:text-slate-300">{card.sub}</p>
                                </div>
                            </Card>
                        ))}
                    </div>
                </section>

                <section className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <Card className="lg:col-span-8">
                        <CardHeader>
                            <CardTitle>Grafik Perbandingan Skor Pre-Test Modul &amp; Post-Test Akhir</CardTitle>
                            <CardDescription>
                                Rata-rata skor awal per modul dibandingkan rata-rata skor Post-Test akhir responden.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {scoresByModule.length === 0 ? (
                                <p className="py-10 text-center text-sm text-slate-600 dark:text-slate-300">
                                    Belum ada modul untuk ditampilkan pada grafik.
                                </p>
                            ) : (
                                <Suspense fallback={<Skeleton className="h-72 w-full" />}>
                                    <ReactApexChart options={chartOptions} series={chartSeries} type="bar" height={300} />
                                </Suspense>
                            )}
                        </CardContent>
                    </Card>

                    <Card className="lg:col-span-4">
                        <CardHeader>
                            <CardTitle>Partisipasi per Fakultas</CardTitle>
                        </CardHeader>
                        <CardContent className="flex flex-col gap-2.5">
                            {fakultasStats.length === 0 ? (
                                <p className="text-sm text-slate-600 dark:text-slate-300">Belum ada responden yang mengerjakan Pre-Test.</p>
                            ) : (
                                fakultasStats.map((item, idx) => (
                                    <div
                                        key={(item.fakultas || 'lain') + idx}
                                        className="flex items-center justify-between gap-2 rounded-lg border border-slate-200 p-3 text-xs dark:border-slate-700"
                                    >
                                        <span className="truncate font-semibold text-slate-800 dark:text-slate-100">
                                            {item.fakultas || 'Belum diisi'}
                                        </span>
                                        <Badge color="primary" size="sm">{item.total} mhs</Badge>
                                    </div>
                                ))
                            )}
                        </CardContent>
                    </Card>
                </section>

                <section className="flex flex-col gap-3">
                    <div className="flex items-center justify-between gap-2">
                        <h2 className="text-base font-bold text-slate-900 dark:text-white">Pengiriman Evaluasi Terbaru</h2>
                        <Link href="/admin/evaluations" className="text-xs font-semibold text-teal-700 hover:underline dark:text-teal-300">
                            Lihat Semua Jawaban
                        </Link>
                    </div>

                    <TableWrapper>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Mahasiswa</TableHead>
                                    <TableHead>Jenis Evaluasi</TableHead>
                                    <TableHead>Skor</TableHead>
                                    <TableHead>N-Gain</TableHead>
                                    <TableHead>Waktu Kirim</TableHead>
                                    <TableHead>Detail</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {recentEvaluations.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={6} className="py-8 text-center text-slate-600 dark:text-slate-300">
                                            Belum ada data pengiriman evaluasi.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    recentEvaluations.map((ev) => (
                                        <TableRow key={ev.id}>
                                            <TableCell>
                                                <div className="font-semibold">{ev.user_name}</div>
                                                <div className="text-xs text-slate-600 dark:text-slate-300">{ev.user_nim}</div>
                                            </TableCell>
                                            <TableCell>
                                                {ev.type === 'post_test' ? (
                                                    <Badge color="success" size="sm">Post-Test Akhir</Badge>
                                                ) : (
                                                    <Badge color="info" size="sm">
                                                        Pre-Test {ev.module_number ? `Modul ${ev.module_number}` : 'Umum'}
                                                    </Badge>
                                                )}
                                            </TableCell>
                                            <TableCell className="font-bold">{ev.total_score}</TableCell>
                                            <TableCell>{ev.n_gain_score ?? '-'}</TableCell>
                                            <TableCell className="text-xs">{ev.submitted_at || '-'}</TableCell>
                                            <TableCell>
                                                <Link href={`/admin/evaluations/${ev.id}`} className="text-xs font-semibold text-teal-700 hover:underline dark:text-teal-300">
                                                    Buka Lembar
                                                </Link>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </TableWrapper>
                </section>
            </div>
        </AppLayout>
    );
}
