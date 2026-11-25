import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FieldError } from '@/components/ui/field-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout';

interface PeriodHistoryRow {
    id: number;
    start_date: string;
    start_date_label: string;
    end_date_label: string | null;
    cycle_length: number;
    period_duration: number;
    flow_level: string;
    flow_color: string;
    volume_category: string | null;
    pbac_score: number | null;
    nrs_pain_score: number;
    walidd_total_score: number | null;
    walidd_category: string | null;
    notes: string | null;
}

interface BmiHistoryRow {
    id: number;
    weight_kg: number;
    height_cm: number;
    bmi_value: number;
    category: string;
    advice: string;
    created_at_label: string | null;
}

interface PredictionPayload {
    summary_narrative?: string;
    countdown_text?: string;
    current_status?: {
        cycle_day?: number;
        phase_name?: string;
        phase_description?: string;
    };
    predictions?: {
        next_period?: { start_date?: string; narrative?: string };
        ovulation?: { date?: string; narrative?: string };
        fertile_window?: { start_date?: string; end_date?: string; narrative?: string };
    };
}

interface SelfCareIndexProps {
    isFemale: boolean;
    recentPeriods: PeriodHistoryRow[];
    recentBmi: BmiHistoryRow[];
    prediction: PredictionPayload | null;
}

const WALIDD_LOCATIONS = [
    { id: 'perut_bawah', label: 'Perut Bawah' },
    { id: 'pinggang', label: 'Pinggang / Punggung Bawah' },
    { id: 'paha_dalam', label: 'Paha Bagian Dalam' },
] as const;

