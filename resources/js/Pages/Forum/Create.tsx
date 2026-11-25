import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FieldError } from '@/components/ui/field-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { ChevronLeftIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

export default function Create() {
    const form = useForm({
        title: '',
        topic: 'Haid',
        content: '',
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/forum');
    };

    return (
        <AppLayout
            title="Buat Pertanyaan Anonim"
            actions={
                <Link href="/forum" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                    <ChevronLeftIcon />
                    Daftar Diskusi
                </Link>
            }
        >
            <Head title="Tulis Pertanyaan Anonim" />

            <div className="mx-auto max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Kirim Pertanyaan Secara Anonim</CardTitle>
                        <CardDescription>
                            Nama asli dan NIM Anda disembunyikan secara otomatis menggunakan alias acak saat pertanyaan tampil di ruang diskusi.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="forum-topic">Topik Pembahasan *</Label>
                                <Select
                                    id="forum-topic"
                                    name="topic"
                                    value={form.data.topic}
                                    onChange={(e) => form.setData('topic', e.target.value)}
                                >
                                    <option value="Haid">Siklus Haid &amp; Dismenore</option>
                                    <option value="Medis">Kesehatan Organ Reproduksi &amp; Gizi</option>
                                    <option value="Hubungan">Relasi Sehat &amp; Batasan Diri</option>
                                    <option value="Darurat">Perlindungan Kekerasan / Darurat</option>
                                </Select>
                                <FieldError message={form.errors.topic} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="forum-title">Judul Pertanyaan *</Label>
                                <Input
                                    id="forum-title"
                                    name="title"
                                    required
                                    value={form.data.title}
                                    onChange={(e) => form.setData('title', e.target.value)}
                                    aria-invalid={Boolean(form.errors.title)}
                                    placeholder="Tuliskan inti pertanyaan secara ringkas"
                                />
                                <FieldError message={form.errors.title} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="forum-content">Uraian Pertanyaan / Keluhan *</Label>
                                <Textarea
                                    id="forum-content"
                                    name="content"
                                    rows={5}
                                    required
                                    minLength={10}
                                    value={form.data.content}
                                    onChange={(e) => form.setData('content', e.target.value)}
                                    aria-invalid={Boolean(form.errors.content)}
                                    placeholder="Ceritakan kronologi atau gejala yang ingin Anda konsultasikan (minimal 10 karakter)..."
                                />
                                <FieldError message={form.errors.content} />
                            </div>

                            <div className="flex justify-end gap-2 pt-2">
                                <Link href="/forum" className={buttonVariants({ variant: 'outline' })}>
                                    Batal
                                </Link>
                                <Button type="submit" disabled={form.processing}>
                                    {form.processing ? 'Mengirim...' : 'Kirim Pertanyaan Anonim'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
