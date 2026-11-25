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
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { BookOpenIcon, ClipboardListIcon, PlusIcon, SearchIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import { cn } from '@/utils';

interface PretestModuleTab {
    id: number;
    module_number: number;
    title: string;
    questions_count: number;
}

interface AdminPretestProps {
    modules: PretestModuleTab[];
    selectedModule: number;
    searchQuery: string;
    questions: AdminQuestionRow[];
    totalPretestQuestions: number;
}

export default function Index({
    modules,
    selectedModule,
    searchQuery,
    questions,
    totalPretestQuestions,
}: AdminPretestProps) {
    const [search, setSearch] = useState(searchQuery);
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editing, setEditing] = useState<AdminQuestionRow | null>(null);
    const [deleting, setDeleting] = useState<AdminQuestionRow | null>(null);
    const [busyDelete, setBusyDelete] = useState(false);

    const form = useForm({
        module_target: String(selectedModule),
        question_text: '',
        option_a: '',
        option_b: '',
        option_c: '',
        option_d: '',
        correct_answer: 'A',
        explanation: '',
    });

    const activeModuleObj = modules.find((m) => m.module_number === selectedModule) ?? modules[0] ?? null;

    const switchModule = (modNum: number, nextSearch = search) => {
        router.get(
            '/admin/pretest-questions',
            {
                module: modNum,
                ...(nextSearch.trim() ? { q: nextSearch.trim() } : {}),
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const openCreate = () => {
        setEditing(null);
        form.clearErrors();
        form.setData({
            module_target: String(selectedModule),
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
            module_target: String(item.module_target ?? selectedModule),
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
            form.put(`/admin/pretest-questions/${editing.id}`, {
                preserveScroll: true,
                onSuccess: () => setDialogOpen(false),
            });
        } else {
            form.post('/admin/pretest-questions', {
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
        router.delete(`/admin/pretest-questions/${deleting.id}`, {
            preserveScroll: true,
            onFinish: () => {
                setBusyDelete(false);
                setDeleting(null);
            },
        });
    };

    return (
        <AppLayout
            title="Kelola Bank Soal Pre-Test (Berdasarkan Modul)"
            actions={
                <div className="flex flex-wrap gap-2">
                    <Link href="/admin/posttest-questions" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                        Ke Kelola Post-Test
                    </Link>
                    {modules.length > 0 && (
                        <Button size="sm" onClick={openCreate}>
                            <PlusIcon />
                            Tambah Soal Modul {selectedModule}
                        </Button>
                    )}
                </div>
            }
        >
            <Head title="Kelola Soal Pre-Test" />

            <div className="flex flex-col gap-5">
                {modules.length === 0 ? (
                    <EmptyState
                        icon={<BookOpenIcon className="size-8" />}
                        title="Belum ada modul edukasi"
                        description="Buat modul terlebih dahulu agar Anda dapat mengelompokkan soal Pre-Test berdasarkan modul."
                        action={
                            <Link href="/admin/modules" className={buttonVariants({ variant: 'default', size: 'sm' })}>
                                Buka Kelola Modul
                            </Link>
                        }
                    />
                ) : (
                    <>
                        <Card size="sm" className="gap-4">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p className="text-xs font-semibold text-teal-700 dark:text-teal-300">
                                        Pengelompokan Soal Pre-Test per Modul ({totalPretestQuestions} total butir)
                                    </p>
                                    <p className="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                                        Soal Pre-Test ditampilkan kepada mahasiswa tepat sebelum mereka dapat membuka materi subbab pada modul terkait.
                                    </p>
                                </div>
                            </div>

                            <div className="flex gap-2 overflow-x-auto pb-1" role="tablist" aria-label="Pilih modul soal Pre-Test">
                                {modules.map((mod) => {
                                    const active = mod.module_number === selectedModule;
                                    return (
                                        <button
                                            key={mod.id}
                                            type="button"
                                            role="tab"
                                            aria-selected={active}
                                            onClick={() => switchModule(mod.module_number)}
                                            className={cn(
                                                'flex min-h-11 shrink-0 items-center gap-2 rounded-lg border px-3.5 py-2 text-xs font-semibold transition-colors',
                                                active
                                                    ? 'border-teal-700 bg-teal-700 text-white dark:border-teal-400 dark:bg-teal-400 dark:text-slate-950'
                                                    : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200',
                                            )}
                                        >
                                            <span>Modul {mod.module_number}</span>
                                            <Badge color={active ? 'gray' : 'primary'} size="sm">
                                                {mod.questions_count} soal
                                            </Badge>
                                        </button>
                                    );
                                })}
                            </div>

                            <div className="flex flex-col justify-between gap-3 border-t border-slate-200 pt-3 sm:flex-row sm:items-center dark:border-slate-700">
                                <p className="text-sm font-bold text-slate-900 dark:text-white">
                                    Modul {selectedModule}: {activeModuleObj?.title}
                                </p>

                                <form
                                    onSubmit={(e) => {
                                        e.preventDefault();
                                        switchModule(selectedModule, search);
                                    }}
                                    className="flex items-center gap-2"
                                >
                                    <Label htmlFor="pretest-search" className="sr-only">
                                        Cari teks soal Pre-Test
                                    </Label>
                                    <Input
                                        id="pretest-search"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        placeholder="Cari teks soal..."
                                        className="sm:w-64"
                                    />
                                    <Button type="submit" variant="outline" size="icon" aria-label="Cari soal Pre-Test">
                                        <SearchIcon />
                                    </Button>
                                </form>
                            </div>
                        </Card>

                        {questions.length === 0 ? (
                            <EmptyState
                                icon={<ClipboardListIcon className="size-8" />}
                                title={`Belum ada soal Pre-Test pada Modul ${selectedModule}`}
                                description="Tambahkan minimal satu soal pilihan ganda (A sampai D) untuk menguji pemahaman awal mahasiswa sebelum membuka modul ini."
                                action={
                                    <Button size="sm" onClick={openCreate}>
                                        <PlusIcon />
                                        Tambah Soal Pertama Modul {selectedModule}
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
                                        moduleLabel={`Modul ${selectedModule}`}
                                        onEdit={openEdit}
                                        onDelete={setDeleting}
                                    />
                                ))}
                            </div>
                        )}
                    </>
                )}
            </div>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent className="sm:max-w-xl">
                    <DialogHeader>
                        <DialogTitle>
                            {editing ? 'Edit Butir Soal Pre-Test' : `Tambah Soal Pre-Test Modul ${form.data.module_target}`}
                        </DialogTitle>
                        <DialogDescription>
                            Pilih modul sasaran, isi pertanyaan serta empat opsi jawaban A sampai D, lalu tentukan kunci jawaban benar.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={handleSubmit} className="flex flex-col gap-3.5">
                        <div className="flex flex-col gap-1">
                            <Label htmlFor="pq-mod">Modul Sasaran *</Label>
                            <Select
                                id="pq-mod"
                                name="module_target"
                                required
                                value={form.data.module_target}
                                onChange={(e) => form.setData('module_target', e.target.value)}
                            >
                                {modules.map((m) => (
                                    <option key={m.id} value={m.module_number}>
                                        Modul {m.module_number}: {m.title}
                                    </option>
                                ))}
                            </Select>
                            <FieldError message={form.errors.module_target} />
                        </div>

                        <div className="flex flex-col gap-1">
                            <Label htmlFor="pq-text">Teks Pertanyaan *</Label>
                            <Textarea
                                id="pq-text"
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
                                        <Label htmlFor={`pq-${letter}`}>Pilihan {upper} *</Label>
                                        <Input
                                            id={`pq-${letter}`}
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
                            <Label htmlFor="pq-exp">Pembahasan Ilmiah (Opsional)</Label>
                            <Textarea
                                id="pq-exp"
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
                                {form.processing ? 'Menyimpan...' : 'Simpan Soal Pre-Test'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <DeleteQuestionDialog
                question={deleting}
                deleting={busyDelete}
                label="Pre-Test"
                onClose={() => setDeleting(null)}
                onConfirm={confirmDelete}
            />
        </AppLayout>
    );
}