export default function Index({ isFemale, recentPeriods, recentBmi, prediction }: SelfCareIndexProps) {
    const today = new Date().toISOString().slice(0, 10);

    const periodForm = useForm({
        menarche_age: '',
        start_date: today,
        end_date: '',
        cycle_length: '28',
        period_duration: '5',
        is_regular: true,
        flow_level: 'sedang',
        flow_color: 'Merah Terang',
        blood_consistency: 'Cair Normal',
        pbac_pads_light: '0',
        pbac_pads_medium: '0',
        pbac_pads_heavy: '0',
        pbac_clots_small: '0',
        pbac_clots_large: '0',
        nrs_pain_score: '2',
        has_dysmenorrhea: true,
        walidd_working_ability: '0',
        walidd_locations: [] as string[],
        walidd_pain_days: '1',
        notes: '',
    });

    const bmiForm = useForm({
        weight_kg: '',
        height_cm: '',
    });

    const pbacPreview =
        (parseInt(periodForm.data.pbac_pads_light, 10) || 0) * 1 +
        (parseInt(periodForm.data.pbac_pads_medium, 10) || 0) * 5 +
        (parseInt(periodForm.data.pbac_pads_heavy, 10) || 0) * 20 +
        (parseInt(periodForm.data.pbac_clots_small, 10) || 0) * 1 +
        (parseInt(periodForm.data.pbac_clots_large, 10) || 0) * 5;

    const nrsVal = parseInt(periodForm.data.nrs_pain_score, 10) || 0;
    const intensityScore = nrsVal === 0 ? 0 : nrsVal <= 3 ? 1 : nrsVal <= 7 ? 2 : 3;
    const painDaysVal = parseInt(periodForm.data.walidd_pain_days, 10) || 0;
    const daysScore = painDaysVal === 0 ? 0 : painDaysVal <= 2 ? 1 : painDaysVal <= 4 ? 2 : 3;
    const waliddPreview =
        (parseInt(periodForm.data.walidd_working_ability, 10) || 0) +
        periodForm.data.walidd_locations.length +
        intensityScore +
        daysScore;

    const weightNum = parseFloat(bmiForm.data.weight_kg);
    const heightNum = parseFloat(bmiForm.data.height_cm);
    const liveBmi =
        weightNum > 0 && heightNum >= 80
            ? Math.round((weightNum / ((heightNum / 100) * (heightNum / 100))) * 10) / 10
            : null;

    const toggleLocation = (loc: string) => {
        const exists = periodForm.data.walidd_locations.includes(loc);
        periodForm.setData(
            'walidd_locations',
            exists
                ? periodForm.data.walidd_locations.filter((item) => item !== loc)
                : [...periodForm.data.walidd_locations, loc],
        );
    };

    const submitPeriod = (event: FormEvent) => {
        event.preventDefault();
        periodForm.post('/selfcare/period', { preserveScroll: true });
    };

    const submitBmi = (event: FormEvent) => {
        event.preventDefault();
        bmiForm.post('/selfcare/bmi', {
            preserveScroll: true,
            onSuccess: () => bmiForm.reset(),
        });
    };

    return (
        <AppLayout
            title={isFemale ? 'Kalender Siklus Haid, WaLIDD, & IMT' : 'Kalkulator IMT & Panduan Higienitas'}
            actions={
                <div className="flex flex-wrap gap-2">
                    <Link href="/selfcare/blood-guide" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                        Panduan Warna Darah
                    </Link>
                    <Link href="/selfcare/hygiene-guide" className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                        Panduan Higienitas
                    </Link>
                </div>
            }
        >
            <Head title="Instrumen Self-Care" />

            <div className="flex flex-col gap-6">
                {isFemale && prediction && (
                    <Card className="border-teal-200 bg-teal-50/60 dark:border-teal-800 dark:bg-teal-950/35">
                        <CardHeader>
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <Badge color="primary">Perkiraan Siklus Berikutnya</Badge>
                                {prediction.countdown_text && <Badge color="info">{prediction.countdown_text}</Badge>}
                            </div>
                            <CardTitle className="mt-1 text-lg">
                                {prediction.current_status?.phase_name || 'Pemantauan Siklus Aktif'}
                            </CardTitle>
                            {prediction.summary_narrative && (
                                <CardDescription className="text-slate-700 dark:text-slate-200">
                                    {prediction.summary_narrative}
                                </CardDescription>
                            )}
                        </CardHeader>
                        <CardContent className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div className="rounded-lg border border-slate-200 bg-white p-3.5 dark:border-slate-700 dark:bg-slate-900">
                                <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Estimasi Haid Berikutnya</p>
                                <p className="mt-1 text-sm font-bold text-slate-900 dark:text-white">
                                    {prediction.predictions?.next_period?.start_date || '-'}
                                </p>
                            </div>
                            <div className="rounded-lg border border-slate-200 bg-white p-3.5 dark:border-slate-700 dark:bg-slate-900">
                                <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Estimasi Hari Ovulasi</p>
                                <p className="mt-1 text-sm font-bold text-slate-900 dark:text-white">
                                    {prediction.predictions?.ovulation?.date || '-'}
                                </p>
                            </div>
                            <div className="rounded-lg border border-slate-200 bg-white p-3.5 dark:border-slate-700 dark:bg-slate-900">
                                <p className="text-xs font-semibold text-slate-600 dark:text-slate-300">Jendela Masa Subur (6 Hari)</p>
                                <p className="mt-1 text-sm font-bold text-slate-900 dark:text-white">
                                    {prediction.predictions?.fertile_window?.start_date
                                        ? `${prediction.predictions.fertile_window.start_date} s.d. ${prediction.predictions.fertile_window.end_date}`
                                        : '-'}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                )}

                <div className="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
                    {isFemale && (
                        <Card className="lg:col-span-7">
                            <CardHeader>
                                <CardTitle>Catat Siklus Haid, PBAC, &amp; Skor Dismenore WaLIDD</CardTitle>
                                <CardDescription>
                                    Instrumen 5 komponen klinis: waktu siklus, volume FIGO/PBAC, warna aliran, serta evaluasi kram haid WaLIDD.
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form onSubmit={submitPeriod} className="flex flex-col gap-5">
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div className="flex flex-col gap-1.5">
                                            <Label htmlFor="sc-start">Tanggal Mulai Haid *</Label>
                                            <Input
                                                id="sc-start"
                                                name="start_date"
                                                type="date"
                                                required
                                                value={periodForm.data.start_date}
                                                onChange={(e) => periodForm.setData('start_date', e.target.value)}
                                            />
                                            <FieldError message={periodForm.errors.start_date} />
                                        </div>
                                        <div className="flex flex-col gap-1.5">
                                            <Label htmlFor="sc-end">Tanggal Selesai Haid (Opsional)</Label>
                                            <Input
                                                id="sc-end"
                                                name="end_date"
                                                type="date"
                                                value={periodForm.data.end_date}
                                                onChange={(e) => periodForm.setData('end_date', e.target.value)}
                                            />
                                            <FieldError message={periodForm.errors.end_date} />
                                        </div>
                                        <div className="flex flex-col gap-1.5">
                                            <Label htmlFor="sc-cycle">Panjang Siklus (Hari) *</Label>
                                            <Input
                                                id="sc-cycle"
                                                name="cycle_length"
                                                type="number"
                                                min={15}
                                                max={60}
                                                required
                                                value={periodForm.data.cycle_length}
                                                onChange={(e) => periodForm.setData('cycle_length', e.target.value)}
                                            />
                                            <FieldError message={periodForm.errors.cycle_length} />
                                        </div>
                                        <div className="flex flex-col gap-1.5">
                                            <Label htmlFor="sc-duration">Durasi Haid (Hari)</Label>
                                            <Input
                                                id="sc-duration"
                                                name="period_duration"
                                                type="number"
                                                min={1}
                                                max={14}
                                                value={periodForm.data.period_duration}
                                                onChange={(e) => periodForm.setData('period_duration', e.target.value)}
                                            />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div className="flex flex-col gap-1.5">
                                            <Label htmlFor="sc-flow">Tingkat Aliran *</Label>
                                            <Select
                                                id="sc-flow"
                                                name="flow_level"
                                                value={periodForm.data.flow_level}
                                                onChange={(e) => periodForm.setData('flow_level', e.target.value)}
                                            >
                                                <option value="ringan">Ringan</option>
                                                <option value="sedang">Sedang (Normal)</option>
                                                <option value="deras">Deras</option>
                                                <option value="sangat_deras">Sangat Deras</option>
                                            </Select>
                                        </div>

                                        <div className="flex flex-col gap-1.5">
                                            <Label htmlFor="sc-color">Warna Dominan Darah *</Label>
                                            <Select
                                                id="sc-color"
                                                name="flow_color"
                                                value={periodForm.data.flow_color}
                                                onChange={(e) => periodForm.setData('flow_color', e.target.value)}
                                            >
                                                <option value="Merah Terang">Merah Terang (Fisiologis)</option>
                                                <option value="Cokelat Gelap">Cokelat Gelap (Oksidasi)</option>
                                                <option value="Merah Muda Pucat">Merah Muda Pucat</option>
                                                <option value="Hitam Pekat">Hitam Pekat / Gumpalan Besar</option>
                                            </Select>
                                        </div>
                                    </div>

                                    <fieldset className="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                                        <legend className="px-2 text-xs font-bold text-slate-800 dark:text-slate-100">
                                            Skor Visual PBAC (Pratinjau: {pbacPreview} poin {pbacPreview >= 100 ? '- Risiko Menoragia' : '- Rentang Normal'})
                                        </legend>
                                        <div className="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-5">
                                            <div className="flex flex-col gap-1">
                                                <Label htmlFor="pbac-l">Pembalut Ringan</Label>
                                                <Input
                                                    id="pbac-l"
                                                    type="number"
                                                    min={0}
                                                    value={periodForm.data.pbac_pads_light}
                                                    onChange={(e) => periodForm.setData('pbac_pads_light', e.target.value)}
                                                />
                                            </div>
                                            <div className="flex flex-col gap-1">
                                                <Label htmlFor="pbac-m">Pembalut Sedang</Label>
                                                <Input
                                                    id="pbac-m"
                                                    type="number"
                                                    min={0}
                                                    value={periodForm.data.pbac_pads_medium}
                                                    onChange={(e) => periodForm.setData('pbac_pads_medium', e.target.value)}
                                                />
                                            </div>
                                            <div className="flex flex-col gap-1">
                                                <Label htmlFor="pbac-h">Pembalut Penuh</Label>
                                                <Input
                                                    id="pbac-h"
                                                    type="number"
                                                    min={0}
                                                    value={periodForm.data.pbac_pads_heavy}
                                                    onChange={(e) => periodForm.setData('pbac_pads_heavy', e.target.value)}
                                                />
                                            </div>
                                            <div className="flex flex-col gap-1">
                                                <Label htmlFor="pbac-cs">Gumpalan Kecil</Label>
                                                <Input
                                                    id="pbac-cs"
                                                    type="number"
                                                    min={0}
                                                    value={periodForm.data.pbac_clots_small}
                                                    onChange={(e) => periodForm.setData('pbac_clots_small', e.target.value)}
                                                />
                                            </div>
                                            <div className="flex flex-col gap-1">
                                                <Label htmlFor="pbac-cl">Gumpalan Besar</Label>
                                                <Input
                                                    id="pbac-cl"
                                                    type="number"
                                                    min={0}
                                                    value={periodForm.data.pbac_clots_large}
                                                    onChange={(e) => periodForm.setData('pbac_clots_large', e.target.value)}
                                                />
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset className="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                                        <legend className="px-2 text-xs font-bold text-slate-800 dark:text-slate-100">
                                            Evaluasi Nyeri Dismenore NRS &amp; WaLIDD (Pratinjau: {waliddPreview}/12)
                                        </legend>
                                        <div className="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            <div className="flex flex-col gap-1.5">
                                                <Label htmlFor="sc-nrs">Intensitas Nyeri NRS (0 sampai 10) *</Label>
                                                <Input
                                                    id="sc-nrs"
                                                    name="nrs_pain_score"
                                                    type="number"
                                                    min={0}
                                                    max={10}
                                                    required
                                                    value={periodForm.data.nrs_pain_score}
                                                    onChange={(e) => periodForm.setData('nrs_pain_score', e.target.value)}
                                                />
                                            </div>
                                            <div className="flex flex-col gap-1.5">
                                                <Label htmlFor="sc-work">Kemampuan Aktivitas Kuliah</Label>
                                                <Select
                                                    id="sc-work"
                                                    value={periodForm.data.walidd_working_ability}
                                                    onChange={(e) => periodForm.setData('walidd_working_ability', e.target.value)}
                                                >
                                                    <option value="0">0 - Tidak terganggu</option>
                                                    <option value="1">1 - Sedikit terganggu</option>
                                                    <option value="2">2 - Sering terganggu</option>
                                                    <option value="3">3 - Tidak mampu beraktivitas</option>
                                                </Select>
                                            </div>
                                            <div className="flex flex-col gap-1.5">
                                                <Label htmlFor="sc-paindays">Durasi Hari Nyeri</Label>
                                                <Input
                                                    id="sc-paindays"
                                                    type="number"
                                                    min={0}
                                                    max={14}
                                                    value={periodForm.data.walidd_pain_days}
                                                    onChange={(e) => periodForm.setData('walidd_pain_days', e.target.value)}
                                                />
                                            </div>
                                        </div>

                                        <div className="mt-3">
                                            <p className="mb-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200">Lokasi Nyeri Kram:</p>
                                            <div className="flex flex-wrap gap-3">
                                                {WALIDD_LOCATIONS.map((loc) => (
                                                    <label key={loc.id} className="inline-flex min-h-11 items-center gap-2 text-xs font-medium text-slate-800 dark:text-slate-100">
                                                        <input
                                                            type="checkbox"
                                                            checked={periodForm.data.walidd_locations.includes(loc.id)}
                                                            onChange={() => toggleLocation(loc.id)}
                                                            className="size-4 rounded border-slate-300 text-teal-700"
                                                        />
                                                        <span>{loc.label}</span>
                                                    </label>
                                                ))}
                                            </div>
                                        </div>
                                    </fieldset>

                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="sc-notes">Catatan Keluhan Tambahan</Label>
                                        <Textarea
                                            id="sc-notes"
                                            name="notes"
                                            rows={2}
                                            value={periodForm.data.notes}
                                            onChange={(e) => periodForm.setData('notes', e.target.value)}
                                            placeholder="Catat gejala penyerta atau obat pereda yang dikonsumsi..."
                                        />
                                    </div>

                                    <div className="flex justify-end">
                                        <Button type="submit" disabled={periodForm.processing}>
                                            {periodForm.processing ? 'Menyimpan catatan...' : 'Simpan Catatan Siklus (+10 Poin)'}
                                        </Button>
                                    </div>
                                </form>

                                {recentPeriods.length > 0 && (
                                    <div className="mt-6 border-t border-slate-200 pt-5 dark:border-slate-700">
                                        <h3 className="mb-3 text-sm font-bold text-slate-900 dark:text-white">Riwayat Siklus Terakhir</h3>
                                        <div className="flex flex-col gap-2.5">
                                            {recentPeriods.map((row) => (
                                                <div
                                                    key={row.id}
                                                    className="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200 p-3 text-xs dark:border-slate-700"
                                                >
                                                    <div>
                                                        <span className="font-bold text-slate-900 dark:text-white">{row.start_date_label}</span>
                                                        <span className="ml-2 text-slate-600 dark:text-slate-300">
                                                            Siklus {row.cycle_length} hr &middot; {row.flow_color}
                                                        </span>
                                                    </div>
                                                    <div className="flex flex-wrap gap-1.5">
                                                        {row.pbac_score !== null && (
                                                            <Badge color={row.pbac_score >= 100 ? 'error' : 'info'} size="sm">
                                                                PBAC {row.pbac_score}
                                                            </Badge>
                                                        )}
                                                        {row.walidd_category && (
                                                            <Badge color="warning" size="sm">
                                                                {row.walidd_category} ({row.walidd_total_score}/12)
                                                            </Badge>
                                                        )}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    )}

                    <Card className={isFemale ? 'lg:col-span-5' : 'lg:col-span-12'}>
                        <CardHeader>
                            <CardTitle>Kalkulator Indeks Massa Tubuh (IMT)</CardTitle>
                            <CardDescription>
                                Klasifikasi status gizi dewasa Asia (Kemenkes RI &amp; WHO) terkait keseimbangan hormon reproduksi.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="flex flex-col gap-5">
                            <form onSubmit={submitBmi} className="flex flex-col gap-4">
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="bmi-weight">Berat Badan (kg) *</Label>
                                        <Input
                                            id="bmi-weight"
                                            name="weight_kg"
                                            type="number"
                                            step="0.1"
                                            min={20}
                                            max={250}
                                            required
                                            value={bmiForm.data.weight_kg}
                                            onChange={(e) => bmiForm.setData('weight_kg', e.target.value)}
                                            placeholder="54"
                                        />
                                        <FieldError message={bmiForm.errors.weight_kg} />
                                    </div>
                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="bmi-height">Tinggi Badan (cm) *</Label>
                                        <Input
                                            id="bmi-height"
                                            name="height_cm"
                                            type="number"
                                            step="0.1"
                                            min={80}
                                            max={250}
                                            required
                                            value={bmiForm.data.height_cm}
                                            onChange={(e) => bmiForm.setData('height_cm', e.target.value)}
                                            placeholder="160"
                                        />
                                        <FieldError message={bmiForm.errors.height_cm} />
                                    </div>
                                </div>

                                {liveBmi !== null && (
                                    <div className="rounded-lg border border-teal-200 bg-teal-50 p-3 text-xs font-semibold text-teal-900 dark:border-teal-800 dark:bg-teal-950/60 dark:text-teal-200">
                                        Pratinjau IMT: {liveBmi} kg/m&sup2;
                                    </div>
                                )}

                                <Button type="submit" disabled={bmiForm.processing}>
                                    {bmiForm.processing ? 'Menghitung...' : 'Hitung & Simpan IMT'}
                                </Button>
                            </form>

                            {recentBmi.length > 0 && (
                                <div className="border-t border-slate-200 pt-4 dark:border-slate-700">
                                    <h3 className="mb-2.5 text-sm font-bold text-slate-900 dark:text-white">Riwayat IMT Terakhir</h3>
                                    <div className="flex flex-col gap-2.5">
                                        {recentBmi.map((row) => (
                                            <div key={row.id} className="rounded-lg border border-slate-200 p-3 text-xs dark:border-slate-700">
                                                <div className="flex items-center justify-between gap-2">
                                                    <span className="font-bold text-slate-900 dark:text-white">
                                                        {row.bmi_value} kg/m&sup2; ({row.weight_kg} kg / {row.height_cm} cm)
                                                    </span>
                                                    <Badge color="primary" size="sm">{row.category}</Badge>
                                                </div>
                                                <p className="mt-1.5 leading-relaxed text-slate-600 dark:text-slate-300">{row.advice}</p>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
