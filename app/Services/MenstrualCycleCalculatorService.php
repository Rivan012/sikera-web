<?php

namespace App\Services;

use App\Models\PeriodLog;
use App\Models\User;
use Carbon\Carbon;

class MenstrualCycleCalculatorService
{
    /**
     * Panduan & Formula Perhitungan Siklus Menstruasi
     */
    public const SCIENTIFIC_REFERENCES = [
        [
            'component' => 'Prediksi Haid Berikutnya',
            'formula' => 'Hari ke-1 Haid Berikutnya = LMP + Rata-rata Panjang Siklus',
            'citation' => 'Pola Siklus Bulanan',
        ],
        [
            'component' => 'Estimasi Hari Ovulasi',
            'formula' => 'Hari Ovulasi = Estimasi Hari ke-1 Haid Berikutnya - 14 Hari',
            'citation' => 'Fase Pelepasan Sel Telur',
        ],
        [
            'component' => 'Jendela Masa Subur (6 Hari)',
            'formula' => 'Rentang 6 Hari: (Hari Ovulasi - 5 Hari) sampai (Hari Ovulasi + 1 Hari)',
            'citation' => 'Peluang Pembuahan Optimal',
        ],
        [
            'component' => 'Variasi Siklus',
            'formula' => 'Awal = (Siklus Terpendek - 18); Akhir = (Siklus Terpanjang - 11)',
            'citation' => 'Metode Rentang Siklus Mandiri',
        ],
    ];

    /**
     * Catatan Pengingat & Batasan Pemantauan Mandiri
     */
    public const CLINICAL_DISCLAIMER = 'Perkiraan siklus haid ini dihitung sebagai panduan pola tubuh mandiri agar kamu lebih siap setiap bulannya. Tanggal sebenarnya dapat maju atau mundur beberapa hari tergantung kondisi fisik dan pikiranmu.';

