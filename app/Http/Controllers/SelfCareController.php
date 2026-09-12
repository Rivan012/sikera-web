<?php

namespace App\Http\Controllers;

use App\Models\BmiLog;
use App\Models\PeriodLog;
use App\Services\MenstrualCycleCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SelfCareController extends Controller
{
    public function index(MenstrualCycleCalculatorService $calculator)
    {
        $user = Auth::user();
        $isFemale = ($user->gender === 'P');

        $recentPeriods = $isFemale ? PeriodLog::where('user_id', $user->id)->latest('start_date')->take(6)->get() : collect();
        $latestPeriod = $recentPeriods->first();
        $recentBmi = BmiLog::where('user_id', $user->id)->latest()->take(5)->get();
        $latestBmi = $recentBmi->first();

        // Prediksi siklus haid dan masa subur ilmiah (ACOG, WHO, Wilcox 1995, Ogino-Knaus)
        $prediction = null;
        $nextPeriodDate = null;
        $fertileWindowStart = null;
        $fertileWindowEnd = null;
        $ovulationDate = null;

        if ($isFemale && $latestPeriod) {
            $prediction = $calculator->calculateForUser($user);
            if ($prediction) {
                $nextPeriodDate = Carbon::parse($prediction['predictions']['next_period']['start_date']);
                $ovulationDate = Carbon::parse($prediction['predictions']['ovulation']['date']);
                $fertileWindowStart = Carbon::parse($prediction['predictions']['fertile_window']['start_date']);
                $fertileWindowEnd = Carbon::parse($prediction['predictions']['fertile_window']['end_date']);
            }
        }

        return view('selfcare.index', compact(
            'user',
            'isFemale',
            'recentPeriods',
            'latestPeriod',
            'recentBmi',
            'latestBmi',
            'prediction',
            'nextPeriodDate',
            'fertileWindowStart',
            'fertileWindowEnd',
            'ovulationDate'
        ));
    }

    public function storePeriod(Request $request, MenstrualCycleCalculatorService $calculator)
    {
        $user = Auth::user();

        // Validasi gender: hanya perempuan yang dapat mencatat siklus haid
        if ($user->gender === 'L') {
            return redirect()->route('selfcare.index')
                ->with('warning', 'Fitur pencatatan siklus menstruasi hanya diperuntukkan bagi perempuan.');
        }

        $validated = $request->validate([
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

        $validated['is_regular'] = $request->has('is_regular') ? (bool) $request->input('is_regular') : true;

        // Evaluasi Volume Darah PBAC & Klasifikasi FIGO
        if ($request->filled('pbac_pads_light') || $request->filled('pbac_pads_medium') || $request->filled('pbac_pads_heavy') || $request->filled('pbac_clots_small') || $request->filled('pbac_clots_large')) {
            $pbac = $calculator->calculatePBAC(
                padsLight: (int) $request->input('pbac_pads_light', 0),
                padsMedium: (int) $request->input('pbac_pads_medium', 0),
                padsHeavy: (int) $request->input('pbac_pads_heavy', 0),
                clotsSmall: (int) $request->input('pbac_clots_small', 0),
                clotsLarge: (int) $request->input('pbac_clots_large', 0)
            );
            $validated['pbac_score'] = $pbac['total_score'];
            $validated['volume_category'] = $pbac['code'];
            $validated['pbac_details'] = $pbac;
        } else {
            $validated['pbac_score'] = null;
            $validated['pbac_details'] = null;
            if (empty($validated['volume_category'])) {
                $validated['volume_category'] = match ($validated['flow_level']) {
                    'ringan' => 'hipomenore',
                    'sangat_deras' => 'menoragia',
                    default => 'normal',
                };
            }
        }

        // Hitung durasi otomatis jika end_date tersedia
        if (! empty($validated['start_date']) && ! empty($validated['end_date'])) {
            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);
            $validated['period_duration'] = max(1, min(14, $start->diffInDays($end) + 1));
        } elseif (empty($validated['period_duration'])) {
            $validated['period_duration'] = 5;
        }

        // Evaluasi Dismenore dengan Instrumen WaLIDD Score
        $hasDysmenorrhea = ! empty($validated['has_dysmenorrhea']) || ($validated['nrs_pain_score'] > 0);
        $validated['has_dysmenorrhea'] = $hasDysmenorrhea;

        if ($hasDysmenorrhea) {
            $walidd = $calculator->calculateWaLIDD(
                workingAbility: $request->input('walidd_working_ability', 0),
                locations: $request->input('walidd_locations', []),
                nrsScore: (int) $validated['nrs_pain_score'],
                painDays: (int) $request->input('walidd_pain_days', 0)
            );

            $validated['walidd_working_ability'] = $walidd['working_ability_score'];
            $validated['walidd_locations'] = $walidd['selected_locations'];
            $validated['walidd_location_score'] = $walidd['location_score'];
            $validated['walidd_intensity_score'] = $walidd['intensity_score'];
            $validated['walidd_pain_days'] = $walidd['pain_days'];
            $validated['walidd_pain_days_score'] = $walidd['pain_days_score'];
            $validated['walidd_total_score'] = $walidd['total_score'];
            $validated['walidd_category'] = $walidd['category'];
            $validated['walidd_interpretation'] = $walidd['interpretation'];
        } else {
            $validated['walidd_working_ability'] = 0;
            $validated['walidd_locations'] = [];
            $validated['walidd_location_score'] = 0;
            $validated['walidd_intensity_score'] = 0;
            $validated['walidd_pain_days'] = 0;
            $validated['walidd_pain_days_score'] = 0;
            $validated['walidd_total_score'] = 0;
            $validated['walidd_category'] = 'Tidak Dismenore';
            $validated['walidd_interpretation'] = 'Kondisi fisiologis bebas nyeri kram menstruasi.';
        }

        $validated['user_id'] = $user->id;
        $periodLog = PeriodLog::create($validated);

        // Update profil menarche jika diisi
        if (! empty($validated['menarche_age']) && $user->menarche_age !== (int) $validated['menarche_age']) {
            $user->update(['menarche_age' => (int) $validated['menarche_age']]);
        }

        // Tambahkan poin aktivitas
        $user->increment('points', 10);

        $successMsg = 'Catatan siklus haid berhasil disimpan (+10 poin).';
        if ($periodLog->walidd_total_score > 0) {
            $successMsg .= " Evaluasi Nyeri Dismenore: {$periodLog->walidd_category} (Skor WaLIDD: {$periodLog->walidd_total_score}/12).";
        }

        return redirect()->route('selfcare.index')->with('success', $successMsg);
    }

    public function storeBmi(Request $request)
    {
        $validated = $request->validate([
            'weight_kg' => 'required|numeric|min:20|max:250',
            'height_cm' => 'required|numeric|min:80|max:250',
        ]);

        $weight = (float) $validated['weight_kg'];
        $heightM = (float) $validated['height_cm'] / 100;
        $bmi = round($weight / ($heightM * $heightM), 1);

        // Kategori WHO & Kemenkes RI untuk Asia
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

        BmiLog::create([
            'user_id' => Auth::id(),
            'weight_kg' => $weight,
            'height_cm' => $validated['height_cm'],
            'bmi_value' => $bmi,
            'category' => $category,
            'advice' => $advice,
        ]);

        return redirect()->route('selfcare.index')->with('success', 'Kalkulasi IMT berhasil disimpan.');
    }

    public function bloodGuide()
    {
        return view('selfcare.blood_guide');
    }

    public function hygieneGuide()
    {
        return view('selfcare.hygiene_guide');
    }
}
