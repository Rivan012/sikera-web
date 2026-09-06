@extends('layouts.admin')

@section('title', 'Dashboard Grafik & Metrik Riset')
@section('page-title', 'Dashboard Grafik & Evaluasi Riset SIKERA')

@section('content')
<div class="space-y-6">
    {{-- Header Ringkasan Riset --}}
    <div class="rounded-2xl border border-brand-100 bg-gradient-to-r from-brand-50 to-white p-6 dark:border-brand-900/50 dark:from-brand-950/30 dark:to-gray-900 sm:p-7">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <span class="rounded-full bg-brand-500/15 px-2.5 py-0.5 text-xs font-semibold text-brand-700 dark:text-brand-300">Pusat Analisis &amp; Grafik Penelitian</span>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-1.5">Evaluasi Efektivitas Intervensi Edukasi</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pemantauan skor kognitif mahasiswa baru Universitas Bengkulu secara terukur (N-Gain).</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-success-700 shadow-theme-xs dark:bg-gray-800 dark:text-success-400 border border-gray-200 dark:border-gray-700">
                    <span class="h-2 w-2 rounded-full bg-success-500 animate-pulse"></span>
                    Live Data Riset
                </span>
            </div>
        </div>
    </div>

    {{-- 4 Kartu Metrik Utama --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Responden --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Responden</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalMahasiswa }}</h4>
            <div class="flex items-center gap-1.5 mt-2 text-[11px] text-success-600 dark:text-success-400 font-semibold">
                <span>{{ $participationRate }}% Partisipasi Aktif</span>
            </div>
        </div>

        {{-- Pre-Test Selesai --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pre-Test Selesai</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-brand-600 dark:text-brand-400 mt-2">{{ $pretestDone }}</h4>
            <p class="text-[11px] text-gray-400 mt-2">Rata-rata: {{ $avgPreScore }}/100</p>
        </div>

        {{-- Post-Test Selesai --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Post-Test Selesai</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-success-600 dark:text-success-400 mt-2">{{ $posttestDone }}</h4>
            <p class="text-[11px] text-success-600 dark:text-success-400 mt-2 font-semibold">+{{ round($avgPostScore - $avgPreScore, 1) }} kenaikan rata-rata</p>
        </div>

        {{-- N-Gain Score --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Indeks N-Gain</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-warning-600 dark:text-warning-400 mt-2">{{ $avgNGain }}</h4>
            <p class="text-[11px] font-semibold {{ $nGainCategory === 'Tinggi' ? 'text-success-600' : 'text-warning-600' }} mt-2">
                Kategori {{ $nGainCategory }}
            </p>
        </div>
    </div>

    {{-- BAGIAN GRAFIK & VISUALISASI UTAMA --}}
    <div class="grid grid-cols-12 gap-6">
        {{-- 1. Grafik Batang Perbandingan Pre-Test vs Post-Test per Modul --}}
        <div class="col-span-12 lg:col-span-8">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Grafik Peningkatan Pemahaman per Modul Pembelajaran</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbandingan rata-rata skor Pre-Test (sebelum) vs Post-Test (sesudah) pada 4 modul.</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="min-h-[320px]" id="chartPrePost"></div>
                </div>
            </div>
        </div>

        {{-- 2. Radial Gauge Efektivitas N-Gain --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-6 pt-5 pb-2">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Tingkat Efektivitas (N-Gain)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Indeks efektivitas pembelajaran SIKERA.</p>
                </div>
                <div class="flex justify-center px-4">
                    <div class="max-h-[190px] w-full" id="chartNGain"></div>
                </div>
                <div class="flex justify-center pb-3">
                    <span class="inline-flex rounded-full {{ $nGainCategory === 'Tinggi' ? 'bg-success-50 text-success-700' : 'bg-warning-50 text-warning-700' }} px-3 py-1 text-xs font-bold">
                        Kategori: {{ $nGainCategory }}
                    </span>
                </div>
                <div class="grid grid-cols-2 divide-x divide-gray-100 border-t border-gray-100 dark:divide-gray-800 dark:border-gray-800 py-3 text-center text-xs">
                    <div>
                        <span class="text-gray-400">Rata-rata Pre</span>
                        <p class="text-base font-bold text-gray-800 dark:text-white">{{ $avgPreScore }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400">Rata-rata Post</span>
                        <p class="text-base font-bold text-success-600 dark:text-success-400">{{ $avgPostScore }}</p>
                    </div>
                </div>
            </div>

            {{-- Rekapitulasi Partisipasi Fakultas --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Distribusi Responden per Fakultas</h4>
                <div class="space-y-2">
                    @foreach($fakultasStats as $fs)
                    <div class="flex items-center justify-between text-xs py-1 border-b border-gray-100 dark:border-gray-800 last:border-0">
                        <span class="font-medium text-gray-700 dark:text-gray-300 truncate max-w-[190px]">{{ $fs->fakultas }}</span>
                        <span class="font-bold text-brand-600 dark:text-brand-400">{{ $fs->total }} Maba</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Chart Pre-Test vs Post-Test per Modul (Bar Chart)
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
            fontFamily: 'Outfit, sans-serif',
            toolbar: { show: false },
        },
        colors: ['#465fff', '#12b76a'],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '45%',
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
                formatter: function(val) { return val.toFixed(0); }
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
            y: { formatter: function(val) { return val + " / 100"; } }
        }
    };

    const chartPrePost = new ApexCharts(document.querySelector("#chartPrePost"), prePostOptions);
    chartPrePost.render();

    // 2. Chart N-Gain Radial Gauge
    const nGainValue = {{ $avgNGain }};
    const nGainPercent = Math.round(nGainValue * 100);

    const nGainOptions = {
        series: [nGainPercent],
        chart: {
            type: 'radialBar',
            height: 190,
            fontFamily: 'Outfit, sans-serif',
        },
        plotOptions: {
            radialBar: {
                hollow: { size: '55%' },
                track: {
                    background: '#e4e7ec',
                },
                dataLabels: {
                    name: {
                        show: true,
                        fontSize: '11px',
                        color: '#667085',
                        offsetY: -8,
                    },
                    value: {
                        show: true,
                        fontSize: '22px',
                        fontWeight: 700,
                        color: '#101828',
                        formatter: function() {
                            return nGainValue.toFixed(3);
                        }
                    }
                }
            }
        },
        colors: [nGainValue >= 0.7 ? '#12b76a' : (nGainValue >= 0.3 ? '#f79009' : '#f04438')],
        labels: ['N-Gain'],
        stroke: { lineCap: 'round' },
    };

    const chartNGain = new ApexCharts(document.querySelector("#chartNGain"), nGainOptions);
    chartNGain.render();
});
</script>
@endpush
