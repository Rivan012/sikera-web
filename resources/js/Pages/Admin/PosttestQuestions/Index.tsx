import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import {
    AdminQuestionCard,
    DeleteQuestionDialog,
    extractOptionText,
    type AdminQuestionRow,
} from '@/components/admin/questions/QuestionDialogs';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { EmptyState } from '@/components/ui/empty-state';
import { FieldError } from '@/components/ui/field-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ClipboardCheckIcon, PlusIcon, SearchIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import { cn } from '@/utils';

interface AdminPosttestProps {
    searchQuery: string;
    questions: AdminQuestionRow[];
    totalPosttestQuestions: number;
}

export default function Index({ searchQuery, questions, totalPosttestQuestions }: AdminPosttestProps) {
    const [search, setSearch] = useState(searchQuery);
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editing, setEditing] = useState<AdminQuestionRow | null>(null);
    const [deleting, setDeleting] = useState<AdminQuestionRow | null>(null);
    const [busyDelete, setBusyDelete] = useState(false);

    const form = useForm({
        question_text: '',
        option_a: '',
        option_b: '',
        option_c: '',
        option_d: '',
        correct_answer: 'A',
        explanation: '',
    });

    const applySearch = (nextSearch = search) => {
        router.get(
            '/admin/posttest-questions',
            nextSearch.trim() ? { q: nextSearch.trim() } : {},
            { preserveState: true, preserveScroll: true },
        );
    };

    const openCreate = () => {
        setEditing(null);
        form.clearErrors();
        form.setData({
            question_text: '',
            option_a: '',
            option_b: '',
            option_c: '',
            option_d: '',
            correct_answer: 'A',
            explanation: '',
        });
        setDialogOpen(true);
    };

    const openEdit = (item: AdminQuestionRow) => {
        setEditing(item);
        form.clearErrors();
        form.setData({
            question_text: item.question_text,
            option_a: extractOptionText(item.options, 'A'),
            option_b: extractOptionText(item.options, 'B'),
            option_c: extractOptionText(item.options, 'C'),
            option_d: extractOptionText(item.options, 'D'),
            correct_answer: item.correct_answer || 'A',
            explanation: item.explanation ?? '',
        });
        setDialogOpen(true);
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        if (editing) {
            form.put(`/admin/posttest-questions/${editing.id}`, {
                preserveScroll: true,
                onSuccess: () => setDialogOpen(false),
            });
        } else {
            form.post('/admin/posttest-questions', {
                preserveScroll: true,
                onSuccess: () => setDialogOpen(false),
            });
        }
    };

    const confirmDelete = () => {
        if (!deleting) {
            return;
        }
        setBusyDelete(true);
        router.delete(`/admin/posttest-questions/${deleting.id}`, {
            preserveScroll: true,
            onFinish: () => {
                setBusyDelete(false);
                setDeleting(null);
            },
        });
    };

    return (
        <AppLayout
            title="Kelola Bank Soal Post-Test Akhir"
            actions={
                <div className="flex flex-wrap gap-2">
                    <Link href="/admin/pretest-questions" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                        Ke Kelola Pre-Test
                    </Link>
                    <Button size="sm" onClick={openCreate}>
                        <PlusIcon />
                        Tambah Soal Post-Test
                    </Button>
                </div>
            }
        >
            <Head title="Kelola Soal Post-Test" />

            <div className="flex flex-col gap-5">
                <Card size="sm" className="flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <div className="flex items-center gap-2">
                            <Badge color="primary">Bank Soal Umum</Badge>
                            <Badge color="gray">{totalPosttestQuestions} Butir Soal</Badge>
                        </div>
                        <p className="mt-1.5 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                            Soal Post-Test bersifat umum tanpa pengelompokan modul, terbuka otomatis 3 hari setelah mahasiswa menyelesaikan seluruh modul, dan digunakan untuk menghitung skor N-Gain total.
                        </p>
                    </div>

                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            applySearch(search);
                        }}
                        className="flex shrink-0 items-center gap-2"
                    >
                        <Label htmlFor="posttest-search" className="sr-only">
                            Cari teks soal Post-Test
                        </Label>
                        <Input
                            id="posttest-search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari teks soal..."
                            className="sm:w-64"
                        />
                        <Button type="submit" variant="outline" size="icon" aria-label="Cari soal Post-Test">
                            <SearchIcon />
                        </Button>
                    </form>
                </Card>

                {questions.length === 0 ? (
                    <EmptyState
                        icon={<ClipboardCheckIcon className="size-8" />}
                        title="Belum ada soal Post-Test umum"
                        description="Tambahkan butir soal evaluasi akhir umum yang akan dikerjakan responden setelah masa retensi 3 hari."
                        action={
                            <Button size="sm" onClick={openCreate}>
                                <PlusIcon />
                                Tambah Soal Post-Test Pertama
                            </Button>
                        }
                    />
                ) : (
                    <div className="flex flex-col gap-3">
                        {questions.map((item, idx) => (
                            <AdminQuestionCard
                                key={item.id}
                                item={item}
                                index={idx}
                                moduleLabel="Post-Test Umum"
                                onEdit={openEdit}
                                onDelete={setDeleting}
                            />
                        ))}
                    </div>
                )}
            </div>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent className="sm:max-w-xl">
                    <DialogHeader>
                        <DialogTitle>
                            {editing ? 'Edit Butir Soal Post-Test' : 'Tambah Soal Post-Test Umum'}
                        </DialogTitle>
                        <DialogDescription>
                            Isi pertanyaan umum tanpa label modul beserta empat pilihan jawaban A sampai D dan kunci jawaban benar.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={handleSubmit} className="flex flex-col gap-3.5">
                        <div className="flex flex-col gap-1">
                            <Label htmlFor="poq-text">Teks Pertanyaan *</Label>
                            <Textarea
                                id="poq-text"
                                name="question_text"
                                rows={3}
                                required
                                value={form.data.question_text}
                                onChange={(e) => form.setData('question_text', e.target.value)}
                            />
                            <FieldError message={form.errors.question_text} />
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            {(['a', 'b', 'c', 'd'] as const).map((letter) => {
                                const field = `option_${letter}` as const;
                                const upper = letter.toUpperCase();
                                return (
                                    <div key={letter} className="flex flex-col gap-1">
                                        <Label htmlFor={`poq-${letter}`}>Pilihan {upper} *</Label>
                                        <Input
                                            id={`poq-${letter}`}
                                            name={field}
                                            required
                                            value={form.data[field]}
                                            onChange={(e) => form.setData(field, e.target.value)}
                                        />
                                    </div>
                                );
                            })}
                        </div>
                        <FieldError message={(form.errors as Record<string, string>).options || (form.errors as Record<string, string>)['options.0.text']} />

                        <div className="flex flex-col gap-1">
                            <span className="text-xs font-semibold text-slate-700 dark:text-slate-200">Kunci Jawaban Benar *</span>
                            <div className="grid grid-cols-4 gap-2">
                                {(['A', 'B', 'C', 'D'] as const).map((ans) => {
                                    const active = form.data.correct_answer === ans;
                                    return (
                                        <label
                                            key={ans}
                                            className={cn(
                                                'flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg border text-sm font-bold',
                                                active
                                                    ? 'border-teal-700 bg-teal-50 text-teal-900 dark:border-teal-400 dark:bg-teal-950/60 dark:text-teal-100'
                                                    : 'border-slate-300 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200',
                                            )}
                                        >
                                            <input
                                                type="radio"
                                                name="correct_answer"
                                                value={ans}
                                                checked={active}
                                                onChange={() => form.setData('correct_answer', ans)}
                                                className="size-4 border-slate-300 text-teal-700"
                                            />
                                            <span>Opsi {ans}</span>
                                        </label>
                                    );
                                })}
                            </div>
                            <FieldError message={form.errors.correct_answer} />
                        </div>

                        <div className="flex flex-col gap-1">
                            <Label htmlFor="poq-exp">Pembahasan Ilmiah (Opsional)</Label>
                            <Textarea
                                id="poq-exp"
                                name="explanation"
                                rows={2}
                                value={form.data.explanation}
                                onChange={(e) => form.setData('explanation', e.target.value)}
                            />
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setDialogOpen(false)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Menyimpan...' : 'Simpan Soal Post-Test'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <DeleteQuestionDialog
                question={deleting}
                deleting={busyDelete}
                label="Post-Test"
                onClose={() => setDeleting(null)}
                onConfirm={confirmDelete}
            />
        </AppLayout>
    );
}
