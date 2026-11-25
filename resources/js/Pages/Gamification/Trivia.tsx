import { Head, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { CheckIcon, SparklesIcon, XIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { SharedProps } from '@/types/sikera';
import { cn } from '@/utils';

interface TriviaResult {
    answered?: boolean;
    correct?: boolean;
    points?: number;
    explanation?: string;
    correct_answer?: string;
    message?: string;
}

interface TriviaPageProps {
    question: {
        id: number;
        question: string;
        choices: string[];
    } | null;
    result: TriviaResult | null;
}

export default function Trivia({ question, result }: TriviaPageProps) {
    const { auth, flash } = usePage<SharedProps>().props;
    const activeResult = (flash.trivia_result as unknown as TriviaResult | null) || result;

    const form = useForm({
        question_id: question?.id ?? 0,
        answer: '',
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        if (!question || !form.data.answer) {
            return;
        }
        form.post('/trivia');
    };

    return (
        <AppLayout title="Kuis Harian Kesehatan Reproduksi">
            <Head title="Trivia Harian" />

            <div className="mx-auto flex max-w-2xl flex-col gap-5">
                <div className="flex flex-wrap items-center gap-2">
                    <Badge color="warning">Streak: {auth.user?.streak_days ?? 0} hari</Badge>
                    <Badge color="primary">{auth.user?.points ?? 0} poin</Badge>
                </div>

                {activeResult && activeResult.answered ? (
                    <Card>
                        <CardContent className="flex flex-col gap-4 py-4">
                            {typeof activeResult.correct === 'boolean' ? (
                                <>
                                    <div className="flex flex-col items-center text-center">
                                        <span
                                            className={cn(
                                                'flex size-14 items-center justify-center rounded-xl',
                                                activeResult.correct
                                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                                                    : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300',
                                            )}
                                        >
                                            {activeResult.correct ? <CheckIcon className="size-7" /> : <XIcon className="size-7" />}
                                        </span>
                                        <h2 className="mt-3 text-lg font-bold text-slate-900 dark:text-white">
                                            {activeResult.correct ? 'Jawaban Benar!' : 'Belum Tepat'}
                                        </h2>
                                        {!activeResult.correct && activeResult.correct_answer && (
                                            <p className="mt-1 text-xs text-slate-600 dark:text-slate-300">
                                                Jawaban benar: <strong>{activeResult.correct_answer}</strong>
                                            </p>
                                        )}
                                        {activeResult.points !== undefined && (
                                            <Badge color="primary" className="mt-2">
                                                +{activeResult.points} poin
                                            </Badge>
                                        )}
                                    </div>
                                    {activeResult.explanation && (
                                        <div className="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                                            <p className="text-xs font-bold text-slate-700 dark:text-slate-200">Penjelasan Ilmiah:</p>
                                            <p className="mt-1 text-sm leading-relaxed text-slate-700 dark:text-slate-200">
                                                {activeResult.explanation}
                                            </p>
                                        </div>
                                    )}
                                </>
                            ) : (
                                <p className="py-4 text-center text-sm text-slate-600 dark:text-slate-300">
                                    {activeResult.message || 'Anda sudah menyelesaikan trivia hari ini. Kembali besok untuk kuis berikutnya.'}
                                </p>
                            )}
                        </CardContent>
                    </Card>
                ) : question ? (
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base leading-relaxed">{question.question}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                                <div className="flex flex-col gap-2.5">
                                    {question.choices.map((choice) => {
                                        const selected = form.data.answer === choice;
                                        return (
                                            <label
                                                key={choice}
                                                className={cn(
                                                    'flex min-h-11 cursor-pointer items-center gap-3 rounded-lg border p-3.5 text-sm transition-colors',
                                                    selected
                                                        ? 'border-teal-700 bg-teal-50 text-slate-900 dark:border-teal-400 dark:bg-teal-950/60 dark:text-white'
                                                        : 'border-slate-300 bg-white text-slate-800 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100',
                                                )}
                                            >
                                                <input
                                                    type="radio"
                                                    name="answer"
                                                    value={choice}
                                                    required
                                                    checked={selected}
                                                    onChange={() => form.setData('answer', choice)}
                                                    className="size-4 border-slate-300 text-teal-700"
                                                />
                                                <span>{choice}</span>
                                            </label>
                                        );
                                    })}
                                </div>
                                <Button type="submit" disabled={form.processing || !form.data.answer}>
                                    {form.processing ? 'Memeriksa jawaban...' : 'Kirim Jawaban'}
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                ) : (
                    <EmptyState
                        icon={<SparklesIcon className="size-8" />}
                        title="Belum ada pertanyaan trivia tersedia"
                    />
                )}
            </div>
        </AppLayout>
    );
}
