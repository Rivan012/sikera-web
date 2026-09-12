@extends('layouts.admin')
@section('title', 'Self-Care Tools')
@section('page-title', 'Pemantauan Kesehatan Mandiri')

@section('content')
<div x-data="{ tab: '{{ $isFemale ? 'period' : 'bmi' }}' }" class="space-y-6">
    {{-- Tab Navigation --}}
    <div class="flex gap-2 overflow-x-auto rounded-xl bg-gray-100 p-1 dark:bg-gray-800">
        @if($isFemale)
        <button @click="tab = 'period'" :class="tab === 'period' ? 'bg-white text-gray-900 shadow-theme-xs font-bold dark:bg-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'"
            class="rounded-lg px-4 py-2 text-xs font-medium transition whitespace-nowrap">
            🩸 Pelacak Siklus Haid
        </button>
        @endif
        <button @click="tab = 'bmi'" :class="tab === 'bmi' ? 'bg-white text-gray-900 shadow-theme-xs font-bold dark:bg-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'"
            class="rounded-lg px-4 py-2 text-xs font-medium transition whitespace-nowrap">
            ⚖️ Kalkulator IMT &amp; Status Gizi
        </button>
        <button @click="tab = 'guides'" :class="tab === 'guides' ? 'bg-white text-gray-900 shadow-theme-xs font-bold dark:bg-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'"
            class="rounded-lg px-4 py-2 text-xs font-medium transition whitespace-nowrap">
            📖 Panduan Kesehatan
        </button>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 p-4 text-xs font-semibold text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-300">
        {{ session('success') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="rounded-xl border border-warning-200 bg-warning-50 p-4 text-xs font-semibold text-warning-700 dark:border-warning-800 dark:bg-warning-950 dark:text-warning-300">
        {{ session('warning') }}
    </div>
    @endif

    {{-- Period Tracker Tab (Khusus Perempuan) --}}
    @if($isFemale)
    <div x-show="tab === 'period'" x-transition>
        <div class="grid grid-cols-12 gap-6">
            {{-- Prediction Panel --}}
            <div class="col-span-12 lg:col-span-5">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Perkiraan Siklus Haid</h3>
                        @if($prediction)
                        <span class="inline-flex items-center rounded-full bg-teal-50 px-2.5 py-0.5 text-[10px] font-bold text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                            {{ $prediction['cycle_metrics']['regularity_status'] ?? 'Panduan Mandiri' }}
                        </span>
                        @endif
                    </div>

                    @if($prediction)
                    {{-- Narasi Ringkasan Utama --}}
                    @if(!empty($prediction['summary_narrative']))
                    <div class="rounded-xl border border-teal-200 bg-teal-50/70 p-3.5 dark:border-teal-900/50 dark:bg-teal-950/30 flex items-start gap-3">
                        <div class="rounded-lg bg-teal-600 p-2 text-white shrink-0 mt-0.5">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-teal-900 dark:text-teal-200">Perkiraan Haid Berikutnya</p>
                            <p class="text-xs text-teal-800 dark:text-teal-300 mt-0.5 leading-relaxed font-medium">
                                {{ $prediction['summary_narrative'] }}
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Status Fase Hari Ini --}}
                    <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-gray-50/80 to-white p-4 dark:border-gray-800 dark:from-gray-900/30 dark:to-gray-800/10">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                Hari ke-{{ $prediction['current_status']['cycle_day'] }} Siklus
                            </span>
                            <span class="rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-gray-700 shadow-xs dark:bg-gray-800 dark:text-gray-300">
                                Peluang Hamil: {{ $prediction['current_status']['fertility_status'] }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $prediction['current_status']['phase_name'] }}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                            {{ $prediction['current_status']['phase_description'] }}
                        </p>
                    </div>

                    {{-- Metric Cards --}}
                    <div class="space-y-3">
                        {{-- 1. Haid Berikutnya --}}
                        <div class="rounded-xl bg-error-50 p-3.5 dark:bg-error-500/10 border border-error-100 dark:border-error-900/50">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-semibold text-error-700 dark:text-error-400">Estimasi Haid Berikutnya</p>
                                @if($prediction['current_status']['is_period_late'])
                                <span class="rounded bg-error-200 px-1.5 py-0.5 text-[10px] font-bold text-error-800 dark:bg-error-900 dark:text-error-200">
                                    Melewati {{ $prediction['current_status']['days_late'] }} hari
                                </span>
                                @else
                                <span class="text-[11px] font-bold text-error-600 dark:text-error-400">
                                    {{ $prediction['predictions']['next_period']['countdown_text'] ?? ($prediction['current_status']['days_until_next_period'] . ' hari lagi') }}
                                </span>
                                @endif
                            </div>
                            <p class="text-lg font-bold text-error-800 dark:text-error-200 mt-0.5">
                                {{ $prediction['predictions']['next_period']['start_formatted'] }}
                            </p>
                            <p class="text-[10px] text-error-600/80 dark:text-error-400/80 mt-0.5">
                                {{ $prediction['predictions']['next_period']['formula_applied'] }}
                            </p>
                        </div>

                        {{-- 2. Masa Subur (6 Hari) --}}
                        <div class="rounded-xl bg-warning-50 p-3.5 dark:bg-warning-500/10 border border-warning-100 dark:border-warning-900/50">
                            <p class="text-xs font-semibold text-warning-700 dark:text-warning-400">Jendela Masa Subur (6 Hari)</p>
                            <p class="text-base font-bold text-warning-800 dark:text-warning-200 mt-0.5">
                                {{ $prediction['predictions']['fertile_window']['formatted'] }}
                            </p>
                            <p class="text-[10px] text-warning-700/80 dark:text-warning-400/80 mt-0.5">
                                Peluang pembuahan paling tinggi dalam rentang 6 hari ini
                            </p>
                        </div>

                        {{-- 3. Hari Puncak Ovulasi --}}
                        <div class="rounded-xl bg-brand-50 p-3.5 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-900/50">
                            <p class="text-xs font-semibold text-brand-700 dark:text-brand-400">Puncak Ovulasi (Pelepasan Sel Telur)</p>
                            <p class="text-base font-bold text-brand-800 dark:text-brand-200 mt-0.5">
                                {{ $prediction['predictions']['ovulation']['formatted'] }}
                            </p>
                            <p class="text-[10px] text-brand-600/80 dark:text-brand-400/80 mt-0.5">
                                Waktu ketika sel telur matang dilepaskan oleh tubuh
                            </p>
                        </div>

                        {{-- 4. Ogino-Knaus jika ada variasi siklus --}}
                        @if(!empty($prediction['predictions']['ogino_knaus']['applicable']) && $prediction['cycle_metrics']['cycle_variation_days'] > 0)
                        <div class="rounded-xl bg-purple-50 p-3.5 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-900/40 text-xs">
                            <p class="font-bold text-purple-800 dark:text-purple-300">Rentang Variasi Siklus</p>
                            <p class="font-semibold text-purple-900 dark:text-purple-200 mt-0.5">
                                {{ $prediction['predictions']['ogino_knaus']['formatted'] }}
                            </p>
                            <p class="text-[10px] text-purple-600 dark:text-purple-400 mt-0.5">
                                {{ $prediction['predictions']['ogino_knaus']['formula_applied'] }}
                            </p>
                        </div>
                        @endif
                    </div>

                    {{-- Catatan Pengingat Ramah --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-3 text-[11px] text-gray-500 dark:border-gray-800 dark:bg-gray-800/40 space-y-1">
                        <p class="font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                            Catatan Pengingat
                        </p>
                        <p class="leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ $prediction['clinical_disclaimer'] }}
                        </p>
                    </div>
                    @else
                    <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed">
                        Belum ada catatan siklus haid. Masukkan tanggal haid terakhir pada formulir di samping untuk melihat perkiraan siklus berikutnya.
                    </p>
                    @endif
                </div>

                @if($recentPeriods->count() > 0)
                <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Riwayat Siklus</h3>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $recentPeriods->count() }} Catatan</span>
                    </div>
                    <div class="space-y-2.5">
                        @foreach($recentPeriods as $log)
                        <div class="rounded-xl bg-gray-50 p-3.5 dark:bg-gray-800/80 text-xs border border-gray-100 dark:border-gray-800 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-gray-900 dark:text-white">{{ $log->start_date->format('d M Y') }}</p>
                                <div class="flex items-center gap-1.5">
                                    <span class="rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-gray-700 shadow-xs dark:bg-gray-700 dark:text-gray-300">
                                        {{ $log->cycle_length }} hari
                                    </span>
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-bold {{ $log->is_regular ? 'bg-teal-50 text-teal-700 dark:bg-teal-950/40 dark:text-teal-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' }}">
                                        {{ $log->is_regular ? 'Teratur' : 'Bervariasi' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                                <span>Vol: <strong class="text-gray-700 dark:text-gray-300">{{ ucfirst($log->flow_level) }}</strong></span>
                                @if($log->pbac_score !== null)
                                <span>&bull;</span>
                                <span>PBAC: <strong class="{{ $log->pbac_score >= 100 ? 'text-rose-600 dark:text-rose-400' : 'text-teal-700 dark:text-teal-300' }}">{{ $log->pbac_score }} pt</strong> ({{ ucfirst($log->volume_category ?? '') }})</span>
                                @endif
                                <span>&bull;</span>
                                <span>Warna: <strong class="text-gray-700 dark:text-gray-300">{{ $log->flow_color }}</strong></span>
                                @if($log->blood_consistency)
                                <span>&bull;</span>
                                <span>Sifat: <strong class="text-gray-700 dark:text-gray-300">{{ $log->blood_consistency }}</strong></span>
                                @endif
                                @if($log->menarche_age)
                                <span>&bull;</span>
                                <span>Menarche: <strong class="text-gray-700 dark:text-gray-300">{{ $log->menarche_age }} th</strong></span>
                                @endif
                            </div>
                            @if(!empty($log->walidd_total_score) && $log->walidd_total_score > 0)
                            <div class="pt-1.5 border-t border-gray-200/60 dark:border-gray-700/60 flex items-center justify-between text-[11px]">
                                <span class="font-medium text-gray-600 dark:text-gray-300">Nyeri: NRS {{ $log->nrs_pain_score }}/10</span>
                                <span class="font-bold px-1.5 py-0.5 rounded text-[10px] {{ $log->walidd_total_score <= 4 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' : ($log->walidd_total_score <= 7 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200') }}">
                                    WaLIDD {{ $log->walidd_total_score }}/12 ({{ $log->walidd_category }})
                                </span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Period Log Form & Interactive Simulator --}}
            <div class="col-span-12 lg:col-span-7 space-y-6">
                {{-- Form Pencatatan Haid (5 Komponen Referensi) --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-5">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Pencatatan Siklus Haid &amp; Evaluasi Dismenore</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Lengkapi 5 komponen pemantauan kesehatan reproduksi mandiri berikut.</p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('selfcare.period.store') }}"
                        class="space-y-6"
                        x-data="{
                            menarche: '{{ old('menarche_age', $user->menarche_age ?? '') }}',
                            startDate: '{{ old('start_date', '') }}',
                            endDate: '{{ old('end_date', '') }}',
                            cycleLength: {{ old('cycle_length', 28) }},
                            periodDuration: {{ old('period_duration', 5) }},
                            isRegular: {{ old('is_regular', '1') == '1' || old('is_regular', 'true') == 'true' ? 'true' : 'false' }},
                            flowLevel: '{{ old('flow_level', 'sedang') }}',
                            flowColor: '{{ old('flow_color', 'Merah Terang') }}',
                            bloodConsistency: '{{ old('blood_consistency', 'Sedang / Normal') }}',
                            showPbac: {{ old('pbac_pads_light') || old('pbac_pads_medium') || old('pbac_pads_heavy') ? 'true' : 'false' }},
                            pbacLight: {{ old('pbac_pads_light', 0) }},
                            pbacMedium: {{ old('pbac_pads_medium', 0) }},
                            pbacHeavy: {{ old('pbac_pads_heavy', 0) }},
                            pbacClotsSmall: {{ old('pbac_clots_small', 0) }},
                            pbacClotsLarge: {{ old('pbac_clots_large', 0) }},

                            get pbacScore() {
                                return (parseInt(this.pbacLight || 0) * 1) +
                                       (parseInt(this.pbacMedium || 0) * 5) +
                                       (parseInt(this.pbacHeavy || 0) * 20) +
                                       (parseInt(this.pbacClotsSmall || 0) * 1) +
                                       (parseInt(this.pbacClotsLarge || 0) * 5);
                            },

                            get pbacStatus() {
                                const s = this.pbacScore;
                                if (s >= 100) return {
                                    badge: 'Menoragia (HMB ≥ 80 ml)',
                                    color: 'bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-200 border-rose-200',
                                    desc: 'Perdarahan haid berlebih (≥ 80 ml). Waspadai anemia defisiensi besi dan konsultasikan ke dokter spesialis obstetri & ginekologi.'
                                };
                                if (s <= 10 && parseInt(this.pbacMedium || 0) === 0 && parseInt(this.pbacHeavy || 0) === 0 && parseInt(this.pbacClotsLarge || 0) === 0) return {
                                    badge: 'Hipomenore (< 5-10 ml)',
                                    color: 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-200 border-amber-200',
                                    desc: 'Volume pengeluaran darah sangat minim / bercak flek (spotting) per siklus.'
                                };
                                return {
                                    badge: 'Normal (Eumenore 30-40 ml)',
                                    color: 'bg-teal-50 text-teal-800 dark:bg-teal-950/40 dark:text-teal-200 border-teal-200',
                                    desc: 'Volume pengeluaran darah fisiologis sehat (dalam batas normal < 80 ml per siklus).'
                                };
                            },

                            applyPbac() {
                                const s = this.pbacScore;
                                if (s >= 100) {
                                    this.flowLevel = 'sangat_deras';
                                } else if (s <= 10 && parseInt(this.pbacMedium || 0) === 0 && parseInt(this.pbacHeavy || 0) === 0) {
                                    this.flowLevel = 'ringan';
                                } else if (s > 60) {
                                    this.flowLevel = 'deras';
                                } else {
                                    this.flowLevel = 'sedang';
                                }
                            },

                            hasDysmenorrhea: {{ old('has_dysmenorrhea', '0') == '1' || old('nrs_pain_score', 0) > 0 ? 'true' : 'false' }},
                            nrs: {{ old('nrs_pain_score', 0) }},
                            workingAbility: {{ old('walidd_working_ability', 0) }},
                            locations: {{ json_encode(old('walidd_locations', [])) }},
                            painDays: {{ old('walidd_pain_days', 0) }},

                            toggleLocation(loc) {
                                if (this.locations.includes(loc)) {
                                    this.locations = this.locations.filter(l => l !== loc);
                                } else {
                                    this.locations.push(loc);
                                }
                            },

                            get locationScore() {
                                return Math.min(3, this.locations.length);
                            },

                            get intensityScore() {
                                const n = parseInt(this.nrs || 0);
                                if (n === 0) return 0;
                                if (n <= 3) return 1;
                                if (n <= 7) return 2;
                                return 3;
                            },

                            get painDaysScore() {
                                const d = parseInt(this.painDays || 0);
                                if (d === 0) return 0;
                                if (d <= 2) return 1;
                                if (d <= 4) return 2;
                                return 3;
                            },

                            get waliddTotal() {
                                if (!this.hasDysmenorrhea && parseInt(this.nrs || 0) === 0) return 0;
                                return parseInt(this.workingAbility || 0) + this.locationScore + this.intensityScore + this.painDaysScore;
                            },

                            get waliddCategory() {
                                const total = this.waliddTotal;
                                if (total === 0) return 'Tidak Dismenore';
                                if (total <= 4) return 'Dismenore Ringan';
                                if (total <= 7) return 'Dismenore Sedang';
                                return 'Dismenore Berat';
                            },

                            get waliddBadgeClass() {
                                const total = this.waliddTotal;
                                if (total === 0) return 'bg-success-50 text-success-700 border-success-200 dark:bg-success-950/40 dark:text-success-300';
                                if (total <= 4) return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300';
                                if (total <= 7) return 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/40 dark:text-orange-300';
                                return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300';
                            },

                            get waliddInterpretation() {
                                const total = this.waliddTotal;
                                if (total === 0) return 'Kondisi fisiologis normal tanpa keluhan nyeri kram menstruasi. Pertahankan gaya hidup sehat.';
                                if (total <= 4) return 'Kram nyeri haid tergolong ringan dan tidak membatasi aktivitas harian. Disarankan kompres hangat dan peregangan ringan.';
                                if (total <= 7) return 'Nyeri kram haid mulai mengganggu konsentrasi dan aktivitas. Istirahat cukup, kurangi kafein, dan gunakan pereda kram bila perlu.';
                                return 'Nyeri kram haid berat multidimensional yang membatasi fisik secara signifikan (butuh bed rest). Sangat disarankan konsultasi ke dokter spesialis obstetri & ginekologi.';
                            },

                            get nrsDescription() {
                                const n = parseInt(this.nrs || 0);
                                if (n === 0) return '0 - Tidak ada rasa sakit sama sekali (kondisi normal).';
                                if (n === 1) return '1 - Sangat ringan seperti gigitan nyamuk, hampir tak terasa.';
                                if (n === 2) return '2 - Nyeri ringan seperti cubitan, sedikit mengganggu.';
                                if (n === 3) return '3 - Nyeri seperti suntikan, masih bisa beraktivitas & komunikasi.';
                                if (n === 4) return '4 - Nyeri seperti sakit gigi, mulai mengganggu efisiensi kegiatan.';
                                if (n === 5) return '5 - Nyeri menusuk seperti terkilir, sulit diabaikan beberapa menit.';
                                if (n === 6) return '6 - Nyeri menusuk kuat, sangat sulit diabaikan.';
                                if (n === 7) return '7 - Nyeri berat mendominasi indra, tidur dan aktivitas terganggu.';
                                if (n === 8) return '8 - Nyeri sangat kuat, tidak bisa berpikir jernih, fisik terbatas.';
                                if (n === 9) return '9 - Nyeri tak tertahankan, mengerang/menangis, komunikasi terputus.';
                                return '10 - Nyeri hebat tak berdaya, harus berbaring di tempat tidur (bed rest).';
                            },

                            updateDuration() {
                                if (this.startDate && this.endDate) {
                                    const s = new Date(this.startDate);
                                    const e = new Date(this.endDate);
                                    const diff = Math.round((e - s) / (1000 * 60 * 60 * 24)) + 1;
                                    if (diff > 0 && diff <= 14) {
                                        this.periodDuration = diff;
                                    }
                                }
                            }
                        }"
                    >
                        @csrf

                        {{-- 1. Menarche / Usia Pertama Menstruasi --}}
                        <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-600 text-[10px] font-bold text-white">1</span>
                                    Menarche / Usia Pertama Menstruasi
                                </label>
                                <span class="rounded-full bg-teal-50 px-2.5 py-0.5 text-[10px] font-bold text-teal-700 dark:bg-teal-950/60 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/40">
                                    Normalnya 10 - 15 Tahun
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="relative w-full sm:w-48">
                                    <input
                                        type="number"
                                        name="menarche_age"
                                        min="8"
                                        max="25"
                                        x-model="menarche"
                                        placeholder="Contoh: 12"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white pr-12"
                                    >
                                    <span class="absolute right-3 top-2 text-xs font-medium text-gray-400">Tahun</span>
                                </div>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 hidden sm:inline">
                                    Usia saat kamu pertama kali mendapatkan menstruasi pertama seumur hidup.
                                </span>
                            </div>
                        </div>

                        {{-- 2. Waktu Siklus Haid --}}
                        <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/30 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-600 text-[10px] font-bold text-white">2</span>
                                    Waktu Siklus Haid
                                </label>
                                <span class="text-[11px] text-gray-400">Kalender &amp; Rentang Tanggal</span>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                                        Hari Pertama Haid (LMP) <span class="text-error-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        name="start_date"
                                        required
                                        x-model="startDate"
                                        @change="updateDuration()"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-800 outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                    >
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                                        Tanggal Selesai (Opsional)
                                    </label>
                                    <input
                                        type="date"
                                        name="end_date"
                                        x-model="endDate"
                                        @change="updateDuration()"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-800 outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                    >
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                                        Panjang Siklus (Hari) <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="number"
                                            name="cycle_length"
                                            min="15"
                                            max="60"
                                            required
                                            x-model="cycleLength"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-800 outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white pr-10"
                                        >
                                        <span class="absolute right-3 top-2 text-xs text-gray-400">Hari</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="period_duration" :value="periodDuration">
                        </div>

                        {{-- 3. Karakteristik Aliran & Warna (Volume & Sifat) --}}
                        <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/30 space-y-3">
                            <label class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-600 text-[10px] font-bold text-white">3</span>
                                Karakteristik Aliran &amp; Warna Darah
                            </label>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                {{-- Volume --}}
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                                        Volume Aliran Darah <span class="text-error-500">*</span>
                                    </label>
                                    <select
                                        name="flow_level"
                                        required
                                        x-model="flowLevel"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-800 outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                    >
                                        <option value="ringan">Hipomenore / Flek (&lt; 5 &ndash; 10 ml / Bercak)</option>
                                        <option value="sedang">Normal / Eumenore (30 &ndash; 40 ml, 3&ndash;5x ganti pembalut/hari)</option>
                                        <option value="sangat_deras">Menoragia / HMB (&gt; 80 ml, pembalut penuh tiap 1-2 jam / gumpalan &gt; 2,5 cm)</option>
                                    </select>
                                </div>

                                {{-- Warna --}}
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                                        Warna Darah Haid <span class="text-error-500">*</span>
                                    </label>
                                    <select
                                        name="flow_color"
                                        required
                                        x-model="flowColor"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-800 outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                    >
                                        <option value="Merah Terang">Merah Terang (Segar &amp; Sehat)</option>
                                        <option value="Cokelat">Cokelat (Oksidasi / Tua)</option>
                                        <option value="Merah Muda">Merah Muda / Pucat</option>
                                        <option value="Hitam">Hitam / Abnormal (Perlu Waspada)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Bantuan Penilaian Volume PBAC (Higham Pictorial Assessment) --}}
                            <div class="pt-2 border-t border-gray-200/60 dark:border-gray-700/60">
                                <div class="flex items-center justify-between">
                                    <button
                                        type="button"
                                        @click="showPbac = !showPbac"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-600 hover:text-teal-700 dark:text-teal-400"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        <span x-text="showPbac ? 'Sembunyikan Skoring Visual PBAC' : 'Bantu Saya Hitung Skor Visual PBAC (Higham)'"></span>
                                    </button>
                                    <a href="{{ route('selfcare.blood_guide') }}" target="_blank" class="text-[11px] font-medium text-gray-500 hover:text-teal-600 dark:text-gray-400">
                                        Panduan Standar FIGO &rarr;
                                    </a>
                                </div>

                                <div x-show="showPbac" x-transition class="mt-3 rounded-xl bg-white p-4 border border-teal-200 dark:bg-gray-900 dark:border-gray-700 space-y-3.5">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                Instrumen Skoring Visual PBAC (Higham et al.)
                                            </h4>
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                                Masukkan jumlah pembalut dan gumpalan darah yang dialami selama siklus ini:
                                            </p>
                                        </div>
                                        <span class="rounded-full bg-teal-50 px-2.5 py-0.5 text-[10px] font-bold text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200">
                                            Ambang Batas 100 Poin
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        {{-- 1. Pembalut --}}
                                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/60 space-y-2">
                                            <p class="text-[11px] font-bold text-gray-800 dark:text-gray-200">1. Penggunaan Pembalut / Tampon</p>
                                            <div class="space-y-1.5 text-xs">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-[11px] text-gray-600 dark:text-gray-300">Noda sedikit (&le; 1/3) &bull; 1 pt</label>
                                                    <input type="number" min="0" max="99" x-model="pbacLight" @input="applyPbac()" class="w-16 rounded border border-gray-300 bg-white px-2 py-1 text-center text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <label class="text-[11px] text-gray-600 dark:text-gray-300">Noda sedang (&approx; 1/2) &bull; 5 pt</label>
                                                    <input type="number" min="0" max="99" x-model="pbacMedium" @input="applyPbac()" class="w-16 rounded border border-gray-300 bg-white px-2 py-1 text-center text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <label class="text-[11px] text-gray-600 dark:text-gray-300">Basah jenuh penuh &bull; 20 pt</label>
                                                    <input type="number" min="0" max="99" x-model="pbacHeavy" @input="applyPbac()" class="w-16 rounded border border-gray-300 bg-white px-2 py-1 text-center text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 2. Gumpalan --}}
                                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/60 space-y-2">
                                            <p class="text-[11px] font-bold text-gray-800 dark:text-gray-200">2. Gumpalan Darah (Clots)</p>
                                            <div class="space-y-1.5 text-xs">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-[11px] text-gray-600 dark:text-gray-300">Ukuran kecil (&lt; 2,5 cm) &bull; 1 pt</label>
                                                    <input type="number" min="0" max="99" x-model="pbacClotsSmall" @input="applyPbac()" class="w-16 rounded border border-gray-300 bg-white px-2 py-1 text-center text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <label class="text-[11px] text-gray-600 dark:text-gray-300">Ukuran besar (&ge; 2,5 cm) &bull; 5 pt</label>
                                                    <input type="number" min="0" max="99" x-model="pbacClotsLarge" @input="applyPbac()" class="w-16 rounded border border-gray-300 bg-white px-2 py-1 text-center text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Live Hasil PBAC --}}
                                    <div class="rounded-xl p-3 border text-xs" :class="pbacStatus.color">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold">Total Skor PBAC:</span>
                                                <span class="font-mono text-sm font-extrabold" x-text="pbacScore + ' Poin'"></span>
                                            </div>
                                            <span class="rounded px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide" x-text="pbacStatus.badge"></span>
                                        </div>
                                        <p class="text-[11px] mt-1 leading-relaxed" x-text="pbacStatus.desc"></p>
                                    </div>

                                    {{-- Hidden inputs --}}
                                    <input type="hidden" name="pbac_pads_light" :value="pbacLight">
                                    <input type="hidden" name="pbac_pads_medium" :value="pbacMedium">
                                    <input type="hidden" name="pbac_pads_heavy" :value="pbacHeavy">
                                    <input type="hidden" name="pbac_clots_small" :value="pbacClotsSmall">
                                    <input type="hidden" name="pbac_clots_large" :value="pbacClotsLarge">
                                </div>
                            </div>

                            {{-- Sifat / Konsistensi --}}
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                                    Sifat &amp; Konsistensi Darah
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @foreach(['Cair / Encer', 'Sedang / Normal', 'Bergumpal', 'Berlendir'] as $consistency)
                                    <label
                                        class="flex items-center gap-2 rounded-lg border p-2 text-xs cursor-pointer transition"
                                        :class="bloodConsistency === '{{ $consistency }}' ? 'border-teal-500 bg-teal-50/80 text-teal-900 font-bold dark:bg-teal-950/40 dark:text-teal-200' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300'"
                                    >
                                        <input
                                            type="radio"
                                            name="blood_consistency"
                                            value="{{ $consistency }}"
                                            x-model="bloodConsistency"
                                            class="text-teal-600 focus:ring-teal-500"
                                        >
                                        <span class="text-[11px]">{{ $consistency }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- 4. Keteraturan Haid --}}
                        <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/30 space-y-2">
                            <label class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-600 text-[10px] font-bold text-white">4</span>
                                Keteraturan Haid
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label
                                    class="flex items-start gap-3 rounded-xl border p-3 cursor-pointer transition"
                                    :class="isRegular ? 'border-teal-500 bg-teal-50/80 dark:bg-teal-950/40' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900'"
                                >
                                    <input
                                        type="radio"
                                        name="is_regular"
                                        value="1"
                                        :checked="isRegular"
                                        @change="isRegular = true"
                                        class="mt-0.5 text-teal-600 focus:ring-teal-500"
                                    >
                                    <div>
                                        <p class="text-xs font-bold text-gray-900 dark:text-white">Teratur</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Siklus datang setiap 21-35 hari secara konsisten setiap bulannya.</p>
                                    </div>
                                </label>
                                <label
                                    class="flex items-start gap-3 rounded-xl border p-3 cursor-pointer transition"
                                    :class="!isRegular ? 'border-amber-500 bg-amber-50/80 dark:bg-amber-950/40' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900'"
                                >
                                    <input
                                        type="radio"
                                        name="is_regular"
                                        value="0"
                                        :checked="!isRegular"
                                        @change="isRegular = false"
                                        class="mt-0.5 text-amber-600 focus:ring-amber-500"
                                    >
                                    <div>
                                        <p class="text-xs font-bold text-gray-900 dark:text-white">Tidak Teratur / Bervariasi</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Sering maju/mundur lebih dari 7 hari atau siklus tidak menentu.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- 5. Disminore (Pengukuran Nyeri NRS & Instrumen WaLIDD) --}}
                        <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/30 space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-600 text-[10px] font-bold text-white">5</span>
                                    Evaluasi Nyeri Haid (Dismenore) &amp; WaLIDD
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="has_dysmenorrhea"
                                        value="1"
                                        x-model="hasDysmenorrhea"
                                        class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                    >
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Mengalami Nyeri Kram</span>
                                </label>
                            </div>

                            {{-- Form Evaluasi WaLIDD & NRS (Tampil jika hasDysmenorrhea / nrs > 0) --}}
                            <div x-show="hasDysmenorrhea || nrs > 0" x-transition class="space-y-4 pt-3 border-t border-gray-200/80 dark:border-gray-700/80">
                                {{-- A. Slider Skala Nyeri NRS 0-10 --}}
                                <div class="rounded-xl bg-white p-3.5 border border-gray-200 dark:bg-gray-900 dark:border-gray-700 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            A. Pengukuran Skala Nyeri Numeric Rating Scale (NRS)
                                        </span>
                                        <span
                                            class="rounded px-2 py-0.5 text-xs font-bold"
                                            :class="nrs === 0 ? 'bg-gray-100 text-gray-700' : (nrs <= 3 ? 'bg-teal-100 text-teal-800' : (nrs <= 7 ? 'bg-orange-100 text-orange-800' : 'bg-rose-100 text-rose-800'))"
                                            x-text="'Skor NRS: ' + nrs + '/10'"
                                        ></span>
                                    </div>
                                    <input
                                        type="range"
                                        name="nrs_pain_score"
                                        min="0"
                                        max="10"
                                        x-model="nrs"
                                        class="w-full h-2 rounded-lg appearance-none bg-gray-200 dark:bg-gray-700 cursor-pointer accent-teal-600"
                                    >
                                    <div class="flex justify-between text-[10px] text-gray-400 font-medium">
                                        <span>0 (Bebas Nyeri)</span>
                                        <span>1-3 (Ringan)</span>
                                        <span>4-7 (Sedang)</span>
                                        <span>8-10 (Berat)</span>
                                    </div>
                                    <div class="rounded-lg bg-gray-50 p-2.5 text-[11px] text-gray-600 dark:bg-gray-800/60 dark:text-gray-300 leading-relaxed font-medium">
                                        <span class="font-bold text-gray-800 dark:text-gray-200">Keterangan Klinis: </span>
                                        <span x-text="nrsDescription"></span>
                                    </div>
                                </div>

                                {{-- B. Instrumen Pertanyaan WaLIDD --}}
                                <div class="space-y-3">
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200">
                                        B. Pertanyaan Evaluasi Multidimensional WaLIDD
                                    </p>

                                    {{-- W: Working Ability --}}
                                    <div class="rounded-xl bg-white p-3 border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                                        <label class="block text-[11px] font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                                            W - Kemampuan Kerja &amp; Aktivitas (Working Ability)
                                        </label>
                                        <select
                                            name="walidd_working_ability"
                                            x-model="workingAbility"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                        >
                                            <option value="0">0 Poin: Tidak ada gangguan aktivitas sama sekali</option>
                                            <option value="1">1 Poin: Sedikit terganggu, namun masih mampu beraktivitas normal</option>
                                            <option value="2">2 Poin: Aktivitas terhambat sedang (sulit konsentrasi / efisiensi turun)</option>
                                            <option value="3">3 Poin: Lumpuh total / tidak sanggup beraktivitas / bed rest</option>
                                        </select>
                                    </div>

                                    {{-- L: Location --}}
                                    <div class="rounded-xl bg-white p-3 border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="block text-[11px] font-bold text-gray-800 dark:text-gray-200">
                                                L - Area Penjalaran Nyeri Dismenore (Location)
                                            </label>
                                            <span class="text-[10px] font-bold text-teal-600 dark:text-teal-400" x-text="'Bobot L: ' + locationScore + ' Poin'"></span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                            <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    name="walidd_locations[]"
                                                    value="perut_bawah"
                                                    :checked="locations.includes('perut_bawah')"
                                                    @change="toggleLocation('perut_bawah')"
                                                    class="h-3.5 w-3.5 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                                >
                                                <span class="text-[11px]">Perut Bawah</span>
                                            </label>
                                            <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    name="walidd_locations[]"
                                                    value="pinggang"
                                                    :checked="locations.includes('pinggang')"
                                                    @change="toggleLocation('pinggang')"
                                                    class="h-3.5 w-3.5 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                                >
                                                <span class="text-[11px]">Pinggang / Punggung</span>
                                            </label>
                                            <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    name="walidd_locations[]"
                                                    value="paha_dalam"
                                                    :checked="locations.includes('paha_dalam')"
                                                    @change="toggleLocation('paha_dalam')"
                                                    class="h-3.5 w-3.5 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                                >
                                                <span class="text-[11px]">Paha Dalam / Selangkangan</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- D: Days of Pain --}}
                                    <div class="rounded-xl bg-white p-3 border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="block text-[11px] font-bold text-gray-800 dark:text-gray-200">
                                                D - Durasi Hari Nyeri Kram (Days of Pain)
                                            </label>
                                            <span class="text-[10px] font-bold text-teal-600 dark:text-teal-400" x-text="'Bobot D: ' + painDaysScore + ' Poin'"></span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="relative w-36">
                                                <input
                                                    type="number"
                                                    name="walidd_pain_days"
                                                    min="0"
                                                    max="14"
                                                    x-model="painDays"
                                                    placeholder="0"
                                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white pr-10"
                                                >
                                                <span class="absolute right-3 top-1.5 text-xs text-gray-400">Hari</span>
                                            </div>
                                            <span class="text-[11px] text-gray-500">
                                                (0 hari: 0 pt, 1-2 hari: 1 pt, 3-4 hari: 2 pt, &ge;5 hari: 3 pt)
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- C. Live Automatic WaLIDD Interpretation Card --}}
                                <div class="rounded-xl p-4 border" :class="waliddBadgeClass">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs">Hasil Otomatis Skor WaLIDD:</span>
                                            <span class="font-mono text-xs font-bold" x-text="'W(' + workingAbility + ') + L(' + locationScore + ') + I(' + intensityScore + ') + D(' + painDaysScore + ') = ' + waliddTotal + '/12'"></span>
                                        </div>
                                        <span class="rounded-md bg-white/90 px-2 py-0.5 text-xs font-extrabold uppercase tracking-wide shadow-xs dark:bg-gray-900" x-text="waliddCategory"></span>
                                    </div>
                                    <p class="text-xs leading-relaxed mt-1" x-text="waliddInterpretation"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Gejala Penyerta & Catatan Tambahan --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Gejala Penyerta (Opsional)</label>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(['Kram Perut', 'Kembung', 'Migrain', 'Lelah', 'Mual', 'Nyeri Punggung', 'Perubahan Mood'] as $symptom)
                                    <label class="flex items-center gap-1.5 cursor-pointer rounded-lg border border-gray-200 px-2.5 py-1 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="symptoms[]" value="{{ $symptom }}" class="h-3 w-3 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        <span class="text-[11px]">{{ $symptom }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Catatan Harian (Opsional)</label>
                                <textarea name="notes" rows="2" maxlength="500" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-teal-500 dark:border-gray-700 dark:text-white" placeholder="Catatan keluhan tambahan atau pola makan..."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="w-full sm:w-auto rounded-xl bg-teal-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-teal-700 transition shadow-theme-xs flex items-center justify-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Simpan Catatan Siklus Haid &amp; Evaluasi (+10 Poin)
                        </button>
                    </form>
                </div>

                {{-- Kalkulator & Simulasi Cepat (Formula Ilmiah) --}}
                <div
                    x-data="{
                        calcLmp: '{{ date('Y-m-d') }}',
                        calcCycle: 28,
                        calcDuration: 5,
                        formatDate(d) {
                            if (!d) return '-';
                            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
                        },
                        getNextPeriod() {
                            if (!this.calcLmp) return null;
                            const d = new Date(this.calcLmp);
                            d.setDate(d.getDate() + parseInt(this.calcCycle || 28));
                            return d;
                        },
                        getOvulation() {
                            const np = this.getNextPeriod();
                            if (!np) return null;
                            const d = new Date(np);
                            d.setDate(d.getDate() - 14);
                            return d;
                        },
                        getFertileStart() {
                            const ov = this.getOvulation();
                            if (!ov) return null;
                            const d = new Date(ov);
                            d.setDate(d.getDate() - 5);
                            return d;
                        },
                        getFertileEnd() {
                            const ov = this.getOvulation();
                            if (!ov) return null;
                            const d = new Date(ov);
                            d.setDate(d.getDate() + 1);
                            return d;
                        }
                    }"
                    class="rounded-2xl border border-teal-200 bg-gradient-to-br from-teal-50/40 to-white p-6 shadow-theme-xs dark:border-teal-900/50 dark:bg-white/[0.02]"
                >
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="rounded-lg bg-teal-600 p-1.5 text-white">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </span>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Simulasi &amp; Kalkulator Siklus Mandiri</h3>
                        </div>
                        <span class="text-[10px] font-bold text-teal-700 dark:text-teal-400 bg-teal-100 dark:bg-teal-900/50 px-2 py-0.5 rounded-full">
                            Kalkulator Cepat
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        Gunakan simulasi interaktif ini untuk menghitung estimasi haid dan masa subur dengan memasukkan tanggal haid terakhir dan panjang siklus yang diinginkan.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                        <div>
                            <label class="block text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Haid (LMP)</label>
                            <input type="date" x-model="calcLmp" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">Panjang Siklus (Hari)</label>
                            <input type="number" min="15" max="60" x-model="calcCycle" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi Haid (Hari)</label>
                            <input type="number" min="1" max="14" x-model="calcDuration" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>

                    {{-- Simulation Results --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-teal-100 dark:border-teal-900/30">
                        <div class="rounded-xl bg-error-50/70 p-3 text-xs dark:bg-error-950/20 border border-error-100 dark:border-error-900/40">
                            <span class="text-[10px] font-bold text-error-600 dark:text-error-400 uppercase">Haid Berikutnya</span>
                            <p class="font-bold text-error-800 dark:text-error-300 text-sm mt-0.5" x-text="formatDate(getNextPeriod())"></p>
                            <span class="text-[10px] text-gray-400">LMP + <span x-text="calcCycle"></span> hari</span>
                        </div>
                        <div class="rounded-xl bg-warning-50/70 p-3 text-xs dark:bg-warning-950/20 border border-warning-100 dark:border-warning-900/40">
                            <span class="text-[10px] font-bold text-warning-700 dark:text-warning-400 uppercase">Jendela Masa Subur</span>
                            <p class="font-bold text-warning-800 dark:text-warning-300 text-xs mt-0.5" x-text="formatDate(getFertileStart()) + ' s.d ' + formatDate(getFertileEnd())"></p>
                            <span class="text-[10px] text-gray-400">Rentang 6 hari masa subur</span>
                        </div>
                        <div class="rounded-xl bg-teal-50/70 p-3 text-xs dark:bg-teal-950/20 border border-teal-100 dark:border-teal-900/40">
                            <span class="text-[10px] font-bold text-teal-700 dark:text-teal-400 uppercase">Puncak Ovulasi</span>
                            <p class="font-bold text-teal-800 dark:text-teal-300 text-sm mt-0.5" x-text="formatDate(getOvulation())"></p>
                            <span class="text-[10px] text-gray-400">14 hari sebelum haid berikutnya</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- BMI Calculator Tab (Untuk Semua Pengguna) --}}
    <div x-show="tab === 'bmi'" x-transition>
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 lg:col-span-5">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Kalkulator Indeks Massa Tubuh (IMT)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Hitung status gizi mandiri berdasarkan tinggi dan berat badan.</p>

                    <form method="POST" action="{{ route('selfcare.bmi.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Berat Badan (kg) <span class="text-error-500">*</span></label>
                            <input type="number" name="weight_kg" step="0.1" min="20" max="250" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white" placeholder="Contoh: 55.0">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Tinggi Badan (cm) <span class="text-error-500">*</span></label>
                            <input type="number" name="height_cm" step="0.1" min="80" max="250" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white" placeholder="Contoh: 160.0">
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-brand-500 py-2.5 text-xs font-bold text-white hover:bg-brand-600 transition shadow-theme-xs">
                            Hitung Status Gizi IMT
                        </button>
                    </form>
                </div>

                @if($latestBmi)
                <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Hasil Pengukuran Terakhir</h3>
                    <div class="text-center py-3">
                        <p class="text-3xl font-bold {{ $latestBmi->bmi_value < 18.5 ? 'text-warning-600' : ($latestBmi->bmi_value < 23 ? 'text-success-600' : ($latestBmi->bmi_value < 25 ? 'text-warning-600' : 'text-error-600')) }}">{{ $latestBmi->bmi_value }}</p>
                        <p class="mt-1 text-xs font-bold text-gray-800 dark:text-white">{{ $latestBmi->category }}</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed border-t border-gray-100 dark:border-gray-800 pt-3">{{ $latestBmi->advice }}</p>
                </div>
                @endif
            </div>

            <div class="col-span-12 lg:col-span-7">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Riwayat Pengukuran IMT</h3>
                    @if($recentBmi->count() > 0)
                    <div class="space-y-2.5">
                        @foreach($recentBmi as $bmi)
                        <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3.5 dark:bg-gray-800 text-xs">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $bmi->weight_kg }} kg &bull; {{ $bmi->height_cm }} cm</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $bmi->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-bold {{ $bmi->bmi_value < 18.5 ? 'text-warning-600' : ($bmi->bmi_value < 23 ? 'text-success-600' : ($bmi->bmi_value < 25 ? 'text-warning-600' : 'text-error-600')) }}">{{ $bmi->bmi_value }}</p>
                                <p class="text-[10px] text-gray-400 font-medium">{{ $bmi->category }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-gray-400 text-center py-8">Belum ada riwayat pengukuran. Gunakan kalkulator di samping untuk mulai.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Guides Tab --}}
    <div x-show="tab === 'guides'" x-transition>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            @if($isFemale)
            <a href="{{ route('selfcare.blood_guide') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 transition hover:border-error-300 hover:shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-error-50 dark:bg-error-500/10 mb-4">
                    <svg class="fill-error-500" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2c-5.33 4.55-8 8.48-8 11.8 0 4.98 3.8 8.2 8 8.2s8-3.22 8-8.2c0-3.32-2.67-7.25-8-11.8z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 group-hover:text-error-600 dark:text-white">Panduan Warna Darah Haid</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Kenali variasi warna dan tekstur darah menstruasi: kapan normal, kapan perlu pemeriksaan medis.</p>
            </a>
            @endif

            <a href="{{ route('selfcare.hygiene_guide') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 transition hover:border-brand-300 hover:shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10 mb-4">
                    <svg class="fill-brand-500" width="24" height="24" viewBox="0 0 24 24"><path d="M7 14c-1.66 0-3-1.34-3-3 0-1.31.84-2.41 2-2.83V2h2v6.17c1.16.42 2 1.52 2 2.83 0 1.66-1.34 3-3 3zm10-4c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zM7 16c-2.76 0-5 2.24-5 5h10c0-2.76-2.24-5-5-5zm10-2c-2.76 0-5 2.24-5 5h10c0-2.76-2.24-5-5-5z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 group-hover:text-brand-600 dark:text-white">Tips Higienitas Genitalia</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Panduan kebersihan organ reproduksi harian: arah membasuh yang benar, pemilihan pakaian dalam, dan menjaga flora normal.</p>
            </a>
        </div>
    </div>
</div>
@endsection
