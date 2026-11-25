import { Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { CheckIcon, ChevronLeftIcon, XIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { QuestionOption } from '@/types/sikera';
import { cn } from '@/utils';

interface QuestionBreakdownItem {
    number: number;
    id: number;
    module_target: number | null;
    question_text: string;
    options: QuestionOption[];
    user_answer: string | null;
    correct_answer: string;
    is_correct: boolean;
    explanation: string | null;
    comparison_answer: string | null;
}

interface EvaluationShowProps {
    evaluation: {
        id: number;
        type: 'pre_test' | 'post_test';
        module_number: number | null;
        total_score: number;
        n_gain_score: number | null;
        submitted_at: string | null;
    };
    student: {
        id: number;
        name: string;
        initials: string | null;
        nim: string | null;
        email: string;
        prodi: string | null;
        fakultas: string | null;
    } | null;
    questionItems: QuestionBreakdownItem[];
    correctCount: number;
    totalQuestions: number;
    comparisonScore: number | null;
}

export default function Show({
    evaluation,
    student,
    questionItems,
    correctCount,
    totalQuestions,
    comparisonScore,
}: EvaluationShowProps) {
    const isPost = evaluation.type === 'post_test';

    return (
        <AppLayout
            title="Rincian Lembar Jawaban Responden"
            actions={
                <Link href="/admin/evaluations" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                    <ChevronLeftIcon />
                    Daftar Evaluasi
                </Link>
            }
        >
            <Head title="Rincian Jawaban Evaluasi" />

            <div className="mx-auto flex max-w-4xl flex-col gap-5">
                <Card>
                    <CardHeader>
                        <div className="flex flex-wrap items-center gap-2">
                            <Badge color={isPost ? 'success' : 'info'}>
                                {isPost ? 'Post-Test Akhir (Umum)' : `Pre-Test ${evaluation.module_number ? `Modul ${evaluation.module_number}` : 'Umum'}`}
                            </Badge>
                            {evaluation.submitted_at && (
                                <span className="text-xs text-slate-600 dark:text-slate-300">
                                    Dikirim {evaluation.submitted_at}
                                </span>
                            )}
                        </div>
                        <CardTitle className="mt-1 text-xl">
                            {student?.name || 'Responden'} ({student?.nim || '-'})
                        </CardTitle>
                        <p className="text-xs text-slate-600 dark:text-slate-300">
                            {student?.prodi || '-'} &middot; {student?.fakultas || '-'}
                        </p>
                    </CardHeader>

                    <CardContent className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div className="rounded-xl border border-slate-200 p-3.5 text-center dark:border-slate-700">
                            <p className="text-xs text-slate-600 dark:text-slate-300">Skor Lembar Ini</p>
                            <p className="mt-1 text-2xl font-bold">{evaluation.total_score}</p>
                        </div>
                        <div className="rounded-xl border border-slate-200 p-3.5 text-center dark:border-slate-700">
                            <p className="text-xs text-slate-600 dark:text-slate-300">Jawaban Benar</p>
                            <p className="mt-1 text-2xl font-bold">
                                {correctCount}/{totalQuestions}
                            </p>
                        </div>
                        <div className="rounded-xl border border-slate-200 p-3.5 text-center dark:border-slate-700">
                            <p className="text-xs text-slate-600 dark:text-slate-300">
                                {isPost ? 'Rata-rata Pre-Test' : 'Skor Post-Test'}
                            </p>
                            <p className="mt-1 text-2xl font-bold">{comparisonScore ?? '-'}</p>
                        </div>
                        <div className="rounded-xl border border-slate-200 p-3.5 text-center dark:border-slate-700">
                            <p className="text-xs text-slate-600 dark:text-slate-300">Indeks N-Gain</p>
                            <p className="mt-1 text-2xl font-bold text-teal-700 dark:text-teal-300">
                                {evaluation.n_gain_score ?? '-'}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <div className="flex flex-col gap-3">
                    {questionItems.map((item) => (
                        <Card key={item.id} size="sm" className="gap-3">
                            <div className="flex items-start justify-between gap-3">
                                <p className="text-sm leading-relaxed font-bold text-slate-900 dark:text-white">
                                    <span className="mr-2 font-mono text-xs text-teal-700 dark:text-teal-300">
                                        #{item.number}
                                    </span>
                                    {item.question_text}
                                </p>
                                <Badge color={item.is_correct ? 'success' : 'error'} size="sm">
                                    {item.is_correct ? 'Benar' : 'Salah'}
                                </Badge>
                            </div>

                            <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                {item.options.map((opt) => {
                                    const picked = item.user_answer === opt.label;
                                    const keyAns = item.correct_answer === opt.label;
                                    return (
                                        <div
                                            key={opt.label}
                                            className={cn(
                                                'flex items-start gap-2 rounded-lg border px-3 py-2 text-xs',
                                                keyAns
                                                    ? 'border-emerald-300 bg-emerald-50 font-semibold text-emerald-950 dark:border-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-100'
                                                    : picked
                                                      ? 'border-rose-300 bg-rose-50 text-rose-950 dark:border-rose-700 dark:bg-rose-950/60 dark:text-rose-100'
                                                      : 'border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200',
                                            )}
                                        >
                                            <span className="font-bold">{opt.label}.</span>
                                            <span className="min-w-0 flex-1">{opt.text}</span>
                                            {keyAns && <CheckIcon className="size-4 shrink-0 text-emerald-700 dark:text-emerald-300" />}
                                            {picked && !keyAns && <XIcon className="size-4 shrink-0 text-rose-700 dark:text-rose-300" />}
                                        </div>
                                    );
                                })}
                            </div>
                        </Card>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
