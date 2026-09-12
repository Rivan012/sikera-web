<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BmiLog;
use App\Models\PeriodLog;
use App\Services\MenstrualCycleCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SelfCareController extends Controller
{
    /**
     * Ringkasan & Prediksi Siklus Haid + IMT (Sesuai Rumus Ilmiah ACOG, WHO, Wilcox 1995)
     */
    public function summary(Request $request, MenstrualCycleCalculatorService $calculator)
    {
        $user = $request->user();
        $isFemale = ($user->gender === 'P');

        $latestPeriod = null;
        $scientificPrediction = null;

        if ($isFemale) {
            $period = PeriodLog::where('user_id', $user->id)->latest('start_date')->first();
            if ($period) {
                $latestPeriod = [
                    'id' => $period->id,
                    'menarche_age' => $period->menarche_age,
                    'start_date' => $period->start_date->format('Y-m-d'),
                    'end_date' => $period->end_date ? $period->end_date->format('Y-m-d') : null,
                    'cycle_length' => $period->cycle_length,
                    'period_duration' => $period->period_duration,
                    'is_regular' => $period->is_regular,
                    'flow_level' => $period->flow_level,
                    'flow_color' => $period->flow_color,
                    'blood_consistency' => $period->blood_consistency,
                    'nrs_pain_score' => $period->nrs_pain_score,
                    'has_dysmenorrhea' => $period->has_dysmenorrhea,
                    'walidd' => [
                        'working_ability' => $period->walidd_working_ability,
                        'locations' => $period->walidd_locations,
                        'location_score' => $period->walidd_location_score,
                        'intensity_score' => $period->walidd_intensity_score,
                        'pain_days' => $period->walidd_pain_days,
                        'pain_days_score' => $period->walidd_pain_days_score,
                        'total_score' => $period->walidd_total_score,
                        'category' => $period->walidd_category,
                        'interpretation' => $period->walidd_interpretation,
                    ],
                    'symptoms' => $period->symptoms ?? [],
                    'notes' => $period->notes,
                ];

                $scientificPrediction = $calculator->calculateForUser($user);
            }
        }

        $latestBmi = null;
        $bmi = BmiLog::where('user_id', $user->id)->latest()->first();
        if ($bmi) {
            $latestBmi = [
                'id' => $bmi->id,
                'weight_kg' => $bmi->weight_kg,
                'height_cm' => $bmi->height_cm,
                'bmi_value' => $bmi->bmi_value,
                'category' => $bmi->category,
                'advice' => $bmi->advice,
                'created_at' => $bmi->created_at->format('Y-m-d H:i'),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Ringkasan data self-care berhasil dimuat.',
            'data' => [
                'is_female' => $isFemale,
                'period_tracker' => [
                    'available' => $isFemale,
                    'latest_period' => $latestPeriod,
                    'predictions' => $scientificPrediction ? [
                        'next_period_date' => $scientificPrediction['predictions']['next_period']['start_date'],
                        'ovulation_date' => $scientificPrediction['predictions']['ovulation']['date'],
                        'fertile_window_start' => $scientificPrediction['predictions']['fertile_window']['start_date'],
                        'fertile_window_end' => $scientificPrediction['predictions']['fertile_window']['end_date'],
                        'countdown_text' => $scientificPrediction['countdown_text'] ?? null,
                        'summary_narrative' => $scientificPrediction['summary_narrative'] ?? null,
                        'details' => $scientificPrediction['predictions'],
                        'current_status' => $scientificPrediction['current_status'],
                        'cycle_metrics' => $scientificPrediction['cycle_metrics'],
                        'scientific_references' => $scientificPrediction['scientific_references'] ?? [],
                        'clinical_disclaimer' => $scientificPrediction['clinical_disclaimer'] ?? null,
                    ] : null,
                ],
                'bmi_tracker' => [
                    'latest_bmi' => $latestBmi,
                ],
            ],
        ]);
    }

    /**
     * Hitung & Prediksi Siklus Menstruasi Interaktif (Rumus Ilmiah ACOG, WHO, Wilcox 1995, Ogino-Knaus)
     */
    public function calculatePeriod(Request $request, MenstrualCycleCalculatorService $calculator)
    {
        $validator = Validator::make($request->all(), [
            'last_period_date' => 'required|date',
            'cycle_length' => 'nullable|integer|min:15|max:60',
            'period_duration' => 'nullable|integer|min:1|max:14',
            'cycle_history' => 'nullable|array',
            'cycle_history.*' => 'integer|min:15|max:60',
            'target_date' => 'nullable|date',
            'projection_count' => 'nullable|integer|min:1|max:6',
        ], [
            'last_period_date.required' => 'Hari pertama haid terakhir (LMP) wajib diisi.',
            'last_period_date.date' => 'Format tanggal haid terakhir tidak valid.',
            'cycle_length.min' => 'Panjang siklus minimal 15 hari.',
            'cycle_length.max' => 'Panjang siklus maksimal 60 hari.',
            'period_duration.min' => 'Durasi haid minimal 1 hari.',
            'period_duration.max' => 'Durasi haid maksimal 14 hari.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi kalkulator siklus haid gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $targetDate = ! empty($validated['target_date']) ? Carbon::parse($validated['target_date']) : null;
        $projectionCount = ! empty($validated['projection_count']) ? (int) $validated['projection_count'] : 3;

        $result = $calculator->calculate(
            lastPeriodDate: $validated['last_period_date'],
            cycleLength: $validated['cycle_length'] ?? 28,
            periodDuration: $validated['period_duration'] ?? 5,
            cycleHistory: $validated['cycle_history'] ?? null,
            targetDate: $targetDate,
            projectionCount: $projectionCount
        );

        return response()->json([
            'success' => true,
            'message' => 'Perhitungan dan prediksi siklus menstruasi berhasil dihitung berdasarkan formula klinis.',
            'data' => $result,
        ]);
    }

    /**
     * Prediksi Siklus Menstruasi Pengguna Terdaftar (Khusus Perempuan)
     */
    public function periodPrediction(Request $request, MenstrualCycleCalculatorService $calculator)
    {
        $user = $request->user();

        if ($user->gender === 'L') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur prediksi siklus menstruasi hanya diperuntukkan bagi perempuan.',
            ], 403);
        }

        $prediction = $calculator->calculateForUser($user);

        if (! $prediction) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada catatan hari pertama haid terakhir (LMP). Silakan catat haid Anda terlebih dahulu.',
                'data' => null,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prediksi siklus menstruasi dan masa subur pengguna berhasil dimuat.',
            'data' => $prediction,
        ]);
    }

    /**
     * Simpan Catatan Siklus Haid (Khusus Perempuan)
     */
    public function storePeriod(Request $request, MenstrualCycleCalculatorService $calculator)
    {
        $user = $request->user();

        if ($user->gender === 'L') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur pencatatan siklus menstruasi hanya diperuntukkan bagi perempuan.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'menarche_age' => 'nullable|integer|min:8|max:25',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'cycle_length' => 'required|integer|min:15|max:60',
            'period_duration' => 'nullable|integer|min:1|max:14',
            'is_regular' => 'nullable|boolean',
            'flow_level' => 'required|in:ringan,sedang,deras,sangat_deras',
            'flow_color' => 'required|string',
            'blood_consistency' => 'nullable|string|max:50',
            'volume_category' => 'nullable|string|in:hipomenore,normal,menoragia',
            'pbac_pads_light' => 'nullable|integer|min:0|max:100',
            'pbac_pads_medium' => 'nullable|integer|min:0|max:100',
            'pbac_pads_heavy' => 'nullable|integer|min:0|max:100',
            'pbac_clots_small' => 'nullable|integer|min:0|max:100',
            'pbac_clots_large' => 'nullable|integer|min:0|max:100',
            'nrs_pain_score' => 'required|integer|min:0|max:10',
            'has_dysmenorrhea' => 'nullable|boolean',
            'walidd_working_ability' => 'nullable|integer|min:0|max:3',
            'walidd_locations' => 'nullable|array',
            'walidd_locations.*' => 'in:perut_bawah,pinggang,paha_dalam',
            'walidd_pain_days' => 'nullable|integer|min:0|max:30',
            'symptoms' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data siklus haid tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['is_regular'] = $request->has('is_regular') ? (bool) $request->input('is_regular') : true;

        // Evaluasi Volume Darah PBAC & Klasifikasi FIGO
        if ($request->filled('pbac_pads_light') || $request->filled('pbac_pads_medium') || $request->filled('pbac_pads_heavy') || $request->filled('pbac_clots_small') || $request->filled('pbac_clots_large')) {
            $pbac = $calculator->calculatePBAC(
                padsLight: (int) $request->input('pbac_pads_light', 0),
                padsMedium: (int) $request->input('pbac_pads_medium', 0),
                padsHeavy: (int) $request->input('pbac_pads_heavy', 0),
                clotsSmall: (int) $request->input('pbac_clots_small', 0),
                clotsLarge: (int) $request->input('pbac_clots_large', 0)
            );
            $data['pbac_score'] = $pbac['total_score'];
            $data['volume_category'] = $pbac['code'];
            $data['pbac_details'] = $pbac;
        } else {
            $data['pbac_score'] = null;
            $data['pbac_details'] = null;
            if (empty($data['volume_category'])) {
                $data['volume_category'] = match ($data['flow_level']) {
                    'ringan' => 'hipomenore',
                    'sangat_deras' => 'menoragia',
                    default => 'normal',
                };
            }
        }

        // Hitung durasi otomatis jika end_date tersedia
        if (! empty($data['start_date']) && ! empty($data['end_date'])) {
            $start = Carbon::parse($data['start_date']);
            $end = Carbon::parse($data['end_date']);
            $data['period_duration'] = max(1, min(14, $start->diffInDays($end) + 1));
        } elseif (empty($data['period_duration'])) {
            $data['period_duration'] = 5;
        }

        // Evaluasi Dismenore dengan Instrumen WaLIDD Score
        $hasDysmenorrhea = ! empty($data['has_dysmenorrhea']) || ($data['nrs_pain_score'] > 0);
        $data['has_dysmenorrhea'] = $hasDysmenorrhea;

        if ($hasDysmenorrhea) {
            $walidd = $calculator->calculateWaLIDD(
                workingAbility: $request->input('walidd_working_ability', 0),
                locations: $request->input('walidd_locations', []),
                nrsScore: (int) $data['nrs_pain_score'],
                painDays: (int) $request->input('walidd_pain_days', 0)
            );

            $data['walidd_working_ability'] = $walidd['working_ability_score'];
            $data['walidd_locations'] = $walidd['selected_locations'];
            $data['walidd_location_score'] = $walidd['location_score'];
            $data['walidd_intensity_score'] = $walidd['intensity_score'];
            $data['walidd_pain_days'] = $walidd['pain_days'];
            $data['walidd_pain_days_score'] = $walidd['pain_days_score'];
            $data['walidd_total_score'] = $walidd['total_score'];
            $data['walidd_category'] = $walidd['category'];
            $data['walidd_interpretation'] = $walidd['interpretation'];
        } else {
            $data['walidd_working_ability'] = 0;
            $data['walidd_locations'] = [];
            $data['walidd_location_score'] = 0;
            $data['walidd_intensity_score'] = 0;
            $data['walidd_pain_days'] = 0;
            $data['walidd_pain_days_score'] = 0;
            $data['walidd_total_score'] = 0;
            $data['walidd_category'] = 'Tidak Dismenore';
            $data['walidd_interpretation'] = 'Kondisi fisiologis bebas nyeri kram menstruasi.';
        }

        $data['user_id'] = $user->id;

        $log = PeriodLog::create($data);

        // Update usia menarche profil jika belum ada atau diperbarui
        if (! empty($data['menarche_age']) && $user->menarche_age !== (int) $data['menarche_age']) {
            $user->update(['menarche_age' => (int) $data['menarche_age']]);
        }

        $user->increment('points', 10);

        $updatedPrediction = $calculator->calculateForUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Catatan siklus haid dan evaluasi dismenore berhasil disimpan (+10 poin).',
            'data' => [
                'period_log' => $log,
                'prediction' => $updatedPrediction,
                'points_earned' => 10,
            ],
        ], 201);
    }

    /**
     * Hitung Skor Dismenore WaLIDD secara Mandiri / Simulasi
     */
    public function calculateWalidd(Request $request, MenstrualCycleCalculatorService $calculator)
    {
        $validator = Validator::make($request->all(), [
            'working_ability' => 'nullable|integer|min:0|max:3',
            'locations' => 'nullable|array',
            'locations.*' => 'in:perut_bawah,pinggang,paha_dalam',
            'nrs_score' => 'required|integer|min:0|max:10',
            'pain_days' => 'nullable|integer|min:0|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data pengukuran dismenore gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $result = $calculator->calculateWaLIDD(
            workingAbility: $validated['working_ability'] ?? 0,
            locations: $validated['locations'] ?? [],
            nrsScore: $validated['nrs_score'],
            painDays: $validated['pain_days'] ?? 0
        );

        return response()->json([
            'success' => true,
            'message' => 'Perhitungan skor WaLIDD dan kategori dismenore berhasil dihitung.',
            'data' => $result,
        ]);
    }

    /**
     * Hitung Skor Visual PBAC (Pictorial Blood Loss Assessment Chart - Higham et al.)
     * Klasifikasi Volume Darah Menstruasi (FIGO & Kemenkes RI)
     */
    public function calculatePbac(Request $request, MenstrualCycleCalculatorService $calculator)
    {
        $validator = Validator::make($request->all(), [
            'pads_light' => 'nullable|integer|min:0|max:100',
            'pads_medium' => 'nullable|integer|min:0|max:100',
            'pads_heavy' => 'nullable|integer|min:0|max:100',
            'clots_small' => 'nullable|integer|min:0|max:100',
            'clots_large' => 'nullable|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data skoring PBAC gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $result = $calculator->calculatePBAC(
            padsLight: (int) ($validated['pads_light'] ?? 0),
            padsMedium: (int) ($validated['pads_medium'] ?? 0),
            padsHeavy: (int) ($validated['pads_heavy'] ?? 0),
            clotsSmall: (int) ($validated['clots_small'] ?? 0),
            clotsLarge: (int) ($validated['clots_large'] ?? 0)
        );

        return response()->json([
            'success' => true,
            'message' => 'Perhitungan skor PBAC dan klasifikasi volume darah berhasil dihitung.',
            'data' => $result,
        ]);
    }

    /**
     * Riwayat Siklus Haid
     */
    public function periodHistory(Request $request)
    {
        $user = $request->user();

        if ($user->gender === 'L') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur riwayat siklus menstruasi hanya diperuntukkan bagi perempuan.',
            ], 403);
        }

        $logs = PeriodLog::where('user_id', $user->id)->latest('start_date')->take(20)->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat siklus haid berhasil dimuat.',
            'data' => [
                'period_logs' => $logs,
            ],
        ]);
    }

    /**
     * Hitung & Simpan IMT
     */
    public function storeBmi(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'weight_kg' => 'required|numeric|min:20|max:250',
            'height_cm' => 'required|numeric|min:80|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter berat atau tinggi badan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $weight = (float) $request->weight_kg;
        $heightM = (float) $request->height_cm / 100;
        $bmi = round($weight / ($heightM * $heightM), 1);

        $category = 'Normal';
        $advice = 'Status gizi seimbang. Pertahankan pola makan bergizi dan hidrasi sehat untuk stabilitas hormon reproduksi.';

        if ($bmi < 18.5) {
            $category = 'Kekurangan Berat Badan (Risiko KEK)';
            $advice = 'Indeks massa tubuh rendah dapat memicu penurunan hormon reproduksi dan kelelahan kronis. Tingkatkan asupan kalori bernutrisi dan protein hewani.';
        } elseif ($bmi >= 23 && $bmi <= 24.9) {
            $category = 'Kelebihan Berat Badan (Overweight)';
            $advice = 'Perhatikan keseimbangan aktivitas fisik dan batasi makanan dengan indeks glikemik tinggi.';
        } elseif ($bmi >= 25) {
            $category = 'Obesitas';
            $advice = 'Jaringan lemak berlebih dapat mempengaruhi resistensi insulin dan stabilitas hormon reproduksi. Disarankan konsultasi pola diet dan olahraga rutin.';
        }

        $log = BmiLog::create([
            'user_id' => $user->id,
            'weight_kg' => $weight,
            'height_cm' => $request->height_cm,
            'bmi_value' => $bmi,
            'category' => $category,
            'advice' => $advice,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kalkulasi IMT berhasil dihitung & disimpan.',
            'data' => [
                'bmi_log' => $log,
            ],
        ], 201);
    }

    /**
     * Riwayat Pengukuran IMT
     */
    public function bmiHistory(Request $request)
    {
        $user = $request->user();
        $logs = BmiLog::where('user_id', $user->id)->latest()->take(20)->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pengukuran IMT berhasil dimuat.',
            'data' => [
                'bmi_logs' => $logs,
            ],
        ]);
    }

    /**
     * Panduan Medis Karakteristik Warna Darah Haid
     */
    public function bloodGuide()
    {
        $guides = [
            [
                'color_name' => 'Merah Terang',
                'hex_color' => '#ef4444',
                'status' => 'Normal / Fisiologis',
                'description' => 'Darah segar dengan aliran lancar, biasanya muncul di hari ke-1 hingga ke-3 siklus haid. Menandakan peluruhan endometrium yang sehat dan sirkulasi darah yang baik.',
            ],
            [
                'color_name' => 'Cokelat Gelap / Kehitaman',
                'hex_color' => '#5C2D06',
                'status' => 'Normal / Oksidasi',
                'description' => 'Darah sisa yang mengalami proses oksidasi saat keluar perlahan dari rahim. Sering muncul di awal atau akhir siklus.',
            ],
            [
                'color_name' => 'Merah Muda Pucat',
                'hex_color' => '#f472b6',
                'status' => 'Perlu Perhatian',
                'description' => 'Dapat terjadi akibat kadar hormon estrogen yang relatif rendah atau bercak flek (spotting) di luar siklus haid.',
            ],
            [
                'color_name' => 'Hitam Pekat + Gumpalan + Bau Menyengat',
                'hex_color' => '#111827',
                'status' => 'Segera Periksa ke Dokter',
                'description' => 'Jika disertai gumpalan besar, aroma busuk menyengat, atau demam, segera periksakan diri ke fasilitas kesehatan.',
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Panduan warna darah haid berhasil dimuat.',
            'data' => [
                'guides' => $guides,
            ],
        ]);
    }

    /**
     * 4 Panduan Utama Higienitas Genitalia
     */
    public function hygieneGuide()
    {
        $tips = [
            [
                'step' => 1,
                'title' => 'Basuh dari Depan ke Belakang',
                'description' => 'Selalu membasuh dari arah vagina ke anus untuk mencegah perpindahan bakteri E. Coli yang menyebabkan ISK.',
            ],
            [
                'step' => 2,
                'title' => 'Ganti Pembalut Secara Teratur',
                'description' => 'Ganti pembalut minimal setiap 3-4 jam sekali saat aliran deras guna mencegah perkembangbiakan bakteri dan jamur.',
            ],
            [
                'step' => 3,
                'title' => 'Pilih Pakaian Dalam yang Tepat',
                'description' => 'Gunakan pakaian dalam berbahan katun berpori yang menyerap keringat dan tidak ketat.',
            ],
            [
                'step' => 4,
                'title' => 'Hindari Sabun Pewangi & Douching',
                'description' => 'Vagina memiliki mekanisme pembersihan mandiri oleh Lactobacillus. Hindari sabun antiseptik pewangi atau douching.',
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Panduan higienitas genitalia berhasil dimuat.',
            'data' => [
                'tips' => $tips,
            ],
        ]);
    }
}
