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
            <div class="col-span-12 lg:col-span-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Prediksi Siklus Menstruasi</h3>
                    @if($nextPeriodDate)
                    <div class="mt-4 space-y-3">
                        <div class="rounded-xl bg-error-50 p-4 dark:bg-error-500/10 border border-error-100 dark:border-error-900/50">
                            <p class="text-xs font-semibold text-error-600 dark:text-error-400 mb-1">Estimasi Haid Berikutnya</p>
                            <p class="text-lg font-bold text-error-700 dark:text-error-300">{{ $nextPeriodDate->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="rounded-xl bg-warning-50 p-4 dark:bg-warning-500/10 border border-warning-100 dark:border-warning-900/50">
                            <p class="text-xs font-semibold text-warning-600 dark:text-warning-400 mb-1">Perkiraan Masa Subur</p>
                            <p class="text-xs font-bold text-warning-700 dark:text-warning-300">{{ $fertileWindowStart->format('d M') }} - {{ $fertileWindowEnd->format('d M Y') }}</p>
                        </div>
                        <div class="rounded-xl bg-brand-50 p-4 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-900/50">
                            <p class="text-xs font-semibold text-brand-600 dark:text-brand-400 mb-1">Hari Ovulasi</p>
                            <p class="text-xs font-bold text-brand-700 dark:text-brand-300">{{ $ovulationDate->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    @else
                    <p class="mt-3 text-xs text-gray-400 dark:text-gray-500 leading-relaxed">Belum ada catatan siklus haid. Masukkan tanggal haid terakhir pada formulir di samping untuk melihat estimasi siklus berikutnya.</p>
                    @endif
                </div>

                @if($recentPeriods->count() > 0)
                <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Riwayat Siklus</h3>
                    <div class="space-y-2">
                        @foreach($recentPeriods as $log)
                        <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3 dark:bg-gray-800 text-xs">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $log->start_date->format('d M Y') }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ ucfirst($log->flow_level) }} &bull; Nyeri NRS {{ $log->nrs_pain_score }}/10</p>
                            </div>
                            <span class="rounded bg-white px-2 py-1 font-bold text-gray-700 shadow-theme-xs dark:bg-gray-700 dark:text-gray-300">{{ $log->cycle_length }} hari</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Period Log Form --}}
            <div class="col-span-12 lg:col-span-8">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Catat Hari Haid Baru</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Catatan data siklus digunakan untuk memantau keteraturan dan kesehatan reproduksi.</p>

                    <form method="POST" action="{{ route('selfcare.period.store') }}" class="space-y-4" x-data="{ nrs: 0 }">
                        @csrf
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Hari Pertama Haid <span class="text-error-500">*</span></label>
                                <input type="date" name="start_date" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Hari Terakhir Haid (Opsional)</label>
                                <input type="date" name="end_date" class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Panjang Siklus (Rata-rata 28 hari)</label>
                                <input type="number" name="cycle_length" value="28" min="15" max="60" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Durasi Haid (Hari)</label>
                                <input type="number" name="period_duration" value="5" min="1" max="14" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Volume Aliran Darah</label>
                                <select name="flow_level" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    <option value="ringan">Ringan (Flek/Sedikit)</option>
                                    <option value="sedang" selected>Sedang (Normal)</option>
                                    <option value="deras">Deras</option>
                                    <option value="sangat_deras">Sangat Deras</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Warna Darah</label>
                                <select name="flow_color" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    <option value="Merah Terang">Merah Terang (Segar/Normal)</option>
                                    <option value="Cokelat">Cokelat Gelap (Oksidasi Normal)</option>
                                    <option value="Merah Muda">Merah Muda (Estrogen Rendah/Spotting)</option>
                                    <option value="Kehitaman">Kehitaman (Perlu Konsultasi)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Derajat Nyeri Haid (Skala NRS 0-10): <span class="font-bold" :class="nrs <= 3 ? 'text-success-600' : (nrs <= 6 ? 'text-warning-600' : 'text-error-600')" x-text="nrs + '/10'"></span></label>
                            <input type="range" name="nrs_pain_score" min="0" max="10" value="0" x-model="nrs" class="w-full h-2 rounded-lg appearance-none bg-gray-200 dark:bg-gray-700 cursor-pointer">
                            <div class="flex justify-between text-[11px] text-gray-400 mt-1">
                                <span>0 (Tidak Nyeri)</span>
                                <span>5 (Nyeri Sedang)</span>
                                <span>10 (Nyeri Sangat Hebat)</span>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Gejala Penyerta</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Kram Perut', 'Kembung', 'Migrain / Sakit Kepala', 'Lelah', 'Mual', 'Nyeri Punggung'] as $symptom)
                                <label class="flex items-center gap-1.5 cursor-pointer rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="symptoms[]" value="{{ $symptom }}" class="h-3.5 w-3.5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                                    <span>{{ $symptom }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Catatan Harian</label>
                            <textarea name="notes" rows="2" maxlength="500" class="w-full rounded-lg border border-gray-300 bg-transparent px-3.5 py-2 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white" placeholder="Catatan keluhan tambahan..."></textarea>
                        </div>

                        <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2 text-xs font-bold text-white hover:bg-brand-600 transition shadow-theme-xs">
                            Simpan Catatan Siklus Haid
                        </button>
                    </form>
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
