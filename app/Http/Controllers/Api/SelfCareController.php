<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PeriodLog;
use App\Models\BmiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SelfCareController extends Controller
{
    /**
     * Ringkasan & Prediksi Siklus Haid + IMT
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        $isFemale = ($user->gender === 'P');

        $latestPeriod = null;
        $predictions = null;

        if ($isFemale) {
            $period = PeriodLog::where('user_id', $user->id)->latest()->first();
            if ($period) {
                $cycleLen = $period->cycle_length ?? 28;
                $start = Carbon::parse($period->start_date);
                $nextPeriodDate = $start->copy()->addDays($cycleLen);
                $ovulationDate = $nextPeriodDate->copy()->subDays(14);
                $fertileStart = $ovulationDate->copy()->subDays(4);
                $fertileEnd = $ovulationDate->copy()->addDays(1);

                $latestPeriod = [
                    'id' => $period->id,
                    'start_date' => $period->start_date->format('Y-m-d'),
                    'end_date' => $period->end_date ? $period->end_date->format('Y-m-d') : null,
                    'cycle_length' => $period->cycle_length,
                    'period_duration' => $period->period_duration,
                    'flow_level' => $period->flow_level,
                    'flow_color' => $period->flow_color,
                    'nrs_pain_score' => $period->nrs_pain_score,
                    'symptoms' => $period->symptoms ?? [],
                    'notes' => $period->notes,
                ];

                $predictions = [
                    'next_period_date' => $nextPeriodDate->format('Y-m-d'),
                    'ovulation_date' => $ovulationDate->format('Y-m-d'),
                    'fertile_window_start' => $fertileStart->format('Y-m-d'),
                    'fertile_window_end' => $fertileEnd->format('Y-m-d'),
                ];
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
                    'predictions' => $predictions,
                ],
                'bmi_tracker' => [
                    'latest_bmi' => $latestBmi,
                ],
            ],
        ]);
    }

    /**
     * Simpan Catatan Siklus Haid (Khusus Perempuan)
     */
    public function storePeriod(Request $request)
    {
        $user = $request->user();

        if ($user->gender === 'L') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur pencatatan siklus menstruasi hanya diperuntukkan bagi perempuan.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'cycle_length' => 'required|integer|min:15|max:60',
            'period_duration' => 'required|integer|min:1|max:14',
            'flow_level' => 'required|in:ringan,sedang,deras,sangat_deras',
            'flow_color' => 'required|string',
            'nrs_pain_score' => 'required|integer|min:0|max:10',
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
        $data['user_id'] = $user->id;

        $log = PeriodLog::create($data);
        $user->increment('points', 10);

        return response()->json([
            'success' => true,
            'message' => 'Catatan siklus haid berhasil disimpan (+10 poin).',
            'data' => [
                'period_log' => $log,
                'points_earned' => 10,
            ],
        ], 201);
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

        $weight = (float)$request->weight_kg;
        $heightM = (float)$request->height_cm / 100;
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
