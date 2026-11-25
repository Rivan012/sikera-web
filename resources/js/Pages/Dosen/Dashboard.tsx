import { Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    TableWrapper,
} from '@/components/ui/table';
import { BookOpenIcon, MessageSquareIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

interface FakultasRow {
    fakultas: string | null;
    total_mhs: number;
    pre_done: number;
    post_done: number;
}

interface DosenModule {
    id: number;
    module_number: number;
    title: string;
    topics_count: number;
}

interface DosenThread {
    id: number;
    title: string;
    topic: string;
    replies_count: number;
}

interface DosenCase {
    id: number;
    title: string;
    category: string;
}

interface DosenDashboardProps {
    totalMahasiswa: number;
    pretestDone: number;
    posttestDone: number;
    avgPre: number;
    avgPost: number;
    avgNGain: number;
    fakultasBreakdown: FakultasRow[];
    modules: DosenModule[];
    unansweredThreads: DosenThread[];
    caseStudies: DosenCase[];
}

export default function Dashboard({
    totalMahasiswa,
    pretestDone,
    posttestDone,
    avgPre,
    avgPost,
    avgNGain,
    fakultasBreakdown,
    modules,
    unansweredThreads,
    caseStudies,
}: DosenDashboardProps) {
    return (
        <AppLayout title="Dashboard Dosen Pembimbing Akademik">
            <Head title="Dashboard Dosen PA" />

            <div className="flex flex-col gap-6">
                <section className="grid grid-cols-2 gap-4 lg:grid-cols-6">
                    {[
                        { label: 'Mahasiswa Terdaftar', value: totalMahasiswa },
                        { label: 'Pre-Test Aktif', value: pretestDone },
                        { label: 'Post-Test Selesai', value: posttestDone },
                        { label: 'Rata-rata Pre-Test', value: avgPre },
                        { label: 'Rata-rata Post-Test', value: avgPost },
                        { label: 'Rata-rata N-Gain', value: avgNGain },
                    ].map((stat) => (
                        <Card key={stat.label} size="sm">
                            <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">{stat.label}</p>
                            <p className="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{stat.value}</p>
                        </Card>
                    ))}
                </section>

                <section className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <div className="flex flex-col gap-3 lg:col-span-7">
                        <h2 className="text-base font-bold text-slate-900 dark:text-white">Sebaran Partisipasi per Fakultas</h2>
                        {fakultasBreakdown.length === 0 ? (
                            <EmptyState title="Belum ada data responden mahasiswa" />
                        ) : (
                            <TableWrapper>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Fakultas</TableHead>
                                            <TableHead>Mahasiswa</TableHead>
                                            <TableHead>Pre-Test</TableHead>
                                            <TableHead>Post-Test</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {fakultasBreakdown.map((row, idx) => (
                                            <TableRow key={(row.fakultas || 'lainnya') + idx}>
                                                <TableCell className="font-medium">{row.fakultas || 'Belum diisi'}</TableCell>
                                                <TableCell>{row.total_mhs}</TableCell>
                                                <TableCell>{row.pre_done}</TableCell>
                                                <TableCell>{row.post_done}</TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </TableWrapper>
                        )}
                    </div>

                    <Card className="lg:col-span-5">
                        <CardHeader>
                            <div className="flex items-center justify-between gap-2">
                                <CardTitle className="flex items-center gap-2 text-base">
                                    <MessageSquareIcon className="size-5 text-teal-700 dark:text-teal-300" />
                                    Pertanyaan Menunggu Tanggapan
                                </CardTitle>
                                <Link href="/forum" className="text-xs font-semibold text-teal-700 hover:underline dark:text-teal-300">
                                    Semua Diskusi
                                </Link>
                            </div>
                            <CardDescription>
                                Bantu jawab konsultasi anonim mahasiswa bimbingan akademik.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="flex flex-col gap-2.5">
                            {unansweredThreads.length === 0 ? (
                                <p className="text-sm text-slate-600 dark:text-slate-300">
                                    Seluruh pertanyaan mahasiswa telah mendapat tanggapan konselor/dosen.
                                </p>
                            ) : (
                                unansweredThreads.map((t) => (
                                    <Link
                                        key={t.id}
                                        href={`/forum/${t.id}`}
                                        className="flex items-center justify-between gap-3 rounded-lg border border-slate-200 p-3 hover:border-teal-700 dark:border-slate-700 dark:hover:border-teal-400"
                                    >
                                        <div className="min-w-0">
                                            <p className="truncate text-sm font-semibold text-slate-900 dark:text-white">{t.title}</p>
                                            <p className="text-xs text-slate-600 dark:text-slate-300">{t.topic}</p>
                                        </div>
                                        <Badge color="warning" size="sm">Jawab</Badge>
                                    </Link>
                                ))
                            )}
                        </CardContent>
                    </Card>
                </section>

                <section className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <Card size="sm">
                        <CardHeader>
                            <CardTitle className="text-base">Akses Materi Modul Flipbook</CardTitle>
                        </CardHeader>
                        <CardContent className="flex flex-col gap-2">
                            {modules.map((m) => (
                                <div key={m.id} className="flex items-center justify-between gap-2 rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                                    <div className="min-w-0">
                                        <p className="text-xs font-bold text-teal-700 dark:text-teal-300">Modul {m.module_number}</p>
                                        <p className="truncate text-sm font-semibold text-slate-900 dark:text-white">{m.title}</p>
                                    </div>
                                    <Link href={`/modules/${m.id}`} className={buttonVariants({ variant: 'outline', size: 'xs' })}>
                                        <BookOpenIcon />
                                        Tinjau ({m.topics_count})
                                    </Link>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card size="sm">
                        <CardHeader>
                            <CardTitle className="text-base">Referensi Studi Kasus Kampus</CardTitle>
                        </CardHeader>
                        <CardContent className="flex flex-col gap-2">
                            {caseStudies.slice(0, 4).map((cs) => (
                                <Link
                                    key={cs.id}
                                    href={`/cases/${cs.id}`}
                                    className="flex items-center justify-between gap-2 rounded-lg border border-slate-200 p-3 hover:border-teal-700 dark:border-slate-700 dark:hover:border-teal-400"
                                >
                                    <span className="truncate text-sm font-semibold text-slate-900 dark:text-white">{cs.title}</span>
                                    <Badge color="info" size="sm">{cs.category}</Badge>
                                </Link>
                            ))}
                        </CardContent>
                    </Card>
                </section>
            </div>
        </AppLayout>
    );
}