    /**
     * Hitung prediksi siklus menstruasi, masa subur, ovulasi, dan fase siklus berdasarkan pola tubuh.
     *
     * @param  Carbon|string  $lastPeriodDate  Hari Pertama Haid Terakhir (LMP)
     * @param  int|null  $cycleLength  Panjang siklus dasar pengguna (default 28)
     * @param  int|null  $periodDuration  Durasi perdarahan haid (default 5, normal 2-7 hari)
     * @param  array|null  $cycleHistory  Riwayat panjang siklus dari 3-6 bulan terakhir
     * @param  Carbon|null  $targetDate  Tanggal acuan evaluasi fase (default: hari ini)
     * @param  int  $projectionCount  Jumlah siklus ke depan yang diproyeksikan (default: 3)
     * @return array<string, mixed>
     */
    public function calculate(
        Carbon|string $lastPeriodDate,
        ?int $cycleLength = 28,
        ?int $periodDuration = 5,
        ?array $cycleHistory = null,
        ?Carbon $targetDate = null,
        int $projectionCount = 3
    ): array {
        $lmp = $lastPeriodDate instanceof Carbon ? $lastPeriodDate->copy()->startOfDay() : Carbon::parse($lastPeriodDate)->startOfDay();
        $target = $targetDate ? $targetDate->copy()->startOfDay() : now()->startOfDay();

        // Validasi & normalisasi durasi haid (normal: 2-7 hari)
        $duration = ($periodDuration && $periodDuration >= 1 && $periodDuration <= 14) ? $periodDuration : 5;

        // Evaluasi riwayat siklus
        $validHistory = [];
        if (is_array($cycleHistory) && count($cycleHistory) > 0) {
            foreach ($cycleHistory as $val) {
                if (is_numeric($val) && (int) $val >= 15 && (int) $val <= 60) {
                    $validHistory[] = (int) $val;
                }
            }
        }

        if (count($validHistory) >= 2) {
            // Ambil maksimal 6 siklus terakhir untuk rata-rata bergerak
            $recentHistory = array_slice($validHistory, -6);
            $avgCycleLength = (int) round(array_sum($recentHistory) / count($recentHistory));
            $shortestCycle = (int) min($recentHistory);
            $longestCycle = (int) max($recentHistory);
            $cycleVariation = $longestCycle - $shortestCycle;
            $hasHistory = true;
        } else {
            $baseLength = ($cycleLength && $cycleLength >= 15 && $cycleLength <= 60) ? $cycleLength : 28;
            $avgCycleLength = $baseLength;
            $shortestCycle = $baseLength;
            $longestCycle = $baseLength;
            $cycleVariation = 0;
            $hasHistory = false;
        }

        // Kategori keteraturan siklus
        $isRegular = ($cycleVariation <= 7);
        $regularityStatus = $hasHistory
            ? ($isRegular ? 'Siklus Teratur' : 'Siklus Bervariasi')
            : 'Estimasi Standar (28 Hari)';

        // Kategori panjang siklus
        if ($avgCycleLength < 21) {
            $cycleCategory = 'Siklus Pendek (< 21 hari)';
            $cycleCategoryDesc = 'Siklus haidmu datang lebih cepat dari rentang umum. Jaga pola istirahat dan konsultasikan ke dokter jika terus berulang.';
        } elseif ($avgCycleLength > 35) {
            $cycleCategory = 'Siklus Panjang (> 35 hari)';
            $cycleCategoryDesc = 'Jarak antar haidmu lebih panjang dari rata-rata. Hal ini bisa dipengaruhi oleh stres, kelelahan, atau perubahan pola tidur.';
        } else {
            $cycleCategory = 'Normal (21 - 35 hari)';
            $cycleCategoryDesc = 'Panjang siklus haidmu berada dalam rentang yang sehat dan normal.';
        }

        // Kategori durasi perdarahan haid
        if ($duration < 2) {
            $durationCategory = 'Durasi Singkat (< 2 hari)';
        } elseif ($duration > 7) {
            $durationCategory = 'Durasi Memanjang (> 7 hari)';
        } else {
            $durationCategory = 'Normal (2 - 7 hari)';
        }

        // 1. Prediksi Hari ke-1 Haid Berikutnya (LMP + Rata-rata Siklus)
        $nextPeriodStart = $lmp->copy()->addDays($avgCycleLength);
        $nextPeriodEnd = $nextPeriodStart->copy()->addDays($duration - 1);

        // 2. Estimasi Hari Ovulasi (14 Hari sebelum haid berikutnya)
        $ovulationDate = $nextPeriodStart->copy()->subDays(14);

        // 3. Jendela Masa Subur Standar (Ovulasi - 5 Hari s.d. Ovulasi + 1 Hari)
        $fertileStart = $ovulationDate->copy()->subDays(5);
        $fertileEnd = $ovulationDate->copy()->addDays(1);

        // 4. Formula Variasi Siklus (Metode Rentang Terpendek-Terpanjang)
        $oginoStartCycleDay = max(1, $shortestCycle - 18);
        $oginoEndCycleDay = max($oginoStartCycleDay, $longestCycle - 11);
        $oginoStartDate = $lmp->copy()->addDays($oginoStartCycleDay);
        $oginoEndDate = $lmp->copy()->addDays($oginoEndCycleDay);

        // 5. Evaluasi Hari Ini & Fase Siklus Berjalan
        $cycleDay = (int) $lmp->diffInDays($target, false) + 1;

        $phaseKey = 'unknown';
        $phaseName = 'Tidak Diketahui';
        $phaseDesc = '';
        $hormoneContext = '';
        $fertilityStatus = 'Rendah';

        if ($cycleDay < 1) {
            $phaseKey = 'pre_cycle';
            $phaseName = 'Sebelum Haid';
            $phaseDesc = 'Tanggal yang dipilih mendahului hari pertama haid terakhir yang tercatat.';
            $fertilityStatus = 'Rendah';
        } elseif ($cycleDay <= $duration) {
            $phaseKey = 'menstrual';
            $phaseName = 'Fase Haid (Menstruasi)';
            $phaseDesc = 'Tubuh sedang mengeluarkan darah haid. Cukupi istirahat, perbanyak minum air hangat, konsumsi makanan bergizi kaya zat besi, dan ganti pembalut secara berkala.';
            $hormoneContext = 'Kadar hormon sedang rendah, wajar jika tubuh terasa lebih cepat lelah.';
            $fertilityStatus = 'Sangat Rendah';
        } elseif ($target->lt($fertileStart)) {
            $phaseKey = 'follicular';
            $phaseName = 'Fase Pra-Subur (Masa Bersih)';
            $phaseDesc = 'Tubuh sedang pulih setelah haid dan menyiapkan sel telur baru. Energi, fokus, dan suasana hatimu biasanya terasa lebih segar dan bersemangat.';
            $hormoneContext = 'Hormon estrogen mulai meningkat secara bertahap.';
            $fertilityStatus = 'Rendah - Sedang';
        } elseif ($target->gte($fertileStart) && $target->lte($fertileEnd)) {
            $isPeak = $target->equalTo($ovulationDate);
            $phaseKey = $isPeak ? 'ovulation' : 'fertile';
            $phaseName = $isPeak ? 'Puncak Ovulasi' : 'Masa Subur';
            $phaseDesc = $isPeak
                ? 'Hari ketika sel telur matang dilepaskan tubuh. Peluang terjadinya pembuahan berada pada titik paling tinggi.'
                : 'Periode waktu di mana sel telur bersiap dilepaskan dan peluang pembuahan optimal.';
            $hormoneContext = 'Hormon pemicu ovulasi sedang berada pada puncaknya.';
            $fertilityStatus = $isPeak ? 'Maksimal / Puncak' : 'Tinggi';
        } elseif ($target->lt($nextPeriodStart)) {
            $phaseKey = 'luteal';
            $phaseName = 'Fase Pra-Haid (PMS)';
            $phaseDesc = 'Tubuh sedang bersiap menuju siklus haid berikutnya. Kamu mungkin mulai merasakan tanda pra-haid (PMS) seperti kram ringan, perubahan suasana hati, atau payudara lebih sensitif.';
            $hormoneContext = 'Hormon progesteron aktif bekerja sebelum kembali turun menjelang haid.';
            $fertilityStatus = 'Rendah';
        } else {
            $daysLate = (int) $nextPeriodStart->diffInDays($target, false);
            $phaseKey = 'late';
            $phaseName = 'Menanti Haid';
            $phaseDesc = 'Haidmu telah melewati estimasi tanggal awal (sekitar '.$daysLate.' hari). Keterlambatan beberapa hari adalah hal yang wajar karena stres pikiran, kelelahan fisik, atau pola tidur.';
            $hormoneContext = 'Tubuh sedang menyesuaikan waktu pelepasan dinding rahim.';
            $fertilityStatus = 'Rendah';
        }

        // Countdown hari menuju haid berikutnya & narasi sederhana
        $daysUntilNextPeriod = (int) $target->diffInDays($nextPeriodStart, false);

        if ($daysUntilNextPeriod > 0) {
            $countdownText = $daysUntilNextPeriod.' hari lagi';
            $summaryNarrative = 'Haid berikutnya diperkirakan tiba pada tanggal '.$nextPeriodStart->translatedFormat('d F Y').' (sekitar '.$daysUntilNextPeriod.' hari lagi).';
        } elseif ($daysUntilNextPeriod === 0) {
            $countdownText = 'Hari ini';
            $summaryNarrative = 'Hari ini adalah perkiraan hari pertama haidmu ('.$nextPeriodStart->translatedFormat('d F Y').'). Jangan lupa siapkan pembalut dan jaga tubuh tetap terhidrasi ya!';
        } else {
            $daysLate = abs($daysUntilNextPeriod);
            $countdownText = 'Terlambat '.$daysLate.' hari';
            $summaryNarrative = 'Haidmu telah melewati perkiraan tanggal '.$nextPeriodStart->translatedFormat('d F Y').' (sekitar '.$daysLate.' hari). Jangan cemas, keterlambatan beberapa hari wajar terjadi saat tubuh lelah atau stres.';
        }

        // 6. Proyeksi Siklus Mendatang (Multi-Cycle Projections)
        $projections = [];
        for ($i = 1; $i <= max(1, min(6, $projectionCount)); $i++) {
            $projStart = $lmp->copy()->addDays($i * $avgCycleLength);
            $projEnd = $projStart->copy()->addDays($duration - 1);
            $projOvu = $projStart->copy()->subDays(14);
            $projFertStart = $projOvu->copy()->subDays(5);
            $projFertEnd = $projOvu->copy()->addDays(1);

            $projections[] = [
                'cycle_number' => $i,
                'period_start' => $projStart->format('Y-m-d'),
                'period_end' => $projEnd->format('Y-m-d'),
                'period_start_formatted' => $projStart->translatedFormat('d F Y'),
                'ovulation_date' => $projOvu->format('Y-m-d'),
                'ovulation_formatted' => $projOvu->translatedFormat('d F Y'),
                'fertile_window_start' => $projFertStart->format('Y-m-d'),
                'fertile_window_end' => $projFertEnd->format('Y-m-d'),
                'fertile_window_formatted' => $projFertStart->format('d M').' - '.$projFertEnd->translatedFormat('d M Y'),
            ];
        }

        return [
            'success' => true,
            'summary_narrative' => $summaryNarrative,
            'countdown_text' => $countdownText,
            'input_data' => [
                'last_period_date' => $lmp->format('Y-m-d'),
                'last_period_date_formatted' => $lmp->translatedFormat('d F Y'),
                'cycle_length_used' => $avgCycleLength,
                'period_duration' => $duration,
                'cycle_history' => $validHistory,
                'target_date' => $target->format('Y-m-d'),
            ],
            'cycle_metrics' => [
                'average_cycle_length' => $avgCycleLength,
                'shortest_cycle' => $shortestCycle,
                'longest_cycle' => $longestCycle,
                'cycle_variation_days' => $cycleVariation,
                'is_regular' => $isRegular,
                'regularity_status' => $regularityStatus,
                'cycle_category' => $cycleCategory,
                'cycle_category_description' => $cycleCategoryDesc,
                'duration_category' => $durationCategory,
            ],
            'current_status' => [
                'cycle_day' => max(1, $cycleDay),
                'phase_key' => $phaseKey,
                'phase_name' => $phaseName,
                'phase_description' => $phaseDesc,
                'hormone_context' => $hormoneContext,
                'fertility_status' => $fertilityStatus,
                'days_until_next_period' => $daysUntilNextPeriod,
                'countdown_text' => $countdownText,
                'summary_narrative' => $summaryNarrative,
                'is_period_late' => ($cycleDay > $avgCycleLength),
                'days_late' => max(0, $cycleDay - $avgCycleLength),
            ],
            'predictions' => [
                // 1. Prediksi Haid Berikutnya
                'next_period' => [
                    'start_date' => $nextPeriodStart->format('Y-m-d'),
                    'end_date' => $nextPeriodEnd->format('Y-m-d'),
                    'start_formatted' => $nextPeriodStart->translatedFormat('d F Y'),
                    'end_formatted' => $nextPeriodEnd->translatedFormat('d F Y'),
                    'countdown_text' => $countdownText,
                    'narrative' => $summaryNarrative,
                    'formula_applied' => 'Dihitung dari haid terakhir ('.$lmp->format('d/m/Y').') + siklus '.$avgCycleLength.' hari',
                ],
                // 2. Estimasi Hari Ovulasi
                'ovulation' => [
                    'date' => $ovulationDate->format('Y-m-d'),
                    'formatted' => $ovulationDate->translatedFormat('d F Y'),
                    'narrative' => 'Pelepasan sel telur matang diperkirakan pada tanggal '.$ovulationDate->translatedFormat('d F Y').'.',
                    'formula_applied' => '14 hari sebelum perkiraan haid berikutnya ('.$nextPeriodStart->format('d/m/Y').')',
                ],
                // 3. Jendela Masa Subur Standar
                'fertile_window' => [
                    'start_date' => $fertileStart->format('Y-m-d'),
                    'end_date' => $fertileEnd->format('Y-m-d'),
                    'formatted' => $fertileStart->format('d M').' - '.$fertileEnd->translatedFormat('d M Y'),
                    'total_days' => 6,
                    'peak_date' => $ovulationDate->format('Y-m-d'),
                    'peak_formatted' => $ovulationDate->translatedFormat('d F Y'),
                    'narrative' => 'Masa subur berlangsung selama 6 hari ('.$fertileStart->format('d M').' s.d. '.$fertileEnd->translatedFormat('d M Y').') dengan peluang pembuahan paling tinggi.',
                    'formula_applied' => 'Rentang 6 hari (5 hari sebelum puncak ovulasi hingga 1 hari setelahnya)',
                ],
                // 4. Metode Rentang Siklus
                'ogino_knaus' => [
                    'applicable' => $hasHistory,
                    'cycle_day_start' => $oginoStartCycleDay,
                    'cycle_day_end' => $oginoEndCycleDay,
                    'start_date' => $oginoStartDate->format('Y-m-d'),
                    'end_date' => $oginoEndDate->format('Y-m-d'),
                    'formatted' => $oginoStartDate->format('d M').' - '.$oginoEndDate->translatedFormat('d M Y'),
                    'narrative' => 'Dengan variasi siklusmu, masa subur diperkirakan berlangsung antara '.$oginoStartDate->format('d M').' sampai '.$oginoEndDate->translatedFormat('d M Y').'.',
                    'formula_applied' => 'Disesuaikan dengan siklus terpendek ('.$shortestCycle.' hari) dan terpanjang ('.$longestCycle.' hari)',
                ],
            ],
            'upcoming_cycles' => $projections,
            'scientific_references' => self::SCIENTIFIC_REFERENCES,
            'clinical_disclaimer' => self::CLINICAL_DISCLAIMER,
        ];
    }

