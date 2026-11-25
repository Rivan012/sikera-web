import { Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { ProgressBar } from '@/components/ui/progress-bar';
import { formatDateId } from '@/constants/akademik';
import { BookOpenIcon, CheckIcon, ChevronLeftIcon, ClockIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { PosttestGate } from '@/types/sikera';

interface ShowTopicItem {
    id: number;
    topic_code: string;
    title: string;
    has_pdf: boolean;
    pdf_size_label: string | null;
    has_video: boolean;
    is_completed: boolean;
}

interface ModuleShowProps {
    module: {
        id: number;
        module_number: number;
        title: string;
        subtitle: string | null;
        description: string | null;
        banner_image: string;
        estimated_time: string | number | null;
        topics_count: number;
        pdf_count: number;
    };
    topics: ShowTopicItem[];
    allTopicsDone: boolean;
    posttestGate: PosttestGate;
}

export default function Show({ module, topics, allTopicsDone, posttestGate }: ModuleShowProps) {
    const doneCount = topics.filter((t) => t.is_completed).length;
    const pct = topics.length > 0 ? Math.round((doneCount / topics.length) * 100) : 0;

    return (
        <AppLayout
            title={`Modul ${module.module_number}: ${module.title}`}
            actions={
                <Link href="/modules" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                    <ChevronLeftIcon />
                    Kembali ke Katalog Modul
                </Link>
            }
        >
            <Head title={`Modul ${module.module_number}`} />

            <div className="flex flex-col gap-6">
                <Card className="overflow-hidden p-0 sm:p-0">
                    <div className="h-44 w-full overflow-hidden border-b border-slate-200 bg-slate-100 sm:h-52 dark:border-slate-700 dark:bg-slate-900">
                        <img
                            src={module.banner_image}
                            alt={`Sampul Modul ${module.module_number}`}
                            width={960}
                            height={300}
                            className="h-full w-full object-cover"
                        />
                    </div>
                    <CardContent className="flex flex-col gap-4 p-5 sm:p-6">
                        <div className="flex flex-wrap items-center gap-2">
                            <Badge color="primary">Modul {module.module_number}</Badge>
                            <Badge color="gray">{module.pdf_count} Flipbook PDF</Badge>
                            {allTopicsDone && <Badge color="success">Seluruh Subbab Tuntas</Badge>}
                            <span className="inline-flex items-center gap-1 text-xs font-medium text-slate-600 dark:text-slate-300">
                                <ClockIcon className="size-3.5" />
                                {module.estimated_time || '15 Menit'}
                            </span>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-slate-900 sm:text-2xl dark:text-white">{module.title}</h2>
                            {module.subtitle && (
                                <p className="mt-1 text-sm font-semibold text-teal-700 dark:text-teal-300">{module.subtitle}</p>
                            )}
                            {module.description && (
                                <p className="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{module.description}</p>
                            )}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <div className="flex justify-between text-xs font-semibold text-slate-700 dark:text-slate-200">
                                <span>
                                    Progres Bacaan ({doneCount}/{topics.length} subbab)
                                </span>
                                <span>{pct}%</span>
                            </div>
                            <ProgressBar value={pct} label={`Progres bacaan Modul ${module.module_number}`} />
                        </div>
                    </CardContent>
                </Card>

                <section>
                    <h2 className="mb-3 text-base font-bold text-slate-900 dark:text-white">Daftar Subbab Materi</h2>

                    {topics.length === 0 ? (
                        <EmptyState title="Belum ada subbab" description="Subbab PDF untuk modul ini belum diunggah." />
                    ) : (
                        <div className="flex flex-col gap-3">
                            {topics.map((topic) => (
                                <Card key={topic.id} size="sm" className="flex-col justify-between gap-4 sm:flex-row sm:items-center">
                                    <div className="flex min-w-0 items-start gap-3">
                                        <span className="mt-0.5 inline-flex min-w-11 items-center justify-center rounded-lg bg-teal-50 px-2.5 py-1.5 font-mono text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-200">
                                            {topic.topic_code}
                                        </span>
                                        <div className="min-w-0">
                                            <h3 className="text-sm font-bold text-slate-900 sm:text-base dark:text-white">{topic.title}</h3>
                                            <div className="mt-1.5 flex flex-wrap items-center gap-2">
                                                {topic.has_pdf ? (
                                                    <Badge color="info" size="sm">
                                                        Flipbook PDF {topic.pdf_size_label ? `(${topic.pdf_size_label})` : ''}
                                                    </Badge>
                                                ) : (
                                                    <Badge color="gray" size="sm">Teks Materi</Badge>
                                                )}
                                                {topic.has_video && <Badge color="warning" size="sm">Video Pendamping</Badge>}
                                                {topic.is_completed && <Badge color="success" size="sm">Sudah Dibaca</Badge>}
                                            </div>
                                        </div>
                                    </div>

                                    <Link
                                        href={`/modules/${module.id}/topic/${topic.id}`}
                                        className={buttonVariants({
                                            variant: topic.is_completed ? 'outline' : 'default',
                                            size: 'sm',
                                        })}
                                    >
                                        {topic.is_completed ? <CheckIcon /> : <BookOpenIcon />}
                                        {topic.is_completed ? 'Baca Ulang' : 'Buka Flipbook'}
                                    </Link>
                                </Card>
                            ))}
                        </div>
                    )}
                </section>

                {allTopicsDone && (
                    <Card size="sm" className="border-teal-200 bg-teal-50/70 dark:border-teal-800 dark:bg-teal-950/40">
                        <CardContent className="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                            <div>
                                <p className="text-sm font-bold text-slate-900 dark:text-white">
                                    Modul {module.module_number} telah selesai dipelajari!
                                </p>
                                {posttestGate.all_modules_completed ? (
                                    <p className="mt-0.5 text-xs text-slate-700 dark:text-slate-200">
                                        {posttestGate.is_posttest_unlocked
                                            ? 'Seluruh modul tuntas dan jeda 3 hari terpenuhi. Anda dapat mengerjakan Post-Test akhir.'
                                            : `Seluruh modul tuntas! Post-Test akhir akan terbuka pada ${formatDateId(posttestGate.posttest_available_at, true)}.`}
                                    </p>
                                ) : (
                                    <p className="mt-0.5 text-xs text-slate-700 dark:text-slate-200">
                                        Lanjutkan mempelajari modul lainnya ({posttestGate.completed_modules}/{posttestGate.total_modules} modul selesai) untuk mengaktifkan jadwal Post-Test akhir.
                                    </p>
                                )}
                            </div>
                            <Link
                                href={posttestGate.all_modules_completed ? '/posttest' : '/modules'}
                                className={buttonVariants({ variant: 'default', size: 'sm' })}
                            >
                                {posttestGate.all_modules_completed ? 'Cek Post-Test Akhir' : 'Lanjut ke Modul Berikutnya'}
                            </Link>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}
