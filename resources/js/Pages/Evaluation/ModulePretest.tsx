import { Head, Link, useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { ProgressBar } from '@/components/ui/progress-bar';
import { ChevronLeftIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { TestQuestionItem } from '@/types/sikera';
import { cn } from '@/utils';

interface ModulePretestProps {
    module: {
        id: number;
        module_number: number;
        title: string;
        subtitle: string | null;
        estimated_time: string | number | null;
    };
    questions: TestQuestionItem[];
}

export default function ModulePretest({ module, questions }: ModulePretestProps) {
    const [clientError, setClientError] = useState<string | null>(null);
    const form = useForm<{ answers: Record<string, string> }>({
        answers: {},
    });

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
            setClientError(`Mohon jawab seluruh pertanyaan (${answeredCount} dari ${total} terjawab) sebelum mengirim Pre-Test.`);
            return;
        }
        form.post(`/modules/${module.id}/pretest`);
    };

    return (
        <AppLayout
            title={`Pre-Test Modul ${module.module_number}`}
            actions={
                <Link href="/modules" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                    <ChevronLeftIcon />
                    Katalog Modul
                </Link>
            }
        >
            <Head title={`Pre-Test Modul ${module.module_number}`} />

            <div className="mx-auto flex max-w-3xl flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div className="flex flex-wrap items-center gap-2">
                            <Badge color="primary">Evaluasi Awal Modul {module.module_number}</Badge>
                            <Badge color="gray">{total} Butir Soal</Badge>
                        </div>
                        <CardTitle className="mt-1 text-xl">{module.title}</CardTitle>
                        <CardDescription>
                            Jawab setiap butir soal sesuai pengetahuan Anda saat ini untuk membuka akses subbab flipbook PDF pada Modul {module.module_number}.
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
                            <ProgressBar value={pct} label="Progres pengisian Pre-Test modul" />
                        </div>
                    </CardContent>
                </Card>

                {(clientError || form.errors.answers) && (
                    <Alert variant="destructive">
                        <AlertDescription>{clientError || form.errors.answers}</AlertDescription>
                    </Alert>
                )}

                {questions.length === 0 ? (
                    <EmptyState
                        title="Belum ada soal Pre-Test untuk modul ini"
                        description="Pengelola belum menambahkan butir soal Pre-Test pada modul ini."
                    />
                ) : (
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
                                {form.processing ? 'Menyimpan jawaban...' : `Kirim Pre-Test & Buka Modul ${module.module_number}`}
                            </Button>
                        </div>
                    </form>
                )}
            </div>
        </AppLayout>
    );
}
