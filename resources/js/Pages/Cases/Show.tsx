import { Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChevronLeftIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

interface CaseShowProps {
    caseStudy: {
        id: number;
        title: string;
        category: string;
        narrative: string;
        legal_analysis: string;
        medical_analysis: string;
        solution_tips: string;
    };
}

export default function Show({ caseStudy }: CaseShowProps) {
    return (
        <AppLayout
            title="Bedah Studi Kasus"
            actions={
                <Link href="/cases" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                    <ChevronLeftIcon />
                    Semua Studi Kasus
                </Link>
            }
        >
            <Head title={caseStudy.title} />

            <div className="mx-auto flex max-w-3xl flex-col gap-5">
                <Card>
                    <CardHeader>
                        <Badge color="warning">{caseStudy.category}</Badge>
                        <CardTitle className="mt-1 text-xl">{caseStudy.title}</CardTitle>
                    </CardHeader>
                    <CardContent className="flex flex-col gap-5">
                        <div>
                            <h3 className="text-xs font-bold tracking-wider text-slate-600 uppercase dark:text-slate-300">
                                Kronologi Kasus
                            </h3>
                            <p className="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-slate-800 dark:text-slate-100">
                                {caseStudy.narrative}
                            </p>
                        </div>

                        {caseStudy.medical_analysis && (
                            <div className="rounded-xl border border-teal-200 bg-teal-50/70 p-4 dark:border-teal-800 dark:bg-teal-950/40">
                                <h3 className="text-xs font-bold tracking-wider text-teal-800 uppercase dark:text-teal-200">
                                    Analisis Medis &amp; Kesehatan Reproduksi
                                </h3>
                                <p className="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-slate-800 dark:text-slate-100">
                                    {caseStudy.medical_analysis}
                                </p>
                            </div>
                        )}

                        {caseStudy.legal_analysis && (
                            <div className="rounded-xl border border-amber-300 bg-amber-50/80 p-4 dark:border-amber-700 dark:bg-amber-950/40">
                                <h3 className="text-xs font-bold tracking-wider text-amber-900 uppercase dark:text-amber-200">
                                    Tinjauan Hukum &amp; UU TPKS
                                </h3>
                                <p className="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-slate-800 dark:text-slate-100">
                                    {caseStudy.legal_analysis}
                                </p>
                            </div>
                        )}

                        {caseStudy.solution_tips && (
                            <div className="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                                <h3 className="text-xs font-bold tracking-wider text-slate-700 uppercase dark:text-slate-200">
                                    Langkah Pencegahan &amp; Rujukan Aman
                                </h3>
                                <p className="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-slate-800 dark:text-slate-100">
                                    {caseStudy.solution_tips}
                                </p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
