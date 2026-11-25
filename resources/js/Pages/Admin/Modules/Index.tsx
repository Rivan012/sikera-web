import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
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
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import {
    BookOpenIcon,
    ClipboardListIcon,
    DownloadIcon,
    PencilIcon,
    PlusIcon,
    TrashBinIcon,
    UploadIcon,
} from '@/icons';
import AppLayout from '@/layouts/AppLayout';

interface AdminTopicItem {
    id: number;
    topic_code: string;
    title: string;
    youtube_video_id: string | null;
    has_pdf: boolean;
    pdf_original_name: string | null;
    pdf_size_label: string | null;
}

interface AdminModuleRow {
    id: number;
    module_number: number;
    title: string;
    subtitle: string | null;
    description: string | null;
    banner_image: string;
    raw_banner_image: string | null;
    estimated_time: string | number | null;
    topics: AdminTopicItem[];
}

interface AdminModulesProps {
    modules: AdminModuleRow[];
    totalTopics: number;
    pdfTopicsCount: number;
    nextModuleNumber: number;
}

const BANNER_THEMES = [
    { value: 'teal', label: 'Hijau Teal Akademik' },
    { value: 'emerald', label: 'Hijau Emerald' },
    { value: 'amber', label: 'Kuning Ochre' },
    { value: 'rose', label: 'Merah Terakota' },
    { value: 'indigo', label: 'Biru Indigo' },
] as const;

