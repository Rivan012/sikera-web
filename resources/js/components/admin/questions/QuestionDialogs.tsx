import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { CheckIcon, PencilIcon, TrashBinIcon } from '@/icons';
import type { QuestionOption } from '@/types/sikera';
import { cn } from '@/utils';

export interface AdminQuestionRow {
    id: number;
    module_target?: number | null;
    question_text: string;
    options: QuestionOption[];
    correct_answer: string;
    explanation: string | null;
}

export function extractOptionText(options: QuestionOption[], label: string): string {
    return options.find((opt) => opt.label.toUpperCase() === label)?.text ?? '';
}

export function AdminQuestionCard({
    item,
    index,
    moduleLabel,
    onEdit,
    onDelete,
}: {
    item: AdminQuestionRow;
    index: number;
    moduleLabel?: string;
    onEdit: (item: AdminQuestionRow) => void;
    onDelete: (item: AdminQuestionRow) => void;
}) {
    return (
        <Card size="sm" className="gap-3.5">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0 flex-1">
                    <div className="flex flex-wrap items-center gap-2">
                        <span className="rounded bg-teal-50 px-2 py-0.5 font-mono text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-200">
                            Soal #{index + 1}
                        </span>
                        {moduleLabel && <Badge color="info" size="sm">{moduleLabel}</Badge>}
                        <Badge color="success" size="sm">Kunci: {item.correct_answer}</Badge>
                    </div>
                    <p className="mt-2 text-sm leading-relaxed font-bold text-slate-900 sm:text-base dark:text-white">
                        {item.question_text}
                    </p>
                </div>

                <div className="flex shrink-0 items-center gap-1.5">
                    <Button
                        type="button"
                        variant="outline"
                        size="icon"
                        onClick={() => onEdit(item)}
                        aria-label={`Edit soal nomor ${index + 1}`}
                    >
                        <PencilIcon />
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        size="icon"
                        onClick={() => onDelete(item)}
                        aria-label={`Hapus soal nomor ${index + 1}`}
                    >
                        <TrashBinIcon />
                    </Button>
                </div>
            </div>

            <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                {item.options.map((opt) => {
                    const isCorrect = opt.label.toUpperCase() === item.correct_answer.toUpperCase();
                    return (
                        <div
                            key={opt.label}
                            className={cn(
                                'flex items-start gap-2 rounded-lg border px-3 py-2 text-xs leading-relaxed sm:text-sm',
                                isCorrect
                                    ? 'border-emerald-300 bg-emerald-50 font-semibold text-emerald-950 dark:border-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-100'
                                    : 'border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200',
                            )}
                        >
                            <span className="font-bold">{opt.label}.</span>
                            <span className="min-w-0 flex-1">{opt.text}</span>
                            {isCorrect && <CheckIcon className="mt-0.5 size-4 shrink-0 text-emerald-700 dark:text-emerald-300" />}
                        </div>
                    );
                })}
            </div>

            {item.explanation && (
                <p className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                    <strong>Pembahasan:</strong> {item.explanation}
                </p>
            )}
        </Card>
    );
}

export function DeleteQuestionDialog({
    question,
    deleting,
    label,
    onClose,
    onConfirm,
}: {
    question: AdminQuestionRow | null;
    deleting: boolean;
    label: string;
    onClose: () => void;
    onConfirm: () => void;
}) {
    return (
        <Dialog open={Boolean(question)} onOpenChange={(open) => !open && onClose()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Butir Soal {label}?</DialogTitle>
                    <DialogDescription>
                        Butir soal &ldquo;{question?.question_text.slice(0, 120)}&rdquo; akan dihapus dari bank soal {label}.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" onClick={onClose}>
                        Batal
                    </Button>
                    <Button type="button" variant="destructive" disabled={deleting} onClick={onConfirm}>
                        {deleting ? 'Menghapus...' : 'Ya, Hapus Soal'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
