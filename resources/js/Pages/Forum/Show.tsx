import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FieldError } from '@/components/ui/field-error';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { formatDateId } from '@/constants/akademik';
import { ChevronLeftIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

interface ForumReplyItem {
    id: number;
    display_name: string;
    role_badge: 'mahasiswa' | 'dosen_pa' | 'admin' | string;
    reply_content: string;
    created_at: string;
}

interface ForumShowProps {
    thread: {
        id: number;
        anonymous_alias: string;
        title: string;
        topic: string;
        content: string;
        is_answered_by_counselor: boolean;
        created_at: string;
        replies: ForumReplyItem[];
    };
}

export default function Show({ thread }: ForumShowProps) {
    const form = useForm({
        reply_content: '',
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        form.post(`/forum/${thread.id}/reply`, {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    return (
        <AppLayout
            title="Detail Diskusi Konsultasi"
            actions={
                <Link href="/forum" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                    <ChevronLeftIcon />
                    Daftar Forum
                </Link>
            }
        >
            <Head title={thread.title} />

            <div className="mx-auto flex max-w-3xl flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div className="flex flex-wrap items-center gap-2">
                            <Badge color={thread.topic === 'Darurat' ? 'error' : 'primary'}>{thread.topic}</Badge>
                            {thread.is_answered_by_counselor && <Badge color="success">Ditanggapi Konselor</Badge>}
                            <span className="text-xs text-slate-600 dark:text-slate-300">
                                {thread.anonymous_alias} &middot; {formatDateId(thread.created_at, true)}
                            </span>
                        </div>
                        <CardTitle className="mt-1 text-xl">{thread.title}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="whitespace-pre-line text-sm leading-relaxed text-slate-800 dark:text-slate-100">
                            {thread.content}
                        </p>
                    </CardContent>
                </Card>

                <section className="flex flex-col gap-3">
                    <h2 className="text-base font-bold text-slate-900 dark:text-white">
                        Tanggapan ({thread.replies.length})
                    </h2>

                    {thread.replies.length === 0 ? (
                        <Card size="sm">
                            <p className="text-sm text-slate-600 dark:text-slate-300">
                                Belum ada balasan pada diskusi ini. Jadilah yang pertama memberikan tanggapan suportif.
                            </p>
                        </Card>
                    ) : (
                        thread.replies.map((reply) => {
                            const isCounselor = reply.role_badge === 'dosen_pa' || reply.role_badge === 'admin';
                            return (
                                <Card
                                    key={reply.id}
                                    size="sm"
                                    className={isCounselor ? 'border-teal-300 bg-teal-50/50 dark:border-teal-700 dark:bg-teal-950/30' : ''}
                                >
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <div className="flex items-center gap-2">
                                            <span className="text-xs font-bold text-slate-900 dark:text-white">{reply.display_name}</span>
                                            {isCounselor && (
                                                <Badge color="primary" size="sm">
                                                    {reply.role_badge === 'dosen_pa' ? 'Dosen PA' : 'Pengelola'}
                                                </Badge>
                                            )}
                                        </div>
                                        <span className="text-xs text-slate-600 dark:text-slate-300">
                                            {formatDateId(reply.created_at, true)}
                                        </span>
                                    </div>
                                    <p className="whitespace-pre-line text-sm leading-relaxed text-slate-800 dark:text-slate-100">
                                        {reply.reply_content}
                                    </p>
                                </Card>
                            );
                        })
                    )}
                </section>

                <Card>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="flex flex-col gap-3">
                            <Label htmlFor="reply-content">Tulis Balasan</Label>
                            <Textarea
                                id="reply-content"
                                name="reply_content"
                                rows={3}
                                required
                                minLength={3}
                                value={form.data.reply_content}
                                onChange={(e) => form.setData('reply_content', e.target.value)}
                                aria-invalid={Boolean(form.errors.reply_content)}
                                placeholder="Tuliskan tanggapan yang empatik dan informatif..."
                            />
                            <FieldError message={form.errors.reply_content} />
                            <div className="flex justify-end">
                                <Button type="submit" disabled={form.processing}>
                                    {form.processing ? 'Mengirim...' : 'Kirim Balasan'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
