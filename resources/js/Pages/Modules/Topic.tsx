import { Head, Link } from '@inertiajs/react';
import { lazy, Suspense } from 'react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { formatDateId } from '@/constants/akademik';
import { ChevronLeftIcon, ChevronRightIcon, DownloadIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { PosttestGate } from '@/types/sikera';
import { cn } from '@/utils';

const PdfFlipbookViewer = lazy(() => import('@/components/flipbook/PdfFlipbookViewer'));

interface TopicNavItem {
    id: number;
    topic_code: string;
    title: string;
}

interface ModuleTopicPageProps {
    module: {
        id: number;
        module_number: number;
        title: string;
    };
    topic: {
        id: number;
        topic_code: string;
        title: string;
        pdf_original_name: string | null;
        pdf_size_label: string | null;
        pdf_version?: string;
        youtube_video_id: string | null;
        content_html: string | null;
    };
    allTopics: TopicNavItem[];
    prevTopic: TopicNavItem | null;
    nextTopic: TopicNavItem | null;
    hasPdfFile: boolean;
    pdfUrl: string | null;
    posttestGate: PosttestGate;
}

export default function Topic({
    module,
    topic,
    allTopics,
    prevTopic,
    nextTopic,
    hasPdfFile,
    pdfUrl,
    posttestGate,
}: ModuleTopicPageProps) {
    return (
        <AppLayout
            title={`Subbab ${topic.topic_code}: ${topic.title}`}
            actions={
                <div className="flex flex-wrap items-center gap-2">
                    <Link href={`/modules/${module.id}`} className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                        <ChevronLeftIcon />
                        Daftar Subbab Modul {module.module_number}
                    </Link>
                    {hasPdfFile && pdfUrl && (
                        <a
                            href={pdfUrl}
                            target="_blank"
                            rel="noopener noreferrer"
                            className={buttonVariants({ variant: 'outline', size: 'sm' })}
                        >
                            <DownloadIcon />
                            Buka PDF Asli
                        </a>
                    )}
                </div>
            }
        >
            <Head title={`${topic.topic_code} ${topic.title}`} />

            <div className="flex flex-col gap-6">
                <Card size="sm">
                    <CardContent className="flex flex-col gap-3">
                        <div className="flex flex-wrap items-center gap-2">
                            <Badge color="primary">Modul {module.module_number}</Badge>
                            <Badge color="info">Subbab {topic.topic_code}</Badge>
                            {topic.pdf_size_label && <Badge color="gray">{topic.pdf_size_label}</Badge>}
                        </div>
                        <h2 className="text-xl font-bold text-slate-900 dark:text-white">{topic.title}</h2>

                        {allTopics.length > 1 && (
                            <nav aria-label="Pindah cepat antar subbab" className="flex gap-1.5 overflow-x-auto pt-1 pb-1">
                                {allTopics.map((item) => {
                                    const current = item.id === topic.id;
                                    return (
                                        <Link
                                            key={item.id}
                                            href={`/modules/${module.id}/topic/${item.id}`}
                                            aria-current={current ? 'page' : undefined}
                                            className={cn(
                                                'inline-flex min-h-11 shrink-0 items-center rounded-lg border px-3 text-xs font-semibold transition-colors',
                                                current
                                                    ? 'border-teal-700 bg-teal-700 text-white dark:border-teal-400 dark:bg-teal-400 dark:text-slate-950'
                                                    : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700',
                                            )}
                                        >
                                            {item.topic_code}
                                        </Link>
                                    );
                                })}
                            </nav>
                        )}
                    </CardContent>
                </Card>

                {hasPdfFile && pdfUrl ? (
                    <Suspense
                        fallback={
                            <div className="flex min-h-96 flex-col items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900">
                                <Skeleton className="h-6 w-56" />
                                <Skeleton className="h-80 w-full max-w-xl" />
                            </div>
                        }
                    >
                        <PdfFlipbookViewer pdfUrl={pdfUrl} title={topic.title} downloadUrl={pdfUrl} version={topic.pdf_version} />
                    </Suspense>
                ) : topic.content_html ? (
                    <Card>
                        <CardContent>
                            <div
                                className="prose prose-slate max-w-none text-sm leading-relaxed text-slate-800 dark:prose-invert dark:text-slate-100"
                                dangerouslySetInnerHTML={{ __html: topic.content_html }}
                            />
                        </CardContent>
                    </Card>
                ) : (
                    <Card>
                        <CardContent>
                            <p className="text-sm text-slate-600 dark:text-slate-300">
                                Berkas PDF untuk subbab ini belum tersedia di server. Silakan hubungi pengelola modul.
                            </p>
                        </CardContent>
                    </Card>
                )}

                {topic.youtube_video_id && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base">Video Penjelasan Pendamping</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="aspect-video w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-950 dark:border-slate-700">
                                <iframe
                                    src={`https://www.youtube.com/embed/${topic.youtube_video_id}`}
                                    title={`Video materi ${topic.title}`}
                                    width={960}
                                    height={540}
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowFullScreen
                                    className="h-full w-full border-0"
                                />
                            </div>
                        </CardContent>
                    </Card>
                )}

                <div className="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    {prevTopic ? (
                        <Link
                            href={`/modules/${module.id}/topic/${prevTopic.id}`}
                            className={buttonVariants({ variant: 'outline' })}
                        >
                            <ChevronLeftIcon />
                            Subbab {prevTopic.topic_code}
                        </Link>
                    ) : (
                        <span />
                    )}

                    {nextTopic ? (
                        <Link
                            href={`/modules/${module.id}/topic/${nextTopic.id}`}
                            className={buttonVariants({ variant: 'default' })}
                        >
                            Lanjut Subbab {nextTopic.topic_code}
                            <ChevronRightIcon />
                        </Link>
                    ) : posttestGate.all_modules_completed ? (
                        <Link href="/posttest" className={buttonVariants({ variant: 'default' })}>
                            {posttestGate.is_posttest_unlocked
                                ? 'Lanjut ke Post-Test Akhir'
                                : `Status Post-Test (${formatDateId(posttestGate.posttest_available_at)})`}
                            <ChevronRightIcon />
                        </Link>
                    ) : (
                        <Link href="/modules" className={buttonVariants({ variant: 'default' })}>
                            Kembali ke Katalog Modul
                            <ChevronRightIcon />
                        </Link>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