export default function Index({ modules, totalTopics, pdfTopicsCount, nextModuleNumber }: AdminModulesProps) {
    const [createOpen, setCreateOpen] = useState(false);
    const [importOpen, setImportOpen] = useState(false);
    const [addTopicModule, setAddTopicModule] = useState<AdminModuleRow | null>(null);
    const [editingTopic, setEditingTopic] = useState<{ module: AdminModuleRow; topic: AdminTopicItem } | null>(null);
    const [deletingModule, setDeletingModule] = useState<AdminModuleRow | null>(null);
    const [deletingTopic, setDeletingTopic] = useState<AdminTopicItem | null>(null);
    const [busyDelete, setBusyDelete] = useState(false);

    const createForm = useForm({
        module_number: String(nextModuleNumber),
        title: '',
        subtitle: '',
        description: '',
        estimated_time: '20 Menit',
        banner_mode: 'auto' as 'auto' | 'upload' | 'url',
        banner_theme: 'teal',
        banner_image: '',
        banner_file: null as File | null,
        topic_code: `${nextModuleNumber}.1`,
        topic_title: '',
        youtube_video_id: '',
        pdf_file: null as File | null,
    });

    const addTopicForm = useForm({
        topic_code: '',
        title: '',
        youtube_video_id: '',
        pdf_file: null as File | null,
    });

    const editTopicForm = useForm({
        _method: 'put',
        topic_code: '',
        title: '',
        youtube_video_id: '',
        pdf_file: null as File | null,
    });

    const importForm = useForm({
        excel_file: null as File | null,
        overwrite: true,
    });

    const previewSvgUrl = `/admin/modules/preview-banner?number=${encodeURIComponent(createForm.data.module_number || '1')}&title=${encodeURIComponent(createForm.data.title || 'Judul Modul Edukasi')}&subtitle=${encodeURIComponent(createForm.data.subtitle || '')}&theme=${encodeURIComponent(createForm.data.banner_theme)}`;

    const openAddTopic = (mod: AdminModuleRow) => {
        setAddTopicModule(mod);
        addTopicForm.clearErrors();
        addTopicForm.setData({
            topic_code: `${mod.module_number}.${mod.topics.length + 1}`,
            title: '',
            youtube_video_id: '',
            pdf_file: null,
        });
    };

    const openEditTopic = (mod: AdminModuleRow, topic: AdminTopicItem) => {
        setEditingTopic({ module: mod, topic });
        editTopicForm.clearErrors();
        editTopicForm.setData({
            _method: 'put',
            topic_code: topic.topic_code,
            title: topic.title,
            youtube_video_id: topic.youtube_video_id ?? '',
            pdf_file: null,
        });
    };

    const submitCreateModule = (event: FormEvent) => {
        event.preventDefault();
        createForm.post('/admin/modules', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                setCreateOpen(false);
                createForm.reset();
            },
        });
    };

    const submitAddTopic = (event: FormEvent) => {
        event.preventDefault();
        if (!addTopicModule) {
            return;
        }
        addTopicForm.post(`/admin/modules/${addTopicModule.id}/topics`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => setAddTopicModule(null),
        });
    };

    const submitEditTopic = (event: FormEvent) => {
        event.preventDefault();
        if (!editingTopic) {
            return;
        }
        editTopicForm.post(`/admin/topics/${editingTopic.topic.id}`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => setEditingTopic(null),
        });
    };

    const submitImportExcel = (event: FormEvent) => {
        event.preventDefault();
        importForm.post('/admin/modules/import', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => setImportOpen(false),
        });
    };

    const confirmDeleteModule = () => {
        if (!deletingModule) {
            return;
        }
        setBusyDelete(true);
        router.delete(`/admin/modules/${deletingModule.id}`, {
            preserveScroll: true,
            onFinish: () => {
                setBusyDelete(false);
                setDeletingModule(null);
            },
        });
    };

    const confirmDeleteTopic = () => {
        if (!deletingTopic) {
            return;
        }
        setBusyDelete(true);
        router.delete(`/admin/topics/${deletingTopic.id}`, {
            preserveScroll: true,
            onFinish: () => {
                setBusyDelete(false);
                setDeletingTopic(null);
            },
        });
    };

    return (
        <AppLayout
            title="Kelola Modul & Unggah Subbab PDF Flipbook"
            actions={
                <div className="flex flex-wrap gap-2">
                    <a href="/admin/modules/template" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                        <DownloadIcon />
                        Template Excel
                    </a>
                    <Button variant="outline" size="sm" onClick={() => setImportOpen(true)}>
                        <UploadIcon />
                        Impor Excel
                    </Button>
                    <Button size="sm" onClick={() => setCreateOpen(true)}>
                        <PlusIcon />
                        Buat Modul Baru
                    </Button>
                </div>
            }
        >
            <Head title="Kelola Modul PDF" />

            <div className="flex flex-col gap-5">
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Total Modul Aktif</p>
                        <p className="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{modules.length}</p>
                    </Card>
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Total Subbab Materi</p>
                        <p className="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{totalTopics}</p>
                    </Card>
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Subbab Berkas PDF Flipbook</p>
                        <p className="mt-1 text-2xl font-bold text-teal-700 dark:text-teal-300">{pdfTopicsCount}</p>
                    </Card>
                </div>

                {modules.length === 0 ? (
                    <EmptyState
                        icon={<BookOpenIcon className="size-8" />}
                        title="Belum ada modul edukasi"
                        description="Tambahkan modul pertama beserta berkas PDF subbab untuk mulai menampilkan pembaca flipbook."
                        action={
                            <Button size="sm" onClick={() => setCreateOpen(true)}>
                                <PlusIcon />
                                Buat Modul Pertama
                            </Button>
                        }
                    />
                ) : (
                    modules.map((mod) => (
                        <Card key={mod.id}>
                            <CardHeader>
                                <div className="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                                    <div>
                                        <div className="flex flex-wrap items-center gap-2">
                                            <Badge color="primary">Modul {mod.module_number}</Badge>
                                            <Badge color="gray">{mod.topics.length} Subbab</Badge>
                                            {mod.estimated_time && <Badge color="info">{mod.estimated_time}</Badge>}
                                        </div>
                                        <CardTitle className="mt-2 text-lg">{mod.title}</CardTitle>
                                        {mod.description && (
                                            <CardDescription className="mt-1">{mod.description}</CardDescription>
                                        )}
                                    </div>

                                    <div className="flex flex-wrap items-center gap-2">
                                        <Link
                                            href={`/admin/pretest-questions?module=${mod.module_number}`}
                                            className={buttonVariants({ variant: 'outline', size: 'xs' })}
                                        >
                                            <ClipboardListIcon />
                                            Soal Pre-Test
                                        </Link>
                                        <Button variant="default" size="xs" onClick={() => openAddTopic(mod)}>
                                            <PlusIcon />
                                            Tambah Subbab PDF
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                            onClick={() => setDeletingModule(mod)}
                                            aria-label={`Hapus Modul ${mod.module_number}`}
                                        >
                                            <TrashBinIcon />
                                        </Button>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent>
                                {mod.topics.length === 0 ? (
                                    <p className="text-sm text-slate-600 dark:text-slate-300">
                                        Belum ada subbab pada modul ini.
                                    </p>
                                ) : (
                                    <div className="divide-y divide-slate-200 rounded-xl border border-slate-200 dark:divide-slate-700 dark:border-slate-700">
                                        {mod.topics.map((tp) => (
                                            <div key={tp.id} className="flex flex-col justify-between gap-3 p-3.5 sm:flex-row sm:items-center">
                                                <div className="min-w-0">
                                                    <div className="flex flex-wrap items-center gap-2">
                                                        <span className="rounded bg-teal-50 px-2 py-0.5 font-mono text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-200">
                                                            {tp.topic_code}
                                                        </span>
                                                        <span className="font-semibold text-slate-900 dark:text-white">{tp.title}</span>
                                                    </div>
                                                    <div className="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                                        {tp.has_pdf ? (
                                                            <Badge color="success" size="sm">
                                                                PDF: {tp.pdf_original_name || 'Tersedia'} {tp.pdf_size_label ? `(${tp.pdf_size_label})` : ''}
                                                            </Badge>
                                                        ) : (
                                                            <Badge color="warning" size="sm">Belum Ada PDF</Badge>
                                                        )}
                                                        {tp.youtube_video_id && (
                                                            <span>YouTube: {tp.youtube_video_id}</span>
                                                        )}
                                                    </div>
                                                </div>

                                                <div className="flex shrink-0 items-center gap-1.5">
                                                    <Link
                                                        href={`/modules/${mod.id}/topic/${tp.id}`}
                                                        className={buttonVariants({ variant: 'ghost', size: 'xs' })}
                                                    >
                                                        Pratinjau
                                                    </Link>
                                                    <Button
                                                        variant="outline"
                                                        size="icon"
                                                        onClick={() => openEditTopic(mod, tp)}
                                                        aria-label={`Edit subbab ${tp.topic_code}`}
                                                    >
                                                        <PencilIcon />
                                                    </Button>
                                                    <Button
                                                        variant="destructive"
                                                        size="icon"
                                                        onClick={() => setDeletingTopic(tp)}
                                                        aria-label={`Hapus subbab ${tp.topic_code}`}
                                                    >
                                                        <TrashBinIcon />
                                                    </Button>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    ))
                )}
            </div>

            <Dialog open={createOpen} onOpenChange={setCreateOpen}>
                <DialogContent className="sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>Buat Modul Baru &amp; Unggah PDF Subbab Pertama</DialogTitle>
                        <DialogDescription>
                            Setiap modul baru wajib menyertakan minimal satu berkas PDF subbab agar siap dibuka sebagai flipbook.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={submitCreateModule} className="flex flex-col gap-4">
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-12">
                            <div className="flex flex-col gap-1 sm:col-span-3">
                                <Label htmlFor="m-num">Nomor Modul *</Label>
                                <Input
                                    id="m-num"
                                    type="number"
                                    min={1}
                                    required
                                    value={createForm.data.module_number}
                                    onChange={(e) => {
                                        const num = e.target.value;
                                        createForm.setData((prev) => ({
                                            ...prev,
                                            module_number: num,
                                            topic_code: `${num}.1`,
                                        }));
                                    }}
                                />
                                <FieldError message={createForm.errors.module_number} />
                            </div>
                            <div className="flex flex-col gap-1 sm:col-span-6">
                                <Label htmlFor="m-title">Judul Modul *</Label>
                                <Input
                                    id="m-title"
                                    required
                                    value={createForm.data.title}
                                    onChange={(e) => createForm.setData('title', e.target.value)}
                                />
                                <FieldError message={createForm.errors.title} />
                            </div>
                            <div className="flex flex-col gap-1 sm:col-span-3">
                                <Label htmlFor="m-time">Estimasi Waktu *</Label>
                                <Input
                                    id="m-time"
                                    required
                                    value={createForm.data.estimated_time}
                                    onChange={(e) => createForm.setData('estimated_time', e.target.value)}
                                />
                                <FieldError message={createForm.errors.estimated_time} />
                            </div>
                        </div>

                        <div className="flex flex-col gap-1">
                            <Label htmlFor="m-sub">Subjudul (Opsional)</Label>
                            <Input
                                id="m-sub"
                                value={createForm.data.subtitle}
                                onChange={(e) => createForm.setData('subtitle', e.target.value)}
                            />
                        </div>

                        <div className="flex flex-col gap-1">
                            <Label htmlFor="m-desc">Deskripsi Modul *</Label>
                            <Textarea
                                id="m-desc"
                                rows={2}
                                required
                                value={createForm.data.description}
                                onChange={(e) => createForm.setData('description', e.target.value)}
                            />
                            <FieldError message={createForm.errors.description} />
                        </div>

                        <div className="grid grid-cols-1 gap-3 rounded-xl border border-slate-200 p-3.5 sm:grid-cols-2 dark:border-slate-700">
                            <div className="flex flex-col gap-2">
                                <Label htmlFor="m-banner-mode">Mode Sampul Banner</Label>
                                <Select
                                    id="m-banner-mode"
                                    value={createForm.data.banner_mode}
                                    onChange={(e) => createForm.setData('banner_mode', e.target.value as 'auto' | 'upload' | 'url')}
                                >
                                    <option value="auto">Buat Otomatis (SVG)</option>
                                    <option value="upload">Unggah Gambar Sendiri</option>
                                    <option value="url">Tautan Gambar URL</option>
                                </Select>

                                {createForm.data.banner_mode === 'auto' && (
                                    <Select
                                        aria-label="Tema warna banner"
                                        value={createForm.data.banner_theme}
                                        onChange={(e) => createForm.setData('banner_theme', e.target.value)}
                                    >
                                        {BANNER_THEMES.map((t) => (
                                            <option key={t.value} value={t.value}>{t.label}</option>
                                        ))}
                                    </Select>
                                )}

                                {createForm.data.banner_mode === 'upload' && (
                                    <Input
                                        type="file"
                                        accept="image/*"
                                        onChange={(e) => createForm.setData('banner_file', e.target.files?.[0] ?? null)}
                                    />
                                )}

                                {createForm.data.banner_mode === 'url' && (
                                    <Input
                                        placeholder="https://..."
                                        value={createForm.data.banner_image}
                                        onChange={(e) => createForm.setData('banner_image', e.target.value)}
                                    />
                                )}
                            </div>

                            {createForm.data.banner_mode === 'auto' && (
                                <div className="overflow-hidden rounded-lg border border-slate-200 bg-slate-100 dark:border-slate-700">
                                    <img
                                        src={previewSvgUrl}
                                        alt="Pratinjau sampul otomatis"
                                        width={400}
                                        height={160}
                                        className="h-28 w-full object-cover"
                                    />
                                </div>
                            )}
                        </div>

                        <fieldset className="flex flex-col gap-3 rounded-xl border border-teal-200 bg-teal-50/40 p-4 dark:border-teal-800 dark:bg-teal-950/30">
                            <legend className="px-2 text-xs font-bold text-teal-900 dark:text-teal-200">
                                Subbab Pertama (Berkas PDF Flipbook)
                            </legend>
                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-12">
                                <div className="flex flex-col gap-1 sm:col-span-3">
                                    <Label htmlFor="m-tcode">Kode Subbab *</Label>
                                    <Input
                                        id="m-tcode"
                                        required
                                        value={createForm.data.topic_code}
                                        onChange={(e) => createForm.setData('topic_code', e.target.value)}
                                    />
                                    <FieldError message={createForm.errors.topic_code} />
                                </div>
                                <div className="flex flex-col gap-1 sm:col-span-9">
                                    <Label htmlFor="m-ttitle">Judul Subbab *</Label>
                                    <Input
                                        id="m-ttitle"
                                        required
                                        value={createForm.data.topic_title}
                                        onChange={(e) => createForm.setData('topic_title', e.target.value)}
                                    />
                                    <FieldError message={createForm.errors.topic_title} />
                                </div>
                            </div>

                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div className="flex flex-col gap-1">
                                    <Label htmlFor="m-pdf">Berkas Materi PDF (Maks 20 MB) *</Label>
                                    <Input
                                        id="m-pdf"
                                        name="pdf_file"
                                        type="file"
                                        accept="application/pdf,.pdf"
                                        required
                                        onChange={(e) => createForm.setData('pdf_file', e.target.files?.[0] ?? null)}
                                    />
                                    <FieldError message={createForm.errors.pdf_file} />
                                </div>
                                <div className="flex flex-col gap-1">
                                    <Label htmlFor="m-yt">ID / URL Video YouTube (Opsional)</Label>
                                    <Input
                                        id="m-yt"
                                        value={createForm.data.youtube_video_id}
                                        onChange={(e) => createForm.setData('youtube_video_id', e.target.value)}
                                        placeholder="Contoh: v3F4QW_h32M"
                                    />
                                </div>
                            </div>
                        </fieldset>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setCreateOpen(false)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={createForm.processing}>
                                {createForm.processing ? 'Mengunggah PDF...' : 'Publikasikan Modul & PDF'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog open={Boolean(addTopicModule)} onOpenChange={(open) => !open && setAddTopicModule(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Tambah Subbab PDF ke Modul {addTopicModule?.module_number}</DialogTitle>
                        <DialogDescription>
                            Unggah berkas PDF materi yang akan ditampilkan sebagai flipbook interaktif.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={submitAddTopic} className="flex flex-col gap-4">
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-12">
                            <div className="flex flex-col gap-1 sm:col-span-4">
                                <Label htmlFor="at-code">Kode Subbab *</Label>
                                <Input
                                    id="at-code"
                                    required
                                    value={addTopicForm.data.topic_code}
                                    onChange={(e) => addTopicForm.setData('topic_code', e.target.value)}
                                />
                                <FieldError message={addTopicForm.errors.topic_code} />
                            </div>
                            <div className="flex flex-col gap-1 sm:col-span-8">
                                <Label htmlFor="at-title">Judul Subbab *</Label>
                                <Input
                                    id="at-title"
                                    required
                                    value={addTopicForm.data.title}
                                    onChange={(e) => addTopicForm.setData('title', e.target.value)}
                                />
                                <FieldError message={addTopicForm.errors.title} />
                            </div>
                        </div>

                        <div className="flex flex-col gap-1">
                            <Label htmlFor="at-pdf">Berkas PDF Subbab (Maks 20 MB) *</Label>
                            <Input
                                id="at-pdf"
                                name="pdf_file"
                                type="file"
                                accept="application/pdf,.pdf"
                                required
                                onChange={(e) => addTopicForm.setData('pdf_file', e.target.files?.[0] ?? null)}
                            />
                            <FieldError message={addTopicForm.errors.pdf_file} />
                        </div>

                        <div className="flex flex-col gap-1">
                            <Label htmlFor="at-yt">ID / URL Video YouTube (Opsional)</Label>
                            <Input
                                id="at-yt"
                                value={addTopicForm.data.youtube_video_id}
                                onChange={(e) => addTopicForm.setData('youtube_video_id', e.target.value)}
                            />
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setAddTopicModule(null)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={addTopicForm.processing}>
                                {addTopicForm.processing ? 'Mengunggah...' : 'Simpan Subbab PDF'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog open={Boolean(editingTopic)} onOpenChange={(open) => !open && setEditingTopic(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Edit Subbab {editingTopic?.topic.topic_code}</DialogTitle>
                        <DialogDescription>
                            Perbarui judul atau unggah berkas PDF baru untuk menggantikan PDF saat ini.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={submitEditTopic} className="flex flex-col gap-4">
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-12">
                            <div className="flex flex-col gap-1 sm:col-span-4">
                                <Label htmlFor="et-code">Kode Subbab *</Label>
                                <Input
                                    id="et-code"
                                    required
                                    value={editTopicForm.data.topic_code}
                                    onChange={(e) => editTopicForm.setData('topic_code', e.target.value)}
                                />
                                <FieldError message={editTopicForm.errors.topic_code} />
                            </div>
                            <div className="flex flex-col gap-1 sm:col-span-8">
                                <Label htmlFor="et-title">Judul Subbab *</Label>
                                <Input
                                    id="et-title"
                                    required
                                    value={editTopicForm.data.title}
                                    onChange={(e) => editTopicForm.setData('title', e.target.value)}
                                />
                                <FieldError message={editTopicForm.errors.title} />
                            </div>
                        </div>

                        <div className="flex flex-col gap-1">
                            <Label htmlFor="et-pdf">Ganti Berkas PDF (Kosongkan bila tetap)</Label>
                            <Input
                                id="et-pdf"
                                name="pdf_file"
                                type="file"
                                accept="application/pdf,.pdf"
                                onChange={(e) => editTopicForm.setData('pdf_file', e.target.files?.[0] ?? null)}
                            />
                            <FieldError message={editTopicForm.errors.pdf_file} />
                        </div>

                        <div className="flex flex-col gap-1">
                            <Label htmlFor="et-yt">ID / URL Video YouTube</Label>
                            <Input
                                id="et-yt"
                                value={editTopicForm.data.youtube_video_id}
                                onChange={(e) => editTopicForm.setData('youtube_video_id', e.target.value)}
                            />
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setEditingTopic(null)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={editTopicForm.processing}>
                                {editTopicForm.processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog open={importOpen} onOpenChange={setImportOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Impor Struktur Modul dari Excel</DialogTitle>
                        <DialogDescription>
                            Unggah berkas `.xlsx` sesuai format template SIKERA untuk menyinkronkan metadata modul dan butir soal Pre-Test.
                        </DialogDescription>
                    </DialogHeader>
                    <form onSubmit={submitImportExcel} className="flex flex-col gap-4">
                        <div className="flex flex-col gap-1">
                            <Label htmlFor="imp-excel">Berkas Excel (.xlsx / .xls) *</Label>
                            <Input
                                id="imp-excel"
                                type="file"
                                accept=".xlsx,.xls"
                                required
                                onChange={(e) => importForm.setData('excel_file', e.target.files?.[0] ?? null)}
                            />
                            <FieldError message={importForm.errors.excel_file} />
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setImportOpen(false)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={importForm.processing}>
                                {importForm.processing ? 'Mengimpor...' : 'Mulai Impor'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog open={Boolean(deletingModule)} onOpenChange={(open) => !open && setDeletingModule(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Hapus Modul {deletingModule?.module_number}?</DialogTitle>
                        <DialogDescription>
                            Seluruh subbab PDF dan soal Pre-Test milik Modul {deletingModule?.module_number} akan dihapus. Bank soal Post-Test umum tetap dipertahankan.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={() => setDeletingModule(null)}>
                            Batal
                        </Button>
                        <Button type="button" variant="destructive" disabled={busyDelete} onClick={confirmDeleteModule}>
                            {busyDelete ? 'Menghapus...' : 'Ya, Hapus Modul'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <Dialog open={Boolean(deletingTopic)} onOpenChange={(open) => !open && setDeletingTopic(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Hapus Subbab {deletingTopic?.topic_code}?</DialogTitle>
                        <DialogDescription>
                            Subbab <strong>{deletingTopic?.title}</strong> beserta berkas PDF-nya akan dihapus dari penyimpanan.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={() => setDeletingTopic(null)}>
                            Batal
                        </Button>
                        <Button type="button" variant="destructive" disabled={busyDelete} onClick={confirmDeleteTopic}>
                            {busyDelete ? 'Menghapus...' : 'Ya, Hapus Subbab'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