    /**
     * Hitung prediksi siklus untuk pengguna terdaftar berdasarkan catatan PeriodLog di database.
     *
     * @return array<string, mixed>|null
     */
    public function calculateForUser(User $user, ?Carbon $targetDate = null): ?array
    {
        if ($user->gender !== 'P') {
            return null;
        }

        $logs = PeriodLog::where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->take(12)
            ->get();

        if ($logs->isEmpty()) {
            return null;
        }

        $latestLog = $logs->first();
        $cycleHistory = $logs->pluck('cycle_length')->filter()->values()->all();

        return $this->calculate(
            lastPeriodDate: $latestLog->start_date,
            cycleLength: $latestLog->cycle_length ?? 28,
            periodDuration: $latestLog->period_duration ?? 5,
            cycleHistory: $cycleHistory,
            targetDate: $targetDate
        );
    }

    /**
     * Hitung Skor WaLIDD (Working ability, Location, Intensity, Days of pain)
     * Berdasarkan instrumen diagnostik multidimensional dismenore (Teherán et al., 2018; Rejeki, 2020)
     *
     * @param  int|null  $workingAbility  Bobot W: Kemampuan kerja/aktivitas (0: Normal, 1: Ringan, 2: Sedang, 3: Bed rest)
     * @param  array|null  $locations  Array lokasi nyeri: perut_bawah, pinggang, paha_dalam
     * @param  int|null  $nrsScore  Intensitas nyeri NRS (0 - 10)
     * @param  int|null  $painDays  Durasi hari nyeri (0, 1-2, 3-4, >= 5 hari)
     * @return array<string, mixed>
     */
    public function calculateWaLIDD(
        ?int $workingAbility = 0,
        ?array $locations = [],
        ?int $nrsScore = 0,
        ?int $painDays = 0
    ): array {
        // 1. Bobot W (Working Ability): 0-3
        $w = max(0, min(3, (int) $workingAbility));

        // 2. Bobot L (Location): hitung lokasi valid
        $validLocs = ['perut_bawah', 'pinggang', 'paha_dalam'];
        $selectedLocs = [];
        if (is_array($locations)) {
            foreach ($locations as $loc) {
                if (in_array($loc, $validLocs) && ! in_array($loc, $selectedLocs)) {
                    $selectedLocs[] = $loc;
                }
            }
        }
        $locCount = count($selectedLocs);
        $l = max(0, min(3, $locCount));

        // 3. Bobot I (Intensity - Konversi NRS 0-10)
        $nrs = max(0, min(10, (int) $nrsScore));
        if ($nrs === 0) {
            $i = 0;
            $intensityCategory = 'Tidak Ada Nyeri';
        } elseif ($nrs <= 3) {
            $i = 1;
            $intensityCategory = 'Nyeri Ringan';
        } elseif ($nrs <= 7) {
            $i = 2;
            $intensityCategory = 'Nyeri Sedang';
        } else {
            $i = 3;
            $intensityCategory = 'Nyeri Berat';
        }

        // 4. Bobot D (Days of Pain): jumlah hari nyeri
        $days = max(0, (int) $painDays);
        if ($days === 0) {
            $d = 0;
        } elseif ($days <= 2) {
            $d = 1;
        } elseif ($days <= 4) {
            $d = 2;
        } else {
            $d = 3;
        }

        // Total Skor WaLIDD: W + L + I + D (0 - 12)
        $totalScore = $w + $l + $i + $d;

        // Kategori & Interpretasi Klinis (Teherán et al., 2018)
        if ($totalScore === 0) {
            $category = 'Tidak Dismenore';
            $interpretation = 'Kondisi fisiologis bebas nyeri kram menstruasi. Pertahankan gaya hidup sehat, hidrasi air putih, dan gizi seimbang.';
            $actionAdvice = 'Tidak memerlukan penanganan khusus. Pertahankan pola hidup sehat.';
            $color = 'success';
        } elseif ($totalScore <= 4) {
            $category = 'Dismenore Ringan';
            $interpretation = 'Kram nyeri haid tergolong ringan dan tidak membatasi aktivitas harian secara bermakna.';
            $actionAdvice = 'Lakukan peregangan tubuh ringan, kompres hangat pada perut bawah, dan minum air putih hangat untuk meredakan kram.';
            $color = 'warning';
        } elseif ($totalScore <= 7) {
            $category = 'Dismenore Sedang';
            $interpretation = 'Nyeri kram haid mulai mengganggu konsentrasi dan menurunkan efisiensi aktivitas harian.';
            $actionAdvice = 'Istirahat yang cukup, aplikasikan kompres hangat secara teratur, kurangi konsumsi kafein berlebih, dan konsultasikan dengan tenaga medis jika keluhan mengganggu kegiatan perkuliahan.';
            $color = 'orange';
        } else {
            $category = 'Dismenore Berat';
            $interpretation = 'Nyeri kram haid berat multidimensional yang sangat membatasi mobilitas hingga memerlukan tirah baring (bed rest).';
            $actionAdvice = 'Sangat dianjurkan untuk berkonsultasi langsung dengan dokter atau dokter spesialis obstetri & ginekologi (Sp.OG) untuk pemeriksaan lebih mendalam.';
            $color = 'error';
        }

        return [
            'working_ability_score' => $w,
            'location_score' => $l,
            'intensity_score' => $i,
            'pain_days_score' => $d,
            'total_score' => $totalScore,
            'max_score' => 12,
            'category' => $category,
            'nrs_score' => $nrs,
            'nrs_category' => $intensityCategory,
            'pain_days' => $days,
            'selected_locations' => $selectedLocs,
            'interpretation' => $interpretation,
            'action_advice' => $actionAdvice,
            'color' => $color,
            'formula' => "W ({$w}) + L ({$l}) + I ({$i}) + D ({$d}) = {$totalScore}/12",
        ];
    }

