import { Head, router, useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
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
import { ImageIcon, TrashBinIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

interface AdminPosterRow {
    id: number;
    title: string;
    category: string;
    image_path: string;
    caption: string;
    share_text: string;
    download_count: number;
    share_count: number;
}

interface AdminPostersProps {
    posters: AdminPosterRow[];
}

export default function Index({ posters }: AdminPostersProps) {
    const [deletingPoster, setDeletingPoster] = useState<AdminPosterRow | null>(null);
    const [busyDelete, setBusyDelete] = useState(false);

    const form = useForm({
        title: '',
        category: 'Higienitas',
        caption: '',
        share_text: '',
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/admin/posters', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    const confirmDelete = () => {
        if (!deletingPoster) {
            return;
        }
        setBusyDelete(true);
        router.delete(`/admin/posters/${deletingPoster.id}`, {
            preserveScroll: true,
            onFinish: () => {
                setBusyDelete(false);
                setDeletingPoster(null);
            },
        });
    };

    return (
        <AppLayout title="Unggah & Kelola Poster Visual Kaspro">
            <Head title="Kelola Poster Kaspro" />

            <div className="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
                <Card className="lg:col-span-5">
                    <CardHeader>
                        <CardTitle>Terbitkan Poster Edukasi Baru</CardTitle>
                        <CardDescription>
                            Tambahkan materi visual ringkas untuk kampanye kesehatan reproduksi mahasiswa.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="ap-title">Judul Poster *</Label>
                                <Input
                                    id="ap-title"
                                    name="title"
                                    required
                                    value={form.data.title}
                                    onChange={(e) => form.setData('title', e.target.value)}
                                />
                                <FieldError message={form.errors.title} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="ap-cat">Kategori Topik *</Label>
                                <Input
                                    id="ap-cat"
                                    name="category"
                                    required
                                    value={form.data.category}
                                    onChange={(e) => form.setData('category', e.target.value)}
                                    placeholder="Contoh: Higienitas / Siklus Haid / UU TPKS"
                                />
                                <FieldError message={form.errors.category} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="ap-caption">Ringkasan Pesan Edukasi *</Label>
                                <Textarea
                                    id="ap-caption"
                                    name="caption"
                                    rows={3}
                                    required
                                    value={form.data.caption}
                                    onChange={(e) => form.setData('caption', e.target.value)}
                                />
                                <FieldError message={form.errors.caption} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="ap-share">Teks Ajakan Bagikan (Opsional)</Label>
                                <Input
                                    id="ap-share"
                                    name="share_text"
                                    value={form.data.share_text}
                                    onChange={(e) => form.setData('share_text', e.target.value)}
                                />
                            </div>

                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Menerbitkan...' : 'Terbitkan Poster'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <div className="flex flex-col gap-3 lg:col-span-7">
                    <h2 className="text-base font-bold text-slate-900 dark:text-white">
                        Daftar Poster Terbit ({posters.length})
                    </h2>

                    {posters.length === 0 ? (
                        <EmptyState
                            icon={<ImageIcon className="size-8" />}
                            title="Belum ada poster Kaspro"
                        />
                    ) : (
                        posters.map((p) => (
                            <Card key={p.id} size="sm" className="flex-row items-start justify-between gap-3">
                                <div className="min-w-0 flex-1">
                                    <div className="flex items-center gap-2">
                                        <Badge color="primary" size="sm">{p.category}</Badge>
                                        <span className="text-xs text-slate-600 dark:text-slate-300">
                                            {p.download_count} unduh &middot; {p.share_count} bagikan
                                        </span>
                                    </div>
                                    <h3 className="mt-1 text-sm font-bold text-slate-900 dark:text-white">{p.title}</h3>
                                    <p className="mt-0.5 text-xs text-slate-600 dark:text-slate-300">{p.caption}</p>
                                </div>
                                <Button
                                    variant="destructive"
                                    size="icon"
                                    onClick={() => setDeletingPoster(p)}
                                    aria-label={`Hapus poster ${p.title}`}
                                >
                                    <TrashBinIcon />
                                </Button>
                            </Card>
                        ))
                    )}
                </div>
            </div>

            <Dialog open={Boolean(deletingPoster)} onOpenChange={(open) => !open && setDeletingPoster(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Hapus Poster Kaspro?</DialogTitle>
                        <DialogDescription>
                            Poster <strong>{deletingPoster?.title}</strong> akan dihapus dari galeri mahasiswa.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={() => setDeletingPoster(null)}>
                            Batal
                        </Button>
                        <Button type="button" variant="destructive" disabled={busyDelete} onClick={confirmDelete}>
                            {busyDelete ? 'Menghapus...' : 'Ya, Hapus Poster'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
