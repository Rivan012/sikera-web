import { Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { ChevronRightIcon, FileTextIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

interface CaseStudyItem {
    id: number;
    title: string;
    category: string;
    narrative: string;
    legal_analysis: string;
    medical_analysis: string;
    solution_tips: string;
}

interface CasesIndexProps {
    caseStudies: CaseStudyItem[];
}

export default function Index({ caseStudies }: CasesIndexProps) {
    return (
        <AppLayout title="Studi Kasus Nyata Kampus & Indekos">
            <Head title="Studi Kasus Kampus" />

            {caseStudies.length === 0 ? (
                <EmptyState
                    icon={<FileTextIcon className="size-8" />}
                    title="Belum ada studi kasus"
                    description="Kumpulan studi kasus hukum UU TPKS dan kesehatan reproduksi akan tampil di halaman ini."
                />
            ) : (
                <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                    {caseStudies.map((cs) => (
                        <Card key={cs.id} className="justify-between">
                            <div>
                                <CardHeader>
                                    <Badge color="warning">{cs.category}</Badge>
                                    <CardTitle className="mt-1 text-lg">{cs.title}</CardTitle>
                                </CardHeader>
                                <CardContent className="mt-3">
                                    <p className="line-clamp-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                                        {cs.narrative}
                                    </p>
                                </CardContent>
                            </div>
                            <div className="flex justify-end border-t border-slate-200 pt-4 dark:border-slate-700">
                                <Link href={`/cases/${cs.id}`} className={buttonVariants({ variant: 'default', size: 'sm' })}>
                                    Baca Analisis Medis &amp; Hukum
                                    <ChevronRightIcon />
                                </Link>
                            </div>
                        </Card>
                    ))}
                </div>
            )}
        </AppLayout>
    );
}
