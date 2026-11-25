import { Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { Pagination } from '@/components/ui/pagination';
import { formatDateId } from '@/constants/akademik';
import { MessageSquareIcon, PlusIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { PaginatedData } from '@/types/sikera';

interface ForumThreadItem {
    id: number;
    anonymous_alias: string;
    title: string;
    topic: string;
    content: string;
    is_answered_by_counselor: boolean;
    replies_count: number;
    created_at: string;
}

interface ForumIndexProps {
    threads: PaginatedData<ForumThreadItem>;
}

export default function Index({ threads }: ForumIndexProps) {
    return (
        <AppLayout
            title="Forum Konsultasi Anonim"
            actions={
                <Link href="/forum/create" className={buttonVariants({ variant: 'default', size: 'sm' })}>
                    <PlusIcon />
                    Tulis Pertanyaan Anonim
                </Link>
            }
        >
            <Head title="Forum Konsultasi Anonim" />

            <div className="flex flex-col gap-5">
                {threads.data.length === 0 ? (
                    <EmptyState
                        icon={<MessageSquareIcon className="size-8" />}
                        title="Belum ada diskusi di forum"
                        description="Ajukan pertanyaan seputar kesehatan reproduksi secara anonim untuk ditanggapi konselor dan dosen PA."
                        action={
                            <Link href="/forum/create" className={buttonVariants({ variant: 'default', size: 'sm' })}>
                                <PlusIcon />
                                Buat Pertanyaan Pertama
                            </Link>
                        }
                    />
                ) : (
                    <div className="flex flex-col gap-3">
                        {threads.data.map((thread) => (
                            <Card key={thread.id} size="sm">
                                <Link href={`/forum/${thread.id}`} className="group block">
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <Badge color={thread.topic === 'Darurat' ? 'error' : 'primary'} size="sm">
                                                {thread.topic}
                                            </Badge>
                                            <span className="text-xs font-medium text-slate-600 dark:text-slate-300">
                                                Oleh {thread.anonymous_alias} &middot; {formatDateId(thread.created_at)}
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            {thread.is_answered_by_counselor && (
                                                <Badge color="success" size="sm">Dijawab Konselor</Badge>
                                            )}
                                            <Badge color="gray" size="sm">{thread.replies_count} Balasan</Badge>
                                        </div>
                                    </div>
                                    <h2 className="mt-2 text-base font-bold text-slate-900 group-hover:text-teal-700 dark:text-white dark:group-hover:text-teal-300">
                                        {thread.title}
                                    </h2>
                                    <p className="mt-1 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{thread.content}</p>
                                </Link>
                            </Card>
                        ))}
                    </div>
                )}

                <Pagination links={threads.links} />
            </div>
        </AppLayout>
    );
}
