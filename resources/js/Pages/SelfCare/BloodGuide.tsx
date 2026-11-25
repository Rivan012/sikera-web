import { Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChevronLeftIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

const BLOOD_GUIDE = [
    {
        color: 'Merah Terang',
        hex: '#B91C1C',
        status: 'Fisiologis (Normal)',
        badge: 'success' as const,
        meaning: 'Darah segar yang keluar lancar dari rahim tanpa sempat teroksidasi lama, lazim terjadi pada hari ke-1 hingga ke-3 siklus.',
        action: 'Ganti pembalut setiap 3 sampai 4 jam dan penuhi kebutuhan cairan serta zat besi.',
    },
    {
        color: 'Cokelat Gelap / Tua',
        hex: '#5C2D06',
        status: 'Fisiologis (Oksidasi)',
        badge: 'info' as const,
        meaning: 'Darah pada awal atau penghujung haid yang alirannya lebih lambat sehingga terpapar oksigen.',
        action: 'Kondisi wajar; jaga kebersihan vulva dengan air bersih mengalir.',
    },
    {
        color: 'Merah Muda Pucat',
        hex: '#F4A28C',
        status: 'Perlu Diamati',
        badge: 'warning' as const,
        meaning: 'Bercak ringan (spotting) yang bercampur cairan serviks, dapat berkaitan dengan kadar estrogen rendah atau anemia.',
        action: 'Catat frekuensi kemunculannya; konsultasikan bila disertai pusing, siklus kacau, atau kelelahan.',
    },
    {
        color: 'Abu-abu atau Berbau Menyengat',
        hex: '#475569',
        status: 'Periksa ke Dokter',
        badge: 'error' as const,
        meaning: 'Dapat mengindikasikan infeksi bakteri vaginosis atau peradangan saluran reproduksi.',
        action: 'Hindari sabun pembersih berpewangi dan periksakan ke fasilitas kesehatan terdekat.',
    },
];

export default function BloodGuide() {
    return (
        <AppLayout
            title="Panduan Klinis Warna Darah Menstruasi"
            actions={
                <Link href="/selfcare" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                    <ChevronLeftIcon />
                    Kembali ke Self-Care
                </Link>
            }
        >
            <Head title="Panduan Warna Darah Haid" />

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                {BLOOD_GUIDE.map((item) => (
                    <Card key={item.color}>
                        <CardHeader>
                            <div className="flex items-center justify-between gap-2">
                                <div className="flex items-center gap-2.5">
                                    <span
                                        className="size-5 rounded-sm border border-slate-300"
                                        style={{ backgroundColor: item.hex }}
                                        aria-hidden="true"
                                    />
                                    <CardTitle className="text-base">{item.color}</CardTitle>
                                </div>
                                <Badge color={item.badge}>{item.status}</Badge>
                            </div>
                            <CardDescription>{item.meaning}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="rounded-lg bg-slate-50 p-3 text-xs font-medium text-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                Anjuran tindakan: {item.action}
                            </p>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </AppLayout>
    );
}
