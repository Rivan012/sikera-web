<?php

namespace App\Http\Controllers;

use App\Models\PeriodLog;
use App\Models\BmiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SelfCareController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isFemale = ($user->gender === 'P');

        $recentPeriods = $isFemale ? PeriodLog::where('user_id', $user->id)->latest()->take(5)->get() : collect();
        $latestPeriod = $recentPeriods->first();
        $recentBmi = BmiLog::where('user_id', $user->id)->latest()->take(5)->get();
        $latestBmi = $recentBmi->first();

        // Prediksi siklus haid dan masa subur (hanya untuk perempuan)
        $nextPeriodDate = null;
        $fertileWindowStart = null;
        $fertileWindowEnd = null;
        $ovulationDate = null;

        if ($isFemale && $latestPeriod) {
            $cycleLen = $latestPeriod->cycle_length ?? 28;
            $start = \Carbon\Carbon::parse($latestPeriod->start_date);
            $nextPeriodDate = $start->copy()->addDays($cycleLen);
            $ovulationDate = $nextPeriodDate->copy()->subDays(14);
            $fertileWindowStart = $ovulationDate->copy()->subDays(4);
            $fertileWindowEnd = $ovulationDate->copy()->addDays(1);
        }

        return view('selfcare.index', compact(
            'user',
            'isFemale',
            'recentPeriods',
            'latestPeriod',
            'recentBmi',
            'latestBmi',
            'nextPeriodDate',
            'fertileWindowStart',
            'fertileWindowEnd',
            'ovulationDate'
        ));
    }

    public function storePeriod(Request $request)
    {
        $user = Auth::user();

        // Validasi gender: hanya perempuan yang dapat mencatat siklus haid
        if ($user->gender === 'L') {
            return redirect()->route('selfcare.index')
                ->with('warning', 'Fitur pencatatan siklus menstruasi hanya diperuntukkan bagi perempuan.');
        }

        $validated = $request->validate([
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

        $validated['user_id'] = $user->id;
        PeriodLog::create($validated);

        // Tambahkan poin aktivitas
        $user->increment('points', 10);

        return redirect()->route('selfcare.index')->with('success', 'Catatan siklus haid berhasil disimpan.');
    }

    public function storeBmi(Request $request)
    {
        $validated = $request->validate([
            'weight_kg' => 'required|numeric|min:20|max:250',
            'height_cm' => 'required|numeric|min:80|max:250',
        ]);

        $weight = (float)$validated['weight_kg'];
        $heightM = (float)$validated['height_cm'] / 100;
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
