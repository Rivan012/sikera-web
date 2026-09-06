<?php

namespace App\Http\Controllers;

use App\Models\EducationalModule;
use App\Models\TestQuestion;
use App\Models\EvaluationResponse;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    /**
     * Tampilan Pre-Test Awal / Umum
     */
    public function showPretest()
    {
        $user = Auth::user();

        if ($user->pretest_completed) {
            return redirect()->route('mahasiswa.dashboard')->with('info', 'Anda telah menyelesaikan Pre-Test.');
        }

        $questions = TestQuestion::all();
        return view('evaluation.pretest', compact('questions'));
    }

    public function submitPretest(Request $request)
    {
        $user = Auth::user();
        $questions = TestQuestion::all();

        $rules = [];
        foreach ($questions as $q) {
            $rules['q_' . $q->id] = 'required|string';
        }
        $request->validate($rules, [
            'required' => 'Mohon jawab seluruh pertanyaan evaluasi.',
        ]);

        $rawAnswers = [];
        $correctCount = 0;
        $moduleStats = [
            1 => ['correct' => 0, 'total' => 0],
            2 => ['correct' => 0, 'total' => 0],
            3 => ['correct' => 0, 'total' => 0],
            4 => ['correct' => 0, 'total' => 0],
        ];

        foreach ($questions as $q) {
            $ans = $request->input('q_' . $q->id);
            $rawAnswers[$q->id] = $ans;

            $moduleStats[$q->module_target]['total']++;
            if (strtoupper($ans) === strtoupper($q->correct_answer)) {
                $correctCount++;
                $moduleStats[$q->module_target]['correct']++;
            }
        }

        $totalQuestions = count($questions);
        $totalScore = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        $scoresPerModule = [];
        foreach ($moduleStats as $mod => $stat) {
            $scoresPerModule['Modul ' . $mod] = $stat['total'] > 0 ? round(($stat['correct'] / $stat['total']) * 100) : 0;
        }

        $pretest = EvaluationResponse::create([
            'user_id' => $user->id,
            'type' => 'pre_test',
            'module_number' => null,
            'raw_answers' => $rawAnswers,
            'scores_per_module' => $scoresPerModule,
            'total_score' => $totalScore,
            'submitted_at' => now(),
        ]);

        $user->update([
            'pretest_completed' => true,
            'points' => $user->points + 50,
        ]);

        GoogleSheetsSyncService::syncPretestSubmission($user, $pretest);

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Pre-Test awal berhasil diselesaikan! Skor awal: ' . $totalScore . '/100. Selamat belajar.');
    }

    /**
     * Tampilan Pre-Test Khusus Modul Tertentu (Gating sebelum buka materi modul)
     */
    public function showModulePretest($moduleId)
    {
        $user = Auth::user();
        $module = EducationalModule::findOrFail($moduleId);

        // Cek apakah user sudah menyelesaikan Pre-Test modul ini
        $existingPretest = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'pre_test')
            ->where('module_number', $module->module_number)
            ->first();

        if ($existingPretest) {
            return redirect()->route('modules.show', $module->id)
                ->with('info', 'Anda telah menyelesaikan Pre-Test untuk Modul ' . $module->module_number . '. Silakan pelajari materi.');
        }

        $questions = TestQuestion::where('module_target', $module->module_number)->get();

        // Jika tidak ada soal khusus modul, gunakan soal umum
        if ($questions->isEmpty()) {
            $questions = TestQuestion::all();
        }

        return view('evaluation.module_pretest', compact('module', 'questions'));
    }

    public function submitModulePretest(Request $request, $moduleId)
    {
        $user = Auth::user();
        $module = EducationalModule::findOrFail($moduleId);
        $questions = TestQuestion::where('module_target', $module->module_number)->get();

        if ($questions->isEmpty()) {
            $questions = TestQuestion::all();
        }

        $rules = [];
        foreach ($questions as $q) {
            $rules['q_' . $q->id] = 'required|string';
        }
        $request->validate($rules, [
            'required' => 'Mohon jawab seluruh pertanyaan Pre-Test Modul ' . $module->module_number . ' sebelum melanjutkan.',
        ]);

        $rawAnswers = [];
        $correctCount = 0;

        foreach ($questions as $q) {
            $ans = $request->input('q_' . $q->id);
            $rawAnswers[$q->id] = $ans;
            if (strtoupper($ans) === strtoupper($q->correct_answer)) {
                $correctCount++;
            }
        }

        $totalScore = count($questions) > 0 ? round(($correctCount / count($questions)) * 100) : 0;

        $pretest = EvaluationResponse::create([
            'user_id' => $user->id,
            'type' => 'pre_test',
            'module_number' => $module->module_number,
            'raw_answers' => $rawAnswers,
            'scores_per_module' => ['Modul ' . $module->module_number => $totalScore],
            'total_score' => $totalScore,
            'submitted_at' => now(),
        ]);

        $user->increment('points', 25);
        $user->update(['pretest_completed' => true]);

        GoogleSheetsSyncService::syncPretestSubmission($user, $pretest);

        return redirect()->route('modules.show', $module->id)->with('success', 'Pre-Test Modul ' . $module->module_number . ' selesai (Skor: ' . $totalScore . '/100). Materi kini terbuka untuk dipelajari!');
    }

    /**
     * Tampilan Post-Test Khusus Modul Tertentu (Setelah selesai semua materi modul)
     */
    public function showModulePosttest($moduleId)
    {
        $user = Auth::user();
        $module = EducationalModule::findOrFail($moduleId);

        $pretest = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'pre_test')
            ->where(function($q) use ($module) {
                $q->where('module_number', $module->module_number)
                  ->orWhereNull('module_number');
            })
            ->latest()
            ->first();

        $questions = TestQuestion::where('module_target', $module->module_number)->get();
        if ($questions->isEmpty()) {
            $questions = TestQuestion::all();
        }

        return view('evaluation.module_posttest', compact('module', 'questions', 'pretest'));
    }

    public function submitModulePosttest(Request $request, $moduleId)
    {
        $user = Auth::user();
        $module = EducationalModule::findOrFail($moduleId);
        $questions = TestQuestion::where('module_target', $module->module_number)->get();

        if ($questions->isEmpty()) {
            $questions = TestQuestion::all();
        }

        $rules = [];
        foreach ($questions as $q) {
            $rules['q_' . $q->id] = 'required|string';
        }
        $request->validate($rules, [
            'required' => 'Mohon jawab seluruh pertanyaan Post-Test Modul ' . $module->module_number . '.',
        ]);

        $rawAnswers = [];
        $correctCount = 0;

        foreach ($questions as $q) {
            $ans = $request->input('q_' . $q->id);
            $rawAnswers[$q->id] = $ans;
            if (strtoupper($ans) === strtoupper($q->correct_answer)) {
                $correctCount++;
            }
        }

        $postScore = count($questions) > 0 ? round(($correctCount / count($questions)) * 100) : 0;

        $pretest = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'pre_test')
            ->where(function($q) use ($module) {
                $q->where('module_number', $module->module_number)
                  ->orWhereNull('module_number');
            })
            ->latest()
            ->first();

        $preScore = $pretest ? $pretest->total_score : 0;

        $nGain = 0;
        if ((100 - $preScore) > 0) {
            $nGain = round(($postScore - $preScore) / (100 - $preScore), 3);
        }

        $posttest = EvaluationResponse::create([
            'user_id' => $user->id,
            'type' => 'post_test',
            'module_number' => $module->module_number,
            'raw_answers' => $rawAnswers,
            'scores_per_module' => ['Modul ' . $module->module_number => $postScore],
            'total_score' => $postScore,
            'n_gain_score' => $nGain,
            'submitted_at' => now(),
        ]);

        $user->increment('points', 50);
        $user->update(['posttest_completed' => true]);

        GoogleSheetsSyncService::syncPosttestSubmission($user, $posttest, $nGain);

        return redirect()->route('modules.index')->with('success', 'Selamat! Anda telah menyelesaikan Post-Test Modul ' . $module->module_number . ' (Skor: ' . $postScore . ', N-Gain: ' . $nGain . '). Nilai telah tersimpan ke sistem penelitian.');
    }

    /**
     * Tampilan Post-Test Akhir Sesi
     */
    public function showPosttest()
    {
        $user = Auth::user();
        $questions = TestQuestion::all();
        $pretest = EvaluationResponse::where('user_id', $user->id)->where('type', 'pre_test')->latest()->first();

        return view('evaluation.posttest', compact('questions', 'pretest'));
    }

    public function submitPosttest(Request $request)
    {
        $user = Auth::user();
        $questions = TestQuestion::all();

        $rules = [];
        foreach ($questions as $q) {
            $rules['q_' . $q->id] = 'required|string';
        }
        $request->validate($rules, [
            'required' => 'Mohon jawab seluruh pertanyaan instrumen evaluasi Post-Test.',
        ]);

        $rawAnswers = [];
        $correctCount = 0;
        $moduleStats = [
            1 => ['correct' => 0, 'total' => 0],
            2 => ['correct' => 0, 'total' => 0],
            3 => ['correct' => 0, 'total' => 0],
            4 => ['correct' => 0, 'total' => 0],
        ];

        foreach ($questions as $q) {
            $ans = $request->input('q_' . $q->id);
            $rawAnswers[$q->id] = $ans;

            $moduleStats[$q->module_target]['total']++;
            if (strtoupper($ans) === strtoupper($q->correct_answer)) {
                $correctCount++;
                $moduleStats[$q->module_target]['correct']++;
            }
        }

        $totalQuestions = count($questions);
        $postScore = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        $scoresPerModule = [];
        foreach ($moduleStats as $mod => $stat) {
            $scoresPerModule['Modul ' . $mod] = $stat['total'] > 0 ? round(($stat['correct'] / $stat['total']) * 100) : 0;
        }

        $pretest = EvaluationResponse::where('user_id', $user->id)->where('type', 'pre_test')->latest()->first();
        $preScore = $pretest ? $pretest->total_score : 0;

        $nGain = 0;
        if ((100 - $preScore) > 0) {
            $nGain = round(($postScore - $preScore) / (100 - $preScore), 3);
        }

        $posttest = EvaluationResponse::create([
            'user_id' => $user->id,
            'type' => 'post_test',
            'module_number' => null,
            'raw_answers' => $rawAnswers,
            'scores_per_module' => $scoresPerModule,
            'total_score' => $postScore,
            'n_gain_score' => $nGain,
            'submitted_at' => now(),
        ]);

        $user->update([
            'posttest_completed' => true,
            'points' => $user->points + 100,
        ]);

        GoogleSheetsSyncService::syncPosttestSubmission($user, $posttest, $nGain);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Post-Test berhasil diselesaikan dan Nilai Akhir & N-Gain telah diperbarui ke Google Sheets (Skor: ' . $postScore . ', N-Gain: ' . $nGain . '). Sesi keluar berhasil.');
    }
}