    /**
     * Hitung Skor Visual PBAC (Pictorial Blood Loss Assessment Chart - Higham et al.)
     * Dan klasifikasi volume darah menstruasi berdasarkan standar klinis FIGO & Kemenkes RI.
     *
     * @param  int  $padsLight  Pembalut bernoda sedikit (<= 1/3 terisi): 1 poin
     * @param  int  $padsMedium  Pembalut bernoda sedang (~ 1/2 terisi): 5 poin
     * @param  int  $padsHeavy  Pembalut basah jenuh (terisi penuh): 20 poin
     * @param  int  $clotsSmall  Gumpalan darah kecil (< 2,5 cm): 1 poin
     * @param  int  $clotsLarge  Gumpalan darah besar (>= 2,5 cm): 5 poin
     * @return array<string, mixed>
     */
    public function calculatePBAC(
        int $padsLight = 0,
        int $padsMedium = 0,
        int $padsHeavy = 0,
        int $clotsSmall = 0,
        int $clotsLarge = 0
    ): array {
        $pLight = max(0, $padsLight);
        $pMedium = max(0, $padsMedium);
        $pHeavy = max(0, $padsHeavy);
        $cSmall = max(0, $clotsSmall);
        $cLarge = max(0, $clotsLarge);

        $padsLightScore = $pLight * 1;
        $padsMediumScore = $pMedium * 5;
        $padsHeavyScore = $pHeavy * 20;

        $clotsSmallScore = $cSmall * 1;
        $clotsLargeScore = $cLarge * 5;

        $padsTotalScore = $padsLightScore + $padsMediumScore + $padsHeavyScore;
        $clotsTotalScore = $clotsSmallScore + $clotsLargeScore;
        $totalScore = $padsTotalScore + $clotsTotalScore;

        if ($totalScore >= 100) {
            $code = 'menoragia';
            $category = 'Menoragia / HMB (Heavy Menstrual Bleeding)';
            $isHmb = true;
            $estimatedVolume = '> 80 ml (Perdarahan Berlebih)';
            $interpretation = 'Skor PBAC ≥ 100 poin mengindikasikan volume darah haid berlebih / menoragia (≥ 80 ml) dengan sensitivitas dan spesifisitas klinis > 80%. Kondisi ini berisiko memicu anemia defisiensi besi.';
            $actionAdvice = 'Waspadai gejala lemas, cepat lelah, atau pusing. Dianjurkan memeriksa kadar hemoglobin (Hb) dan berkonsultasi dengan dokter spesialis obstetri & ginekologi (Sp.OG).';
            $color = 'error';
        } elseif ($totalScore <= 10 && ($pMedium === 0 && $pHeavy === 0 && $cLarge === 0)) {
            $code = 'hipomenore';
            $category = 'Hipomenore (Sangat Sedikit / Bercak)';
            $isHmb = false;
            $estimatedVolume = '< 5 – 10 ml per siklus';
            $interpretation = 'Volume pengeluaran darah sangat minim berupa bercak flek (spotting) dan pembalut jarang terisi penuh.';
            $actionAdvice = 'Umumnya fisiologis normal bila terjadi sesekali atau di hari awal/akhir haid. Jika berlangsung terus-menerus, evaluasi pola nutrisi dan keseimbangan hormonal.';
            $color = 'warning';
        } else {
            $code = 'normal';
            $category = 'Normal (Eumenore)';
            $isHmb = false;
            $estimatedVolume = '30 – 40 ml (Rentang Fisiologis 5 – 80 ml)';
            $interpretation = 'Volume pengeluaran darah berada dalam rentang fisiologis normal yang sehat (< 80 ml per siklus).';
            $actionAdvice = 'Pertahankan kebiasaan mengganti pembalut teratur 3–5 kali sehari dan menjaga higienitas organ reproduksi.';
            $color = 'success';
        }

        return [
            'total_score' => $totalScore,
            'is_hmb' => $isHmb,
            'code' => $code,
            'category' => $category,
            'estimated_volume' => $estimatedVolume,
            'interpretation' => $interpretation,
            'action_advice' => $actionAdvice,
            'color' => $color,
            'breakdown' => [
                'pads' => [
                    'light' => ['count' => $pLight, 'points_per_item' => 1, 'score' => $padsLightScore],
                    'medium' => ['count' => $pMedium, 'points_per_item' => 5, 'score' => $padsMediumScore],
                    'heavy' => ['count' => $pHeavy, 'points_per_item' => 20, 'score' => $padsHeavyScore],
                    'total_score' => $padsTotalScore,
                ],
                'clots' => [
                    'small' => ['count' => $cSmall, 'points_per_item' => 1, 'score' => $clotsSmallScore],
                    'large' => ['count' => $cLarge, 'points_per_item' => 5, 'score' => $clotsLargeScore],
                    'total_score' => $clotsTotalScore,
                ],
            ],
            'formula' => "Pembalut ({$padsTotalScore}) + Gumpalan ({$clotsTotalScore}) = {$totalScore} Poin PBAC",
        ];
    }
}
