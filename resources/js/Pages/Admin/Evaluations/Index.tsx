import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pagination } from '@/components/ui/pagination';
import { Select } from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    TableWrapper,
} from '@/components/ui/table';
import { DownloadIcon, SearchIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { PaginatedData } from '@/types/sikera';

interface EvaluationRow {
    id: number;
    type: 'pre_test' | 'post_test';
    module_number: number | null;
    total_score: number;
    n_gain_score: number | null;
    submitted_at: string | null;
    user: {
        id: number;
        name: string;
        initials: string | null;
        nim: string | null;
        prodi: string | null;
        fakultas: string | null;
    } | null;
}

interface AdminEvaluationsIndexProps {
    evaluations: PaginatedData<EvaluationRow>;
    typeFilter: string;
    moduleFilter: string;
    searchQuery: string;
    totalEvaluations: number;
    totalPosttests: number;
    totalPretests: number;
    avgPostScore: number;
    avgPreScore: number;
    avgNGain: number;
    highGainCount: number;
    mediumGainCount: number;
    lowGainCount: number;
}

export default function Index({
    evaluations,
    typeFilter,
    moduleFilter,
    searchQuery,
    totalEvaluations,
    totalPosttests,
    totalPretests,
    avgPostScore,
    avgPreScore,
    avgNGain,
    highGainCount,
    mediumGainCount,
    lowGainCount,
}: AdminEvaluationsIndexProps) {
    const [typeVal, setTypeVal] = useState(typeFilter || 'all');
    const [modVal, setModVal] = useState(moduleFilter || 'all');
    const [qVal, setQVal] = useState(searchQuery || '');

    const applyFilters = (nextType = typeVal, nextMod = modVal, nextQ = qVal) => {
        router.get(
            '/admin/evaluations',
            {
                ...(nextType !== 'all' ? { type: nextType } : {}),
                ...(nextMod !== 'all' ? { module: nextMod } : {}),
                ...(nextQ.trim() ? { q: nextQ.trim() } : {}),
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const exportHref = `/admin/evaluations/export?${new URLSearchParams({
        ...(modVal !== 'all' ? { module: modVal } : {}),
        ...(qVal.trim() ? { q: qVal.trim() } : {}),
    }).toString()}`;

    return (
        <AppLayout
            title="Analisis & Lembar Jawaban Evaluasi"
            actions={
                <a href={exportHref} className={buttonVariants({ variant: 'default', size: 'sm' })}>
                    <DownloadIcon />
                    Ekspor Excel Post-Test
                </a>
            }
        >
            <Head title="Jawaban Evaluasi" />

            <div className="flex flex-col gap-5">
                <div className="grid grid-cols-2 gap-4 lg:grid-cols-6">
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Total Lembar</p>
                        <p className="mt-1 text-2xl font-bold">{totalEvaluations}</p>
                    </Card>
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Pre-Test ({totalPretests})</p>
                        <p className="mt-1 text-2xl font-bold">{avgPreScore}</p>
                    </Card>
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Post-Test ({totalPosttests})</p>
                        <p className="mt-1 text-2xl font-bold">{avgPostScore}</p>
                    </Card>
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Rata-rata N-Gain</p>
                        <p className="mt-1 text-2xl font-bold text-teal-700 dark:text-teal-300">{avgNGain}</p>
                    </Card>
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">N-Gain Tinggi</p>
                        <p className="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300">{highGainCount}</p>
                    </Card>
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Sedang / Rendah</p>
                        <p className="mt-1 text-2xl font-bold">{mediumGainCount} / {lowGainCount}</p>
                    </Card>
                </div>

                <Card size="sm">
                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            applyFilters();
                        }}
                        className="grid grid-cols-1 gap-3 sm:grid-cols-12"
                    >
                        <div className="sm:col-span-3">
                            <Label htmlFor="ev-type" className="sr-only">Filter Jenis</Label>
                            <Select
                                id="ev-type"
                                value={typeVal}
                                onChange={(e) => {
                                    setTypeVal(e.target.value);
                                    applyFilters(e.target.value, modVal, qVal);
                                }}
                            >
                                <option value="all">Semua Jenis Evaluasi</option>
                                <option value="pre_test">Pre-Test (per Modul)</option>
                                <option value="post_test">Post-Test Akhir (Umum)</option>
                            </Select>
                        </div>

                        <div className="sm:col-span-3">
                            <Label htmlFor="ev-mod" className="sr-only">Filter Modul</Label>
                            <Select
                                id="ev-mod"
                                value={modVal}
                                onChange={(e) => {
                                    setModVal(e.target.value);
                                    applyFilters(typeVal, e.target.value, qVal);
                                }}
                            >
                                <option value="all">Semua Cakupan</option>
                                <option value="general">Umum (Tanpa Modul)</option>
                                <option value="1">Modul 1</option>
                                <option value="2">Modul 2</option>
                                <option value="3">Modul 3</option>
                                <option value="4">Modul 4</option>
                            </Select>
                        </div>

                        <div className="flex gap-2 sm:col-span-6">
                            <Label htmlFor="ev-q" className="sr-only">Cari responden</Label>
                            <Input
                                id="ev-q"
                                value={qVal}
                                onChange={(e) => setQVal(e.target.value)}
                                placeholder="Cari nama, NIM, atau program studi..."
                            />
                            <Button type="submit" variant="outline" size="icon" aria-label="Cari jawaban evaluasi">
                                <SearchIcon />
                            </Button>
                        </div>
                    </form>
                </Card>

                <TableWrapper>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Responden</TableHead>
                                <TableHead>Program Studi</TableHead>
                                <TableHead>Instrumen</TableHead>
                                <TableHead>Skor</TableHead>
                                <TableHead>N-Gain</TableHead>
                                <TableHead>Waktu</TableHead>
                                <TableHead>Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {evaluations.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={7} className="py-8 text-center text-slate-600 dark:text-slate-300">
                                        Tidak ada lembar evaluasi yang cocok dengan filter.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                evaluations.data.map((row) => (
                                    <TableRow key={row.id}>
                                        <TableCell>
                                            <div className="font-semibold">{row.user?.name || 'Mahasiswa'}</div>
                                            <div className="text-xs text-slate-600 dark:text-slate-300">{row.user?.nim || '-'}</div>
                                        </TableCell>
                                        <TableCell className="text-xs">{row.user?.prodi || '-'}</TableCell>
                                        <TableCell>
                                            {row.type === 'post_test' ? (
                                                <Badge color="success" size="sm">Post-Test Akhir (Umum)</Badge>
                                            ) : (
                                                <Badge color="info" size="sm">
                                                    Pre-Test {row.module_number ? `Modul ${row.module_number}` : 'Umum'}
                                                </Badge>
                                            )}
                                        </TableCell>
                                        <TableCell className="font-bold">{row.total_score}</TableCell>
                                        <TableCell>{row.n_gain_score ?? '-'}</TableCell>
                                        <TableCell className="text-xs">{row.submitted_at || '-'}</TableCell>
                                        <TableCell>
                                            <Link
                                                href={`/admin/evaluations/${row.id}`}
                                                className="text-xs font-semibold text-teal-700 hover:underline dark:text-teal-300"
                                            >
                                                Rincian Jawaban
                                            </Link>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </TableWrapper>

                <Pagination links={evaluations.links} />
            </div>
        </AppLayout>
    );
}
