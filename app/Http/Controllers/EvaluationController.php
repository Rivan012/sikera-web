<?php

namespace App\Http\Controllers;

use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\TestQuestion;
use App\Services\EvaluationEligibilityService;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EvaluationController extends Controller
{
    public function showPretest(): RedirectResponse
    {
        return redirect()->route('modules.index')
            ->with('info', 'Pre-Test dikerjakan pada masing-masing modul sebelum membuka materi modul tersebut.');
    }

    public function submitPretest(): RedirectResponse
    {
        return redirect()->route('modules.index');
    }

    public function showModulePretest(int $moduleId): Response|RedirectResponse
    {
        $user = Auth::user();
        $module = EducationalModule::findOrFail($moduleId);

        if ($user->isMahasiswa()) {
            $existingPretest = EvaluationResponse::where('user_id', $user->id)
                ->where('type', 'pre_test')
                ->where('module_number', $module->module_number)
                ->first();

            if ($existingPretest) {
                return redirect()->route('modules.show', $module->id)
                    ->with('info', 'Anda telah menyelesaikan Pre-Test Modul '.$module->module_number.'. Silakan pelajari materi.');
            }
        }

        $questions = TestQuestion::preTest()
            ->where('module_target', $module->module_number)
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            $questions = TestQuestion::preTest()->orderBy('id')->get();
        }

        return Inertia::render('Evaluation/ModulePretest', [
            'module' => [
                'id' => $module->id,
                'module_number' => $module->module_number,
                'title' => $module->title,
                'subtitle' => $module->subtitle,
                'estimated_time' => $module->estimated_time,
            ],
            'questions' => $questions->map(fn (TestQuestion $q): array => [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $q->options,
            ]),
        ]);
    }

    public function submitModulePretest(Request $request, int $moduleId): RedirectResponse
    {
        $user = Auth::user();
        $module = EducationalModule::findOrFail($moduleId);

        $questions = TestQuestion::preTest()
            ->where('module_target', $module->module_number)
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            $questions = TestQuestion::preTest()->orderBy('id')->get();
        }

        $submittedAnswers = $this->extractAnswers($request, $questions);

        if (count($submittedAnswers) < $questions->count()) {
            return back()->withErrors([
                'answers' => 'Mohon jawab seluruh pertanyaan Pre-Test Modul '.$module->module_number.' sebelum melanjutkan.',
            ]);
        }

        $rawAnswers = [];
        $correctCount = 0;

        foreach ($questions as $q) {
            $ans = strtoupper((string) ($submittedAnswers[$q->id] ?? ''));
            $rawAnswers[$q->id] = $ans;
            if ($ans !== '' && $ans === strtoupper($q->correct_answer)) {
                $correctCount++;
            }
        }

        $totalScore = $questions->isNotEmpty()
            ? (int) round(($correctCount / $questions->count()) * 100)
            : 0;

        $pretest = EvaluationResponse::create([
            'user_id' => $user->id,
            'type' => 'pre_test',
            'module_number' => $module->module_number,
            'raw_answers' => $rawAnswers,
            'scores_per_module' => ['Modul '.$module->module_number => $totalScore],
            'total_score' => $totalScore,
            'submitted_at' => now(),
        ]);

        $user->increment('points', 25);
        $user->update(['pretest_completed' => true]);

        GoogleSheetsSyncService::syncPretestSubmission($user, $pretest);

        return redirect()->route('modules.show', $module->id)
            ->with('success', 'Pre-Test Modul '.$module->module_number.' selesai (Skor: '.$totalScore.'/100). Materi modul kini terbuka.');
    }

    public function showModulePosttest(): RedirectResponse
    {
        return redirect()->route('posttest.show')
            ->with('info', 'Post-Test dikerjakan secara terpadu 3 hari setelah seluruh modul selesai dipelajari.');
    }

    public function submitModulePosttest(): RedirectResponse
    {
        return redirect()->route('posttest.show');
    }

    public function showPosttest(EvaluationEligibilityService $eligibility): Response
    {
        $user = Auth::user();
        $gate = $eligibility->syncAllModulesCompletion($user);
        $preScore = $eligibility->calculateAggregatePretestScore($user);

        $questions = [];
        if ($gate['is_posttest_unlocked']) {
            $questionModels = TestQuestion::postTest()->orderBy('id')->get();
            if ($questionModels->isEmpty()) {
                $questionModels = TestQuestion::orderBy('id')->get();
            }

            $questions = $questionModels->map(fn (TestQuestion $q): array => [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $q->options,
            ])->all();
        }

        $modulesProgress = EducationalModule::with(['topics:id,educational_module_id'])
            ->orderBy('module_number')
            ->get()
            ->map(function (EducationalModule $m) use ($user): array {
                $topicIds = $m->topics->pluck('id');
                $done = $topicIds->isEmpty()
                    ? 0
                    : $user->moduleProgresses()->whereIn('module_topic_id', $topicIds)->where('is_completed', true)->count();

                return [
                    'id' => $m->id,
                    'module_number' => $m->module_number,
                    'title' => $m->title,
                    'completed_topics' => $done,
                    'total_topics' => $topicIds->count(),
                    'is_completed' => $topicIds->isNotEmpty() && $done >= $topicIds->count(),
                ];
            });

        return Inertia::render('Evaluation/Posttest', [
            'gate' => $gate,
            'preScore' => $preScore,
            'questions' => $questions,
            'modulesProgress' => $modulesProgress,
        ]);
    }

    public function submitPosttest(Request $request, EvaluationEligibilityService $eligibility): RedirectResponse
    {
        $user = Auth::user();
        $gate = $eligibility->syncAllModulesCompletion($user);

        if ($user->isMahasiswa() && ! $gate['is_posttest_unlocked']) {
            abort(403, 'Post-Test belum dapat dikerjakan. Selesaikan seluruh modul dan tunggu 3 hari sejak penyelesaian modul terakhir.');
        }

        $questions = TestQuestion::postTest()->orderBy('id')->get();
        if ($questions->isEmpty()) {
            $questions = TestQuestion::orderBy('id')->get();
        }

        $submittedAnswers = $this->extractAnswers($request, $questions);

        if (count($submittedAnswers) < $questions->count()) {
            return back()->withErrors([
                'answers' => 'Mohon jawab seluruh pertanyaan instrumen evaluasi Post-Test.',
            ]);
        }

        $rawAnswers = [];
        $correctCount = 0;

        foreach ($questions as $q) {
            $ans = strtoupper((string) ($submittedAnswers[$q->id] ?? ''));
            $rawAnswers[$q->id] = $ans;
            if ($ans !== '' && $ans === strtoupper($q->correct_answer)) {
                $correctCount++;
            }
        }

        $postScore = $questions->isNotEmpty()
            ? (int) round(($correctCount / $questions->count()) * 100)
            : 0;

        $preScore = $eligibility->calculateAggregatePretestScore($user);
        $nGain = $eligibility->calculateNGain($preScore, (float) $postScore);

        $posttest = EvaluationResponse::create([
            'user_id' => $user->id,
            'type' => 'post_test',
            'module_number' => null,
            'raw_answers' => $rawAnswers,
            'scores_per_module' => null,
            'total_score' => $postScore,
            'n_gain_score' => $nGain,
            'submitted_at' => now(),
        ]);

        $user->update([
            'posttest_completed' => true,
            'points' => (int) $user->points + 100,
        ]);

        GoogleSheetsSyncService::syncPosttestSubmission($user, $posttest, $nGain);

        return redirect()->route('posttest.show')
            ->with('success', 'Post-Test berhasil diselesaikan (Skor Akhir: '.$postScore.'/100, N-Gain: '.$nGain.').');
    }

    /**
     * @param  Collection<int, TestQuestion>  $questions
     * @return array<int, string>
     */
    private function extractAnswers(Request $request, $questions): array
    {
        $answersPayload = $request->input('answers');
        $resolved = [];

        foreach ($questions as $q) {
            $val = is_array($answersPayload) && isset($answersPayload[$q->id])
                ? $answersPayload[$q->id]
                : $request->input('q_'.$q->id);

            if (is_string($val) && trim($val) !== '') {
                $resolved[$q->id] = trim($val);
            }
        }

        return $resolved;
    }
}
