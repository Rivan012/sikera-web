import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ProgressBar } from '@/components/ui/progress-bar';
import { formatDateId } from '@/constants/akademik';
import { useCountdown } from '@/hooks/use-countdown';
import { BookOpenIcon, CheckIcon, ClipboardCheckIcon, ClockIcon, LockIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { PosttestGate, TestQuestionItem } from '@/types/sikera';
import { cn } from '@/utils';

interface ModuleCompletionItem {
    id: number;
    module_number: number;
    title: string;
    completed_topics: number;
    total_topics: number;
    is_completed: boolean;
}

interface PosttestPageProps {
    gate: PosttestGate;
    preScore: number;
    questions: TestQuestionItem[];
    modulesProgress: ModuleCompletionItem[];
}

export default function Posttest({ gate, preScore, questions, modulesProgress }: PosttestPageProps) {
    const [clientError, setClientError] = useState<string | null>(null);
    const form = useForm<{ answers: Record<string, string> }>({
        answers: {},
    });

    const countdown = useCountdown(
        gate.state === 'waiting_delay' ? gate.posttest_available_at : null,
        gate.remaining_seconds,
        () => router.reload(),
    );

    const answeredCount = Object.keys(form.data.answers).length;
    const total = questions.length;
    const pct = total > 0 ? Math.round((answeredCount / total) * 100) : 0;

    const selectOption = (questionId: number, label: string) => {
        setClientError(null);
        form.setData('answers', {
            ...form.data.answers,
            [String(questionId)]: label,
        });
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        if (answeredCount < total) {
            setClientError(`Mohon jawab seluruh pertanyaan Post-Test (${answeredCount} dari ${total} terjawab).`);
            return;
        }
        form.post('/posttest');
    };

    return (
        <AppLayout title="Post-Test Akhir Terpadu">
            <Head title="Post-Test Akhir" />

            <div className="mx-auto flex max-w-3xl flex-col gap-6">
                {gate.state === 'completed' && gate.latest_posttest && (
                    <Card className="border-emerald-200 dark:border-emerald-800">
                        <CardHeader>
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <Badge color="success">Evaluasi Tuntas</Badge>
                                {gate.latest_posttest.submitted_at && (
                                    <span className="text-xs text-slate-600 dark:text-slate-300">
                                        Dikirim {formatDateId(gate.latest_posttest.submitted_at, true)}
                                    </span>
                                )}
                            </div>
                            <CardTitle className="mt-1 text-xl">Hasil Post-Test &amp; Indeks N-Gain Anda</CardTitle>
                            <CardDescription>
                                Terima kasih telah menuntaskan seluruh rangkaian pembelajaran dan evaluasi riset SIKERA. Akun Anda tetap aktif untuk mengakses seluruh materi serta instrumen mandiri.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="flex flex-col gap-5">
                            <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div className="rounded-xl border border-slate-200 bg-slate-50 p-4 text-center dark:border-slate-700 dark:bg-slate-900">
                                    <p className="text-xs font-medium text-slate-600 dark:text-slate-300">Rata-rata Pre-Test</p>
                                    <p className="mt-1 text-2xl font-bold text-slate-900 dark:text-white">
                                        {gate.latest_posttest.pre_score}
                                    </p>
                                </div>
                                <div className="rounded-xl border border-teal-200 bg-teal-50 p-4 text-center dark:border-teal-800 dark:bg-teal-950/60">
                                    <p className="text-xs font-medium text-teal-800 dark:text-teal-200">Skor Post-Test</p>
                                    <p className="mt-1 text-2xl font-bold text-teal-900 dark:text-teal-100">
                                        {gate.latest_posttest.total_score}
                                    </p>
                                </div>
                                <div className="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-center dark:border-emerald-800 dark:bg-emerald-950/60">
                                    <p className="text-xs font-medium text-emerald-800 dark:text-emerald-200">Indeks N-Gain</p>
                                    <p className="mt-1 text-2xl font-bold text-emerald-900 dark:text-emerald-100">
                                        {gate.latest_posttest.n_gain_score ?? 0}
                                    </p>
                                </div>
                                <div className="rounded-xl border border-amber-200 bg-amber-50 p-4 text-center dark:border-amber-800 dark:bg-amber-950/60">
                                    <p className="text-xs font-medium text-amber-900 dark:text-amber-200">Efektivitas</p>
                                    <p className="mt-1 text-lg font-bold text-amber-950 dark:text-amber-100">
                                        {gate.latest_posttest.effectiveness_category}
                                    </p>
                                </div>
                            </div>

                            <div className="flex flex-wrap justify-end gap-2">
                                <Link href="/modules" className={buttonVariants({ variant: 'outline' })}>
                                    Buka Kembali Modul Flipbook
                                </Link>
                                <Link href="/mahasiswa/dashboard" className={buttonVariants({ variant: 'default' })}>
                                    Kembali ke Menu Utama
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {gate.state === 'locked_incomplete' && (
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <Badge color="gray">
                                    <LockIcon className="size-3.5" />
                                    Belum Terbuka
                                </Badge>
                            </div>
                            <CardTitle className="mt-1 text-xl">Selesaikan Seluruh Modul Terlebih Dahulu</CardTitle>
                            <CardDescription>
                                Post-Test akhir bersifat terpadu dan baru akan dijadwalkan 3 hari (72 jam) setelah Anda menuntaskan seluruh subbab pada semua modul di bawah ini.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="flex flex-col gap-4">
                            <div className="flex flex-col gap-2.5">
                                {modulesProgress.map((mod) => (
                                    <div
                                        key={mod.id}
                                        className="flex flex-col justify-between gap-3 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center dark:border-slate-700"
                                    >
                                        <div>
                                            <div className="flex items-center gap-2">
                                                <span className="text-xs font-bold text-teal-700 dark:text-teal-300">
                                                    Modul {mod.module_number}
                                                </span>
                                                <Badge color={mod.is_completed ? 'success' : 'warning'} size="sm">
                                                    {mod.completed_topics}/{mod.total_topics} subbab
                                                </Badge>
                                            </div>
                                            <p className="mt-0.5 text-sm font-bold text-slate-900 dark:text-white">{mod.title}</p>
                                        </div>
                                        <Link
                                            href={`/modules/${mod.id}`}
                                            className={buttonVariants({
                                                variant: mod.is_completed ? 'outline' : 'default',
                                                size: 'sm',
                                            })}
                                        >
                                            {mod.is_completed ? <CheckIcon /> : <BookOpenIcon />}
                                            {mod.is_completed ? 'Tuntas' : 'Pelajari'}
                                        </Link>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                )}

                {gate.state === 'waiting_delay' && (
                    <Card className="border-amber-300 dark:border-amber-700">
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <Badge color="warning">
                                    <ClockIcon className="size-3.5" />
                                    Masa Retensi 3 Hari
                                </Badge>
                            </div>
                            <CardTitle className="mt-1 text-xl">Hitung Mundur Pembukaan Post-Test</CardTitle>
                            <CardDescription>
                                Anda telah menyelesaikan seluruh modul pada {formatDateId(gate.all_modules_completed_at, true)}. Sesuai protokol riset, Post-Test akhir baru terbuka 3 hari kemudian pada{' '}
                                <strong>{formatDateId(gate.posttest_available_at, true)}</strong>.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="flex flex-col gap-5">
                            <div className="grid grid-cols-4 gap-2.5 text-center sm:gap-4">
                                {[
                                    { label: 'Hari', value: countdown.days },
                                    { label: 'Jam', value: countdown.hours },
                                    { label: 'Menit', value: countdown.minutes },
                                    { label: 'Detik', value: countdown.seconds },
                                ].map((box) => (
                                    <div
                                        key={box.label}
                                        className="rounded-xl border border-amber-300 bg-amber-50 p-3 sm:p-4 dark:border-amber-700 dark:bg-amber-950/60"
                                    >
                                        <p className="font-mono text-2xl font-bold tabular-nums text-amber-950 sm:text-3xl dark:text-amber-100">
                                            {String(box.value).padStart(2, '0')}
                                        </p>
                                        <p className="mt-1 text-xs font-semibold text-amber-900 dark:text-amber-200">{box.label}</p>
                                    </div>
                                ))}
                            </div>

                            <div className="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4 dark:border-slate-700">
                                <span className="text-xs text-slate-600 dark:text-slate-300">
                                    Rata-rata skor Pre-Test Anda: <strong>{preScore}</strong>
                                </span>
                                <Link href="/modules" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                                    Ulas Kembali Flipbook Materi
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {gate.state === 'ready' && (
                    <>
                        <Card>
                            <CardHeader>
                                <div className="flex flex-wrap items-center gap-2">
                                    <Badge color="primary" variant="solid">
                                        <ClipboardCheckIcon className="size-3.5" />
                                        Post-Test Umum Terbuka
                                    </Badge>
                                    <Badge color="gray">{total} Butir Soal</Badge>
                                    <Badge color="info">Rata-rata Pre-Test: {preScore}</Badge>
                                </div>
                                <CardTitle className="mt-1 text-xl">Instrumen Evaluasi Post-Test Akhir</CardTitle>
                                <CardDescription>
                                    Jawab seluruh pertanyaan umum berikut secara mandiri. Skor akhir Anda akan dibandingkan dengan rata-rata Pre-Test untuk menghitung indeks N-Gain total.
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="flex flex-col gap-1.5">
                                    <div className="flex justify-between text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        <span>
                                            Terjawab {answeredCount} dari {total} soal
                                        </span>
                                        <span>{pct}%</span>
                                    </div>
                                    <ProgressBar value={pct} label="Progres pengisian Post-Test akhir" />
                                </div>
                            </CardContent>
                        </Card>

                        {(clientError || form.errors.answers) && (
                            <Alert variant="destructive">
                                <AlertDescription>{clientError || form.errors.answers}</AlertDescription>
                            </Alert>
                        )}

                        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                            {questions.map((q, index) => {
                                const selected = form.data.answers[String(q.id)];
                                return (
                                    <Card key={q.id} size="sm">
                                        <fieldset className="flex flex-col gap-3">
                                            <legend className="text-sm leading-relaxed font-bold text-slate-900 sm:text-base dark:text-white">
                                                <span className="mr-2 inline-flex rounded bg-teal-50 px-2 py-0.5 font-mono text-xs text-teal-800 dark:bg-teal-950 dark:text-teal-200">
                                                    #{index + 1}
                                                </span>
                                                {q.question_text}
                                            </legend>

                                            <div className="flex flex-col gap-2">
                                                {q.options.map((opt) => {
                                                    const active = selected === opt.label;
                                                    return (
                                                        <label
                                                            key={opt.label}
                                                            className={cn(
                                                                'flex min-h-11 cursor-pointer items-start gap-3 rounded-lg border p-3 text-sm transition-colors focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-teal-700 dark:focus-within:outline-teal-400',
                                                                active
                                                                    ? 'border-teal-700 bg-teal-50 text-slate-900 dark:border-teal-400 dark:bg-teal-950/60 dark:text-white'
                                                                    : 'border-slate-300 bg-white text-slate-800 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800',
                                                            )}
                                                        >
                                                            <input
                                                                type="radio"
                                                                name={`answers[${q.id}]`}
                                                                value={opt.label}
                                                                checked={active}
                                                                onChange={() => selectOption(q.id, opt.label)}
                                                                className="mt-0.5 size-4 shrink-0 border-slate-300 text-teal-700"
                                                            />
                                                            <span className="min-w-0 flex-1 leading-relaxed">
                                                                <strong className="mr-1.5 font-bold">{opt.label}.</strong>
                                                                {opt.text}
                                                            </span>
                                                        </label>
                                                    );
                                                })}
                                            </div>
                                        </fieldset>
                                    </Card>
                                );
                            })}

                            <div className="flex justify-end pt-2">
                                <Button type="submit" size="lg" disabled={form.processing}>
                                    {form.processing ? 'Menghitung skor & N-Gain...' : 'Kirim Post-Test Akhir'}
                                </Button>
                            </div>
                        </form>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
