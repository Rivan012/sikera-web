import { Head } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    TableWrapper,
} from '@/components/ui/table';
import { formatDateId } from '@/constants/akademik';
import { DownloadIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';

interface SyncLogRow {
    id: number;
    sheet_tab: string | null;
    sync_status: string;
    error_message: string | null;
    created_at: string;
    user?: { name: string } | null;
}

interface RespondenRow {
    nim: string;
    nama_inisial: string;
    usia: number;
    gender: string;
    agama: string;
    prodi: string;
    fakultas: string;
    pendidikan_terakhir: string;
    skor_pretest: number | string;
    skor_posttest: number | string;
    n_gain: number | string;
    status: string;
}

interface AdminSheetsProps {
    syncLogs: SyncLogRow[];
    totalSyncCount: number;
    successSyncCount: number;
    respondenData: RespondenRow[];
}

export default function Index({
    syncLogs,
    totalSyncCount,
    successSyncCount,
    respondenData,
}: AdminSheetsProps) {
    return (
        <AppLayout
            title="Lembar Tabulasi Responden & Log Google Sheets"
            actions={
                <a href="/admin/evaluations/export" className={buttonVariants({ variant: 'default', size: 'sm' })}>
                    <DownloadIcon />
                    Unduh Tabulasi Excel
                </a>
            }
        >
            <Head title="Lembar Data Responden" />

            <div className="flex flex-col gap-6">
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Total Responden Mahasiswa</p>
                        <p className="mt-1 text-2xl font-bold">{respondenData.length}</p>
                    </Card>
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Total Aktivitas Sinkronisasi</p>
                        <p className="mt-1 text-2xl font-bold">{totalSyncCount}</p>
                    </Card>
                    <Card size="sm">
                        <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Sinkronisasi Berhasil</p>
                        <p className="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300">{successSyncCount}</p>
                    </Card>
                </div>

                <section className="flex flex-col gap-3">
                    <h2 className="text-base font-bold text-slate-900 dark:text-white">
                        Rekapitulasi Skor Responden (Pre-Test Rata-rata vs Post-Test Akhir)
                    </h2>
                    <TableWrapper>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>NIM</TableHead>
                                    <TableHead>Inisial</TableHead>
                                    <TableHead>L/P</TableHead>
                                    <TableHead>Usia</TableHead>
                                    <TableHead>Prodi &amp; Fakultas</TableHead>
                                    <TableHead>Pre-Test</TableHead>
                                    <TableHead>Post-Test</TableHead>
                                    <TableHead>N-Gain</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {respondenData.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={9} className="py-8 text-center text-slate-600 dark:text-slate-300">
                                            Belum ada data responden mahasiswa.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    respondenData.map((r, idx) => (
                                        <TableRow key={r.nim + idx}>
                                            <TableCell className="font-mono text-xs">{r.nim}</TableCell>
                                            <TableCell className="font-semibold">{r.nama_inisial}</TableCell>
                                            <TableCell>{r.gender}</TableCell>
                                            <TableCell>{r.usia}</TableCell>
                                            <TableCell className="text-xs">
                                                <div>{r.prodi}</div>
                                                <div className="text-slate-500">{r.fakultas}</div>
                                            </TableCell>
                                            <TableCell className="font-semibold">{r.skor_pretest}</TableCell>
                                            <TableCell className="font-semibold">{r.skor_posttest}</TableCell>
                                            <TableCell className="font-bold text-teal-700 dark:text-teal-300">{r.n_gain}</TableCell>
                                            <TableCell>
                                                <Badge
                                                    color={r.status === 'Lengkap' ? 'success' : r.status === 'Pre-Test Selesai' ? 'info' : 'gray'}
                                                    size="sm"
                                                >
                                                    {r.status}
                                                </Badge>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </TableWrapper>
                </section>

                {syncLogs.length > 0 && (
                    <section className="flex flex-col gap-3">
                        <h2 className="text-base font-bold text-slate-900 dark:text-white">Riwayat Sinkronisasi Terakhir</h2>
                        <TableWrapper>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Waktu</TableHead>
                                        <TableHead>Pengguna</TableHead>
                                        <TableHead>Tab</TableHead>
                                        <TableHead>Status</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {syncLogs.map((log) => (
                                        <TableRow key={log.id}>
                                            <TableCell className="text-xs">{formatDateId(log.created_at, true)}</TableCell>
                                            <TableCell>{log.user?.name || '-'}</TableCell>
                                            <TableCell>{log.sheet_tab || 'Utama'}</TableCell>
                                            <TableCell>
                                                <Badge color={log.sync_status === 'success' ? 'success' : 'warning'} size="sm">
                                                    {log.sync_status}
                                                </Badge>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </TableWrapper>
                    </section>
                )}
            </div>
        </AppLayout>
    );
}
