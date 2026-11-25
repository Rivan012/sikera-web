import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { FieldError } from '@/components/ui/field-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { formatDateId } from '@/constants/akademik';
import { FileTextIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import { cn } from '@/utils';

const MOODS = ['Senang', 'Tenang', 'Lelah', 'Cemas', 'Sedih'] as const;

interface DiaryEntry {
    id: number;
    mood: string;
    encrypted_note: string;
    entry_date: string;
}

interface DiaryProps {
    entries: DiaryEntry[];
}

export default function Diary({ entries }: DiaryProps) {
    const today = new Date().toISOString().slice(0, 10);
    const form = useForm({
        mood: 'Tenang',
        entry_date: today,
        encrypted_note: '',
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/diary', {
            preserveScroll: true,
            onSuccess: () => form.setData('encrypted_note', ''),
        });
    };

    return (
        <AppLayout title="Catatan Mood & Jurnal Pribadi">
            <Head title="Catatan Mood" />

            <div className="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
                <Card className="lg:col-span-5">
                    <CardHeader>
                        <CardTitle>Tulis Jurnal Harian</CardTitle>
                        <CardDescription>
                            Rekam suasana hati dan kondisi tubuh harian Anda secara pribadi.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="diary-date">Tanggal Catatan *</Label>
                                <Input
                                    id="diary-date"
                                    name="entry_date"
                                    type="date"
                                    required
                                    value={form.data.entry_date}
                                    onChange={(e) => form.setData('entry_date', e.target.value)}
                                />
                                <FieldError message={form.errors.entry_date} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <span className="text-xs font-semibold text-slate-700 dark:text-slate-200">Suasana Hati *</span>
                                <div className="flex flex-wrap gap-2">
                                    {MOODS.map((mood) => {
                                        const active = form.data.mood === mood;
                                        return (
                                            <button
                                                key={mood}
                                                type="button"
                                                aria-pressed={active}
                                                onClick={() => form.setData('mood', mood)}
                                                className={cn(
                                                    'inline-flex min-h-11 items-center rounded-lg border px-3.5 text-xs font-semibold transition-colors',
                                                    active
                                                        ? 'border-teal-700 bg-teal-700 text-white dark:border-teal-400 dark:bg-teal-400 dark:text-slate-950'
                                                        : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200',
                                                )}
                                            >
                                                {mood}
                                            </button>
                                        );
                                    })}
                                </div>
                                <FieldError message={form.errors.mood} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="diary-note">Catatan Refleksi *</Label>
                                <Textarea
                                    id="diary-note"
                                    name="encrypted_note"
                                    rows={4}
                                    required
                                    maxLength={1000}
                                    value={form.data.encrypted_note}
                                    onChange={(e) => form.setData('encrypted_note', e.target.value)}
                                    placeholder="Tuliskan hal yang Anda rasakan hari ini..."
                                />
                                <FieldError message={form.errors.encrypted_note} />
                            </div>

                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Menyimpan...' : 'Simpan Catatan'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <div className="flex flex-col gap-3 lg:col-span-7">
                    <h2 className="text-base font-bold text-slate-900 dark:text-white">Riwayat Catatan Pribadi</h2>
                    {entries.length === 0 ? (
                        <EmptyState
                            icon={<FileTextIcon className="size-8" />}
                            title="Belum ada catatan tersimpan"
                            description="Mulai catat suasana hati Anda di formulir sebelah kiri."
                        />
                    ) : (
                        entries.map((entry) => (
                            <Card key={entry.id} size="sm">
                                <div className="flex items-center justify-between gap-2">
                                    <Badge color="primary">{entry.mood}</Badge>
                                    <span className="text-xs text-slate-600 dark:text-slate-300">
                                        {formatDateId(entry.entry_date)}
                                    </span>
                                </div>
                                <p className="whitespace-pre-line text-sm leading-relaxed text-slate-800 dark:text-slate-100">
                                    {entry.encrypted_note}
                                </p>
                            </Card>
                        ))
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
