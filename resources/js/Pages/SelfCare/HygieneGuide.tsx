import { Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChevronLeftIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

const HYGIENE_TIPS = [
    {
        step: '01',
        title: 'Arah Membasuh yang Benar (Depan ke Belakang)',
        detail: 'Basuh area urogenital menggunakan air bersih mengalir dari arah depan (vulva) menuju belakang (anus) untuk mencegah perpindahan bakteri Escherichia coli ke saluran kemih.',
    },
    {
        step: '02',
        title: 'Jadwal Penggantian Pembalut Maksimal 4 Jam',
        detail: 'Saat menstruasi, ganti pembalut setiap 3 sampai 4 jam sekali meskipun belum penuh agar kelembapan tidak memicu pertumbuhan jamur Candida.',
    },
    {
        step: '03',
        title: 'Gunakan Pakaian Dalam Katun Menyerap Keringat',
        detail: 'Pilih bahan katun yang longgar dan ganti segera apabila lembap setelah berolahraga atau beraktivitas seharian di kampus.',
    },
    {
        step: '04',
        title: 'Hindari Douching & Sabun Kewanitaan Berpewangi',
        detail: 'Vagina memiliki mekanisme pembersihan alami oleh bakteri baik Lactobacillus dengan pH asam. Cairan antiseptik keras justru merusak flora pelindung tersebut.',
    },
];

export default function HygieneGuide() {
    return (
        <AppLayout
            title="Panduan Higienitas Organ Reproduksi"
            actions={
                <Link href="/selfcare" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                    <ChevronLeftIcon />
                    Kembali ke Self-Care
                </Link>
            }
        >
            <Head title="Panduan Higienitas Reproduksi" />

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                {HYGIENE_TIPS.map((tip) => (
                    <Card key={tip.step}>
                        <CardHeader>
                            <Badge color="primary">Prinsip {tip.step}</Badge>
                            <CardTitle className="mt-1 text-base">{tip.title}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm leading-relaxed text-slate-600 dark:text-slate-300">{tip.detail}</p>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </AppLayout>
    );
}
