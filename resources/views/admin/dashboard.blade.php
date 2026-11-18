@extends('layouts.admin')

@section('title', 'Dashboard Grafik & Metrik Riset')
@section('page-title', 'Dashboard Grafik & Evaluasi Riset SIKERA')

@section('content')
@php
    $adminScoreDelta = round($avgPostScore - $avgPreScore, 1);
@endphp

<div class="flex flex-col gap-6">
    {{-- Header Pusat Analisis & Tindakan Cepat Pengelola Riset --}}
    <section aria-labelledby="admin-dashboard-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex h-1.5 w-full" aria-hidden="true">
            <span class="w-20 bg-rose-600 dark:bg-rose-500"></span>
            <span class="grow bg-teal-700 dark:bg-teal-500"></span>
        </div>

        <div class="flex flex-col justify-between gap-4 p-6 sm:flex-row sm:items-center sm:p-7">
            <div>
                <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">
                    Dashboard Grafik &middot; Pusat Analisis Penelitian SIKERA
                </p>
                <h2 id="admin-dashboard-heading" class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Evaluasi Efektivitas Intervensi Edukasi
                </h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300 sm:text-sm">
                    Pemantauan skor kognitif Pre-Test, Post-Test, dan indeks N-Gain responden mahasiswa Universitas Bengkulu.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('admin.evaluations.export') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                >
                    Unduh Rekap Excel / SPSS
                </a>
                <a
                    href="{{ route('admin.evaluations.index') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-800 transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700"
                >
                    Lembar Jawaban Evaluasi
                </a>
            </div>
        </div>
    </section>

    {{-- 4 Panel Metrik Hierarkis (1 Kartu Fokus N-Gain + 3 Metrik Pendukung) --}}
    <section aria-label="Metrik Utama Penelitian" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-teal-900 p-5 text-white shadow-sm dark:border dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold text-amber-300">Indeks N-Gain Rata-rata</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-white">{{ number_format((float) $avgNGain, 3) }}</p>
            <p class="mt-2 text-xs font-medium text-teal-50 dark:text-slate-300">
                Kategori efektivitas: <strong class="font-semibold text-white">{{ $nGainCategory }}</strong>
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Total Responden Mahasiswa</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $totalMahasiswa }}</p>
            <p class="mt-2 text-xs font-semibold text-teal-700 dark:text-teal-300">
                {{ $participationRate }}% partisipasi Pre-Test aktif
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Ketuntasan Pre-Test</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $pretestDone }}</p>
            <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                Rata-rata skor awal: <strong class="font-semibold text-slate-900 dark:text-white">{{ $avgPreScore }}/100</strong>
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Ketuntasan Post-Test</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $posttestDone }}</p>
            <p class="mt-2 text-xs font-semibold text-teal-700 dark:text-teal-300">
                Rata-rata {{ $avgPostScore }}/100 ({{ $adminScoreDelta >= 0 ? '+' . $adminScoreDelta : $adminScoreDelta }} poin)
            </p>
        </div>
    </section>

    {{-- Grid Utama Visualisasi & Rekapitulasi --}}
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
        {{-- Kolom Kiri (8 kolom): Grafik Perbandingan Modul & Pengiriman Evaluasi Terbaru --}}
        <div class="flex flex-col gap-6 lg:col-span-8">
            <section aria-labelledby="chart-prepost-heading" class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                    <div>
                        <h3 id="chart-prepost-heading" class="text-base font-semibold text-slate-900 dark:text-white">
                            Grafik Peningkatan Pemahaman per Modul Pembelajaran
                        </h3>
                        <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                            Perbandingan rata-rata skor Pre-Test (sebelum materi) dan Post-Test (sesudah materi) pada 4 modul inti.
                        </p>
                    </div>
                </div>

                <div class="p-6">
                    @if($pretestDone === 0 && $posttestDone === 0)
                    <div class="mb-4 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                        Belum ada jawaban evaluasi yang masuk. Grafik di bawah akan otomatis terisi setelah mahasiswa mengerjakan Pre-Test atau Post-Test.
                    </div>
                    @endif
                    <div class="min-h-[320px]" id="chartPrePost" role="img" aria-label="Grafik batang perbandingan skor Pre-Test dan Post-Test pada empat modul"></div>
                </div>
            </section>

            {{-- Tabel Pengiriman Evaluasi Terbaru --}}
            <section aria-labelledby="recent-eval-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                    <div>
                        <h3 id="recent-eval-heading" class="text-base font-semibold text-slate-900 dark:text-white">
                            Pengiriman Evaluasi Terbaru
                        </h3>
                        <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                            10 rekaman kuesioner Pre-Test dan Post-Test terakhir yang dikirimkan responden.
                        </p>
                    </div>
                    <a
                        href="{{ route('admin.sheets.index') }}"
                        class="inline-flex min-h-11 items-center rounded-lg px-2 text-xs font-semibold text-teal-700 underline decoration-teal-700/40 underline-offset-4 hover:text-teal-800 focus-visible:outline-2 focus-visible:outline-teal-700 dark:text-teal-300"
                    >
                        Buka Lembar Data
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[520px] text-left text-xs">
                        <thead class="bg-slate-50 font-semibold text-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <tr>
                                <th scope="col" class="px-5 py-3.5">Responden</th>
                                <th scope="col" class="px-4 py-3.5">Jenis Evaluasi</th>
                                <th scope="col" class="px-4 py-3.5 text-right">Skor</th>
                                <th scope="col" class="px-4 py-3.5 text-right">N-Gain</th>
                                <th scope="col" class="px-5 py-3.5 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-800 dark:divide-slate-700 dark:text-slate-200">
                            @forelse($recentEvaluations as $eval)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-900/40">
                                <td class="px-5 py-3 font-medium text-slate-900 dark:text-white">
                                    {{ $eval->user->name ?? 'Responden' }}
                                    <span class="block text-[11px] font-normal text-slate-600 dark:text-slate-300">{{ $eval->user->fakultas ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-md px-2 py-0.5 text-[11px] font-semibold {{ $eval->type === 'post_test' ? 'bg-teal-50 text-teal-800 dark:bg-teal-950 dark:text-teal-200' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-100' }}">
                                        {{ $eval->type === 'post_test' ? 'Post-Test' : 'Pre-Test' }}
                                        {{ $eval->module_number ? '· Modul ' . $eval->module_number : '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ $eval->total_score }}/100</td>
                                <td class="px-4 py-3 text-right font-medium text-slate-700 dark:text-slate-200">
                                    {{ $eval->n_gain_score !== null ? number_format((float) $eval->n_gain_score, 3) : '-' }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a
                                        href="{{ route('admin.evaluations.show', $eval->id) }}"
                                        class="inline-flex min-h-11 items-center rounded-lg px-2.5 font-semibold text-teal-700 underline decoration-teal-700/40 underline-offset-4 hover:text-teal-800 focus-visible:outline-2 focus-visible:outline-teal-700 dark:text-teal-300"
                                    >
                                        Rincian
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-600 dark:text-slate-300">
                                    Belum ada rekaman jawaban evaluasi dari responden.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Kolom Kanan (4 kolom): Gauge Efektivitas N-Gain & Distribusi Fakultas --}}
        <div class="flex flex-col gap-6 lg:col-span-4">
            <section aria-labelledby="ngain-gauge-heading" class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="px-6 pt-5 pb-2">
                    <h3 id="ngain-gauge-heading" class="text-base font-semibold text-slate-900 dark:text-white">
                        Tingkat Efektivitas (N-Gain)
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                        Rasio peningkatan skor aktual terhadap peningkatan maksimal ideal.
                    </p>
                </div>

                <div class="flex justify-center px-4">
                    <div class="max-h-[200px] w-full" id="chartNGain" role="img" aria-label="Indikator radial nilai rata-rata N-Gain"></div>
                </div>

                <div class="flex justify-center pb-4">
                    <span class="inline-flex rounded-md px-3 py-1 text-xs font-bold {{ $nGainCategory === 'Tinggi' ? 'bg-teal-50 text-teal-800 dark:bg-teal-950 dark:text-teal-200' : ($nGainCategory === 'Sedang' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950 dark:text-amber-200' : 'bg-rose-50 text-rose-900 dark:bg-rose-950 dark:text-rose-200') }}">
                        Kategori: {{ $nGainCategory }}
                    </span>
                </div>

                <dl class="grid grid-cols-2 divide-x divide-slate-200 border-t border-slate-200 py-3.5 text-center text-xs dark:divide-slate-700 dark:border-slate-700">
                    <div>
                        <dt class="font-medium text-slate-600 dark:text-slate-300">Rata-rata Pre</dt>
                        <dd class="mt-0.5 text-base font-bold text-slate-900 dark:text-white">{{ $avgPreScore }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-600 dark:text-slate-300">Rata-rata Post</dt>
                        <dd class="mt-0.5 text-base font-bold text-teal-700 dark:text-teal-300">{{ $avgPostScore }}</dd>
                    </div>
                </dl>
            </section>

            {{-- Distribusi Responden Aktif per Fakultas --}}
            <section aria-labelledby="fakultas-dist-heading" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center justify-between gap-2 border-b border-slate-200 pb-3.5 dark:border-slate-700">
                    <h3 id="fakultas-dist-heading" class="text-sm font-semibold text-slate-900 dark:text-white">
                        Distribusi Responden per Fakultas
                    </h3>
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex min-h-11 items-center rounded-lg px-2 text-xs font-semibold text-teal-700 underline decoration-teal-700/40 underline-offset-4 hover:text-teal-800 focus-visible:outline-2 focus-visible:outline-teal-700 dark:text-teal-300"
                    >
                        Kelola Akun
                    </a>
                </div>

                <div class="mt-3 flex flex-col divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($fakultasStats as $fs)
                    <div class="flex items-center justify-between gap-3 py-2.5 text-xs">
                        <span class="truncate font-medium text-slate-700 dark:text-slate-200">{{ $fs->fakultas ?: 'Lainnya' }}</span>
                        <span class="shrink-0 font-bold text-teal-700 dark:text-teal-300">{{ $fs->total }} responden</span>
                    </div>
                    @empty
                    <p class="py-6 text-center text-xs text-slate-600 dark:text-slate-300">
                        Belum ada responden yang menyelesaikan Pre-Test.
                    </p>
                    @endforelse
                </div>

                <dl class="mt-4 grid grid-cols-2 gap-2.5 border-t border-slate-200 pt-4 text-xs dark:border-slate-700">
                    <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-900/60">
                        <dt class="font-medium text-slate-600 dark:text-slate-300">Diskusi Forum</dt>
                        <dd class="mt-1 font-bold text-slate-900 dark:text-white">{{ $totalThreads }} topik ({{ $unansweredThreads }} menunggu)</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-900/60">
                        <dt class="font-medium text-slate-600 dark:text-slate-300">Catatan Siklus Haid</dt>
                        <dd class="mt-1 font-bold text-slate-900 dark:text-white">{{ $totalPeriodLogs }} entri</dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDarkInitial = JSON.parse(localStorage.getItem('darkMode')) === true;

    const getGaugeColors = (isDark) => ({
        track: isDark ? '#334155' : '#e2e8f0',
        nameColor: isDark ? '#cbd5e1' : '#475569',
        valueColor: isDark ? '#ffffff' : '#0f172a',
        barColors: isDark ? ['#94a3b8', '#14b8a6'] : ['#64748b', '#0f766e'],
    });

    const initialPalette = getGaugeColors(isDarkInitial);

    const prePostOptions = {
        series: [{
            name: 'Pre-Test (Sebelum)',
            data: {!! json_encode(array_column($scoresByModule, 'pre')) !!}
        }, {
            name: 'Post-Test (Sesudah)',
            data: {!! json_encode(array_column($scoresByModule, 'post')) !!}
        }],
        chart: {
            type: 'bar',
            height: 320,
            fontFamily: 'Plus Jakarta Sans, ui-sans-serif, system-ui, sans-serif',
            toolbar: { show: false },
            background: 'transparent',
        },
        colors: initialPalette.barColors,
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '46%',
                borderRadius: 6,
                borderRadiusApplication: 'end',
            },
        },
        dataLabels: { enabled: false },
        stroke: {
            show: true,
            width: 3,
            colors: ['transparent']
        },
        xaxis: {
            categories: {!! json_encode(array_column($scoresByModule, 'modul')) !!},
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            max: 100,
            labels: {
                formatter: function (val) { return val.toFixed(0); }
            }
        },
        fill: { opacity: 1 },
        legend: {
            position: 'top',
            horizontalAlign: 'left',
            fontWeight: 600,
            fontSize: '12px'
        },
        grid: {
            yaxis: { lines: { show: true } },
        },
        tooltip: {
            y: { formatter: function (val) { return val + ' / 100'; } }
        }
    };

    const chartPrePost = new ApexCharts(document.querySelector('#chartPrePost'), prePostOptions);
    chartPrePost.render();

    const nGainValue = {{ (float) $avgNGain }};
    const nGainPercent = Math.max(0, Math.min(100, Math.round(nGainValue * 100)));
    const gaugeTone = nGainValue >= 0.7 ? '#0f766e' : (nGainValue >= 0.3 ? '#d97706' : '#e11d48');

    const nGainOptions = {
        series: [nGainPercent],
        chart: {
            type: 'radialBar',
            height: 200,
            fontFamily: 'Plus Jakarta Sans, ui-sans-serif, system-ui, sans-serif',
            background: 'transparent',
        },
        plotOptions: {
            radialBar: {
                hollow: { size: '58%' },
                track: {
                    background: initialPalette.track,
                },
                dataLabels: {
                    name: {
                        show: true,
                        fontSize: '11px',
                        color: initialPalette.nameColor,
                        offsetY: -8,
                    },
                    value: {
                        show: true,
                        fontSize: '22px',
                        fontWeight: 700,
                        color: initialPalette.valueColor,
                        formatter: function () {
                            return nGainValue.toFixed(3);
                        }
                    }
                }
            }
        },
        colors: [gaugeTone],
        labels: ['N-Gain'],
        stroke: { lineCap: 'round' },
    };

    const chartNGain = new ApexCharts(document.querySelector('#chartNGain'), nGainOptions);
    chartNGain.render();

    window.addEventListener('sikera-theme-change', function (event) {
        const nextPalette = getGaugeColors(Boolean(event.detail && event.detail.dark));
        chartPrePost.updateOptions({
            colors: nextPalette.barColors
        });
        chartNGain.updateOptions({
            plotOptions: {
                radialBar: {
                    track: { background: nextPalette.track },
                    dataLabels: {
                        name: { color: nextPalette.nameColor },
                        value: { color: nextPalette.valueColor },
                    }
                }
            }
        });
    });
});
</script>
@endpush
