import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { CheckIcon, SparklesIcon, XIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { SharedProps } from '@/types/sikera';
import { cn } from '@/utils';

interface MythResult {
    answered?: boolean;
    correct?: boolean;
    is_fact?: boolean;
    explanation?: string;
    points?: number;
    message?: string;
}

interface MythFactProps {
    card: {
        id: number;
        statement: string;
        category: string;
    } | null;
    result: MythResult | null;
}

export default function MythFact({ card, result }: MythFactProps) {
    const { auth, flash } = usePage<SharedProps>().props;
    const [submitting, setSubmitting] = useState(false);
    const activeResult = (flash.myth_result as unknown as MythResult | null) || result;

    const submitChoice = (answer: 'fact' | 'myth') => {
        if (!card || submitting) {
            return;
        }
        setSubmitting(true);
        router.post(
            '/myth-fact',
            { card_id: card.id, answer },
            { onFinish: () => setSubmitting(false) },
        );
    };

    return (
        <AppLayout title="Kartu Mitos vs Fakta Ilmiah">
            <Head title="Mitos vs Fakta" />

            <div className="mx-auto flex max-w-2xl flex-col gap-5">
                <div className="flex flex-wrap items-center gap-2">
                    <Badge color="warning">Streak: {auth.user?.streak_days ?? 0} hari</Badge>
                    <Badge color="primary">{auth.user?.points ?? 0} poin</Badge>
                </div>

                {activeResult && (activeResult.answered || typeof activeResult.correct === 'boolean') ? (
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
                                            {activeResult.correct ? 'Tepat Sekali!' : 'Kurang Tepat'}
                                        </h2>
                                        <p className="mt-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                            Status ilmiah pernyataan: <strong>{activeResult.is_fact ? 'FAKTA' : 'MITOS'}</strong>
                                        </p>
                                        {activeResult.points !== undefined && (
                                            <Badge color="primary" className="mt-2">
                                                +{activeResult.points} poin
                                            </Badge>
                                        )}
                                    </div>
                                    {activeResult.explanation && (
                                        <div className="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                                            <p className="text-xs font-bold text-slate-700 dark:text-slate-200">Rujukan Ilmiah:</p>
                                            <p className="mt-1 text-sm leading-relaxed text-slate-700 dark:text-slate-200">
                                                {activeResult.explanation}
                                            </p>
                                        </div>
                                    )}
                                </>
                            ) : (
                                <p className="py-4 text-center text-sm text-slate-600 dark:text-slate-300">
                                    {activeResult.message || 'Anda sudah menyelesaikan tantangan Mitos vs Fakta hari ini.'}
                                </p>
                            )}
                        </CardContent>
                    </Card>
                ) : card ? (
                    <Card>
                        <CardHeader>
                            <Badge color="info">{card.category}</Badge>
                            <CardTitle className="mt-2 text-lg leading-relaxed">
                                &ldquo;{card.statement}&rdquo;
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <Button
                                    type="button"
                                    variant="destructive"
                                    size="lg"
                                    disabled={submitting}
                                    onClick={() => submitChoice('myth')}
                                >
                                    Ini MITOS
                                </Button>
                                <Button
                                    type="button"
                                    variant="default"
                                    size="lg"
                                    disabled={submitting}
                                    onClick={() => submitChoice('fact')}
                                >
                                    Ini FAKTA Ilmiah
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <EmptyState
                        icon={<SparklesIcon className="size-8" />}
                        title="Belum ada kartu Mitos vs Fakta tersedia"
                    />
                )}
            </div>
        </AppLayout>
    );
}
