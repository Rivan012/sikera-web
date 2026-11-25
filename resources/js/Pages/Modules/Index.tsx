import { Head, Link, router } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { ProgressBar } from '@/components/ui/progress-bar';
import { formatDateId } from '@/constants/akademik';
import { useCountdown } from '@/hooks/use-countdown';
import { BookOpenIcon, CheckIcon, ClipboardCheckIcon, ClockIcon, LockIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { PosttestGate } from '@/types/sikera';

interface ModuleCatalogItem {
    id: number;
    module_number: number;
    title: string;
    subtitle: string | null;
    description: string | null;
    banner_image: string;
    estimated_time: string | number | null;
    completed: number;
    total: number;
    pdf_count: number;
    percentage: number;
    has_pretest: boolean;
    all_topics_done: boolean;
}

interface ModulesIndexProps {
    modules: ModuleCatalogItem[];
    posttestGate: PosttestGate;
}

export default function Index({ modules, posttestGate }: ModulesIndexProps) {
    const countdown = useCountdown(
        posttestGate.state === 'waiting_delay' ? posttestGate.posttest_available_at : null,
        posttestGate.remaining_seconds,
        () => router.reload({ only: ['posttestGate'] }),
    );

    return (
        <AppLayout title="Katalog Modul Flipbook PDF">
            <Head title="Modul Edukasi" />

            <div className="flex flex-col gap-6">
                <Card className="border-teal-200 bg-teal-50/60 dark:border-teal-800/80 dark:bg-teal-950/35" size="sm">
                    <CardContent className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div className="flex items-start gap-3">
                            <ClipboardCheckIcon className="mt-0.5 size-5 shrink-0 text-teal-700 dark:text-teal-300" />
                            <div>
                                <p className="text-sm font-bold text-slate-900 dark:text-white">
                                    Alur Evaluasi Bertahap SIKERA
                                </p>
                                {posttestGate.state === 'locked_incomplete' && (
                                    <p className="mt-0.5 text-xs text-slate-700 dark:text-slate-200">
                                        Kerjakan Pre-Test tiap modul sebelum membuka flipbook PDF ({posttestGate.completed_topics}/{posttestGate.total_topics} subbab selesai). Post-Test akhir akan terbuka 3 hari setelah seluruh modul tuntas.
                                    </p>
                                )}
                                {posttestGate.state === 'waiting_delay' && (
                                    <p className="mt-0.5 text-xs text-slate-700 dark:text-slate-200">
                                        Seluruh modul selesai! Post-Test akhir terbuka pada {formatDateId(posttestGate.posttest_available_at, true)} ({countdown.label}).
                                    </p>
                                )}
                                {posttestGate.state === 'ready' && (
                                    <p className="mt-0.5 text-xs text-slate-700 dark:text-slate-200">
                                        Masa jeda 3 hari telah selesai. Silakan lanjutkan mengerjakan Post-Test akhir terpadu.
                                    </p>
                                )}
                                {posttestGate.state === 'completed' && (
                                    <p className="mt-0.5 text-xs text-slate-700 dark:text-slate-200">
                                        Anda telah menuntaskan seluruh modul dan Post-Test akhir.
                                    </p>
                                )}
                            </div>
                        </div>
                        <Link
                            href="/posttest"
                            className={buttonVariants({
                                variant: posttestGate.state === 'ready' ? 'default' : 'outline',
                                size: 'sm',
                            })}
                        >
                            {posttestGate.state === 'ready' ? 'Mulai Post-Test Akhir' : 'Status Post-Test'}
                        </Link>
                    </CardContent>
                </Card>

                {modules.length === 0 ? (
                    <EmptyState
                        icon={<BookOpenIcon className="size-8" />}
                        title="Belum ada modul pembelajaran"
                        description="Modul flipbook PDF akan tampil di halaman ini setelah diunggah oleh pengelola."
                    />
                ) : (
                    <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                        {modules.map((mod) => (
                            <Card key={mod.id} className="overflow-hidden p-0 sm:p-0">
                                <div className="relative h-40 w-full overflow-hidden border-b border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-900">
                                    <img
                                        src={mod.banner_image}
                                        alt={`Sampul Modul ${mod.module_number}: ${mod.title}`}
                                        width={640}
                                        height={240}
                                        className="h-full w-full object-cover"
                                    />
                                </div>

                                <div className="flex flex-1 flex-col justify-between gap-4 p-5">
                                    <div className="flex flex-col gap-2.5">
                                        <div className="flex flex-wrap items-center justify-between gap-2">
                                            <div className="flex flex-wrap items-center gap-1.5">
                                                <Badge color="primary">Modul {mod.module_number}</Badge>
                                                <Badge color="gray">{mod.pdf_count} Flipbook PDF</Badge>
                                                {mod.all_topics_done ? (
                                                    <Badge color="success">Tuntas</Badge>
                                                ) : mod.has_pretest ? (
                                                    <Badge color="info">Pre-Test Selesai</Badge>
                                                ) : (
                                                    <Badge color="warning">Terkunci Pre-Test</Badge>
                                                )}
                                            </div>
                                            <span className="inline-flex items-center gap-1 text-xs font-medium text-slate-600 dark:text-slate-300">
                                                <ClockIcon className="size-3.5" />
                                                {mod.estimated_time || '15 Menit'}
                                            </span>
                                        </div>

                                        <div>
                                            <h2 className="text-lg font-bold text-slate-900 dark:text-white">{mod.title}</h2>
                                            {mod.subtitle && (
                                                <p className="mt-0.5 text-xs font-semibold text-teal-700 dark:text-teal-300">{mod.subtitle}</p>
                                            )}
                                            {mod.description && (
                                                <p className="mt-1.5 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{mod.description}</p>
                                            )}
                                        </div>
                                    </div>

                                    <div className="flex flex-col gap-3 border-t border-slate-200 pt-4 dark:border-slate-700">
                                        <div className="flex flex-col gap-1.5">
                                            <div className="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-300">
                                                <span>
                                                    {mod.completed} dari {mod.total} subbab selesai
                                                </span>
                                                <span>{mod.percentage}%</span>
                                            </div>
                                            <ProgressBar value={mod.percentage} label={`Progres Modul ${mod.module_number}`} />
                                        </div>

                                        <div className="flex justify-end">
                                            {mod.has_pretest ? (
                                                <Link
                                                    href={`/modules/${mod.id}`}
                                                    className={buttonVariants({ variant: mod.all_topics_done ? 'outline' : 'default', size: 'sm' })}
                                                >
                                                    {mod.all_topics_done ? <CheckIcon /> : <BookOpenIcon />}
                                                    {mod.all_topics_done ? 'Tinjau Subbab' : 'Buka Daftar Subbab'}
                                                </Link>
                                            ) : (
                                                <Link
                                                    href={`/modules/${mod.id}/pretest`}
                                                    className={buttonVariants({ variant: 'default', size: 'sm' })}
                                                >
                                                    <LockIcon />
                                                    Kerjakan Pre-Test Modul {mod.module_number}
                                                </Link>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
