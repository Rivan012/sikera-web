import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { DownloadIcon, ImageIcon, ShareIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import { cn } from '@/utils';

interface PosterItem {
    id: number;
    title: string;
    category: string;
    image_path: string;
    caption: string;
    share_text: string;
    download_count: number;
    share_count: number;
}

interface PostersIndexProps {
    posters: PosterItem[];
}

export default function Index({ posters }: PostersIndexProps) {
    const [activeCategory, setActiveCategory] = useState<string>('Semua');
    const [copiedId, setCopiedId] = useState<number | null>(null);

    const categories = ['Semua', ...Array.from(new Set(posters.map((p) => p.category).filter(Boolean)))];
    const filtered = activeCategory === 'Semua' ? posters : posters.filter((p) => p.category === activeCategory);

    const handleDownload = (poster: PosterItem) => {
        router.post(`/posters/${poster.id}/download`, {}, { preserveScroll: true });
        if (poster.image_path) {
            const link = document.createElement('a');
            link.href = poster.image_path.startsWith('http') ? poster.image_path : `/${poster.image_path.replace(/^\//, '')}`;
            link.download = `${poster.title}.png`;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    };

    const handleShare = async (poster: PosterItem) => {
        const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '';
        try {
            await fetch(`/posters/${poster.id}/share`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            });
        } catch {
            // Tetap lanjutkan pembagian meskipun pencatatan metrik tertunda.
        }

        const shareText = `${poster.share_text || poster.title} - ${window.location.origin}/posters`;
        if (navigator.share) {
            try {
                await navigator.share({ title: poster.title, text: poster.share_text || poster.title, url: window.location.href });
                return;
            } catch {
                // Pengguna menutup lembar berbagi bawaan.
            }
        }

        try {
            await navigator.clipboard.writeText(shareText);
            setCopiedId(poster.id);
            window.setTimeout(() => setCopiedId(null), 2500);
        } catch {
            window.open(`https://wa.me/?text=${encodeURIComponent(shareText)}`, '_blank', 'noopener,noreferrer');
        }
    };

    return (
        <AppLayout title="Visual Poster Edukasi Kaspro">
            <Head title="Poster Kaspro" />

            <div className="flex flex-col gap-6">
                {categories.length > 1 && (
                    <div className="flex gap-2 overflow-x-auto pb-1" role="tablist" aria-label="Filter kategori poster">
                        {categories.map((cat) => {
                            const selected = cat === activeCategory;
                            return (
                                <button
                                    key={cat}
                                    type="button"
                                    role="tab"
                                    aria-selected={selected}
                                    onClick={() => setActiveCategory(cat)}
                                    className={cn(
                                        'inline-flex min-h-11 shrink-0 items-center rounded-lg border px-3.5 text-xs font-semibold transition-colors',
                                        selected
                                            ? 'border-teal-700 bg-teal-700 text-white dark:border-teal-400 dark:bg-teal-400 dark:text-slate-950'
                                            : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200',
                                    )}
                                >
                                    {cat}
                                </button>
                            );
                        })}
                    </div>
                )}

                {filtered.length === 0 ? (
                    <EmptyState
                        icon={<ImageIcon className="size-8" />}
                        title="Belum ada poster edukasi"
                        description="Poster visual Kaspro akan tampil pada galeri ini setelah diterbitkan."
                    />
                ) : (
                    <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        {filtered.map((poster) => {
                            const src = poster.image_path
                                ? poster.image_path.startsWith('http')
                                    ? poster.image_path
                                    : `/${poster.image_path.replace(/^\//, '')}`
                                : null;

                            return (
                                <Card key={poster.id} className="justify-between overflow-hidden p-0 sm:p-0">
                                    <div>
                                        {src && (
                                            <div className="aspect-[4/3] w-full overflow-hidden border-b border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-900">
                                                <img
                                                    src={src}
                                                    alt={poster.title}
                                                    width={600}
                                                    height={450}
                                                    loading="lazy"
                                                    className="h-full w-full object-cover"
                                                />
                                            </div>
                                        )}
                                        <CardContent className="flex flex-col gap-2 p-5">
                                            <div className="flex items-center justify-between gap-2">
                                                <Badge color="primary" size="sm">{poster.category}</Badge>
                                                <span className="text-xs text-slate-600 dark:text-slate-300">
                                                    {poster.download_count} unduh &middot; {poster.share_count} bagikan
                                                </span>
                                            </div>
                                            <h2 className="text-base font-bold text-slate-900 dark:text-white">{poster.title}</h2>
                                            <p className="text-sm leading-relaxed text-slate-600 dark:text-slate-300">{poster.caption}</p>
                                        </CardContent>
                                    </div>

                                    <div className="flex items-center gap-2 border-t border-slate-200 px-5 py-3.5 dark:border-slate-700">
                                        <Button variant="default" size="sm" className="flex-1" onClick={() => handleDownload(poster)}>
                                            <DownloadIcon />
                                            Unduh
                                        </Button>
                                        <button
                                            type="button"
                                            onClick={() => void handleShare(poster)}
                                            className={cn(buttonVariants({ variant: 'outline', size: 'sm' }), 'flex-1')}
                                        >
                                            <ShareIcon />
                                            {copiedId === poster.id ? 'Tersalin!' : 'Bagikan'}
                                        </button>
                                    </div>
                                </Card>
                            );
                        })}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
