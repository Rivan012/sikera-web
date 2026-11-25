<?php

namespace App\Http\Controllers;

use App\Models\EvaluationResponse;
use App\Models\TestQuestion;
use App\Services\EvaluationEligibilityService;
use App\Services\PostTestExcelExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminEvaluationController extends Controller
{
    private function authorizeAdmin(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman pengelolaan evaluasi.');
        }
    }

    public function index(Request $request): Response
    {
        $this->authorizeAdmin();

        $typeFilter = (string) $request->query('type', 'all');
        $moduleFilter = (string) $request->query('module', 'all');
        $searchQuery = trim((string) $request->query('q', ''));

        $query = EvaluationResponse::with('user');

        if ($typeFilter !== '' && $typeFilter !== 'all') {
            $query->where('type', $typeFilter);
        }

        if ($moduleFilter !== '' && $moduleFilter !== 'all') {
            if ($moduleFilter === 'general') {
                $query->whereNull('module_number');
            } else {
                $query->where('module_number', (int) $moduleFilter);
            }
        }

        if ($searchQuery !== '') {
            $query->whereHas('user', function ($q) use ($searchQuery): void {
                $q->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('nim', 'like', "%{$searchQuery}%")
                    ->orWhere('initials', 'like', "%{$searchQuery}%")
                    ->orWhere('prodi', 'like', "%{$searchQuery}%");
            });
        }

        $evaluations = $query->latest('submitted_at')->paginate(15)->withQueryString()
            ->through(fn (EvaluationResponse $eval): array => [
                'id' => $eval->id,
                'type' => $eval->type,
                'module_number' => $eval->module_number,
                'total_score' => $eval->total_score,
                'n_gain_score' => $eval->n_gain_score,
                'submitted_at' => $eval->submitted_at?->format('d M Y, H:i'),
                'user' => $eval->user ? [
                    'id' => $eval->user->id,
                    'name' => $eval->user->name,
                    'initials' => $eval->user->initials,
                    'nim' => $eval->user->nim,
                    'prodi' => $eval->user->prodi,
                    'fakultas' => $eval->user->fakultas,
                ] : null,
            ]);

        $totalEvaluations = EvaluationResponse::count();
        $totalPosttests = EvaluationResponse::where('type', 'post_test')->count();
        $totalPretests = EvaluationResponse::where('type', 'pre_test')->count();

        $avgPostScore = round((float) (EvaluationResponse::where('type', 'post_test')->avg('total_score') ?: 0), 1);
        $avgPreScore = round((float) (EvaluationResponse::where('type', 'pre_test')->avg('total_score') ?: 0), 1);
        $avgNGain = round((float) (EvaluationResponse::where('type', 'post_test')->whereNotNull('n_gain_score')->avg('n_gain_score') ?: 0), 3);

        $highGainCount = EvaluationResponse::where('type', 'post_test')->where('n_gain_score', '>=', 0.7)->count();
        $mediumGainCount = EvaluationResponse::where('type', 'post_test')->whereBetween('n_gain_score', [0.3, 0.699])->count();
        $lowGainCount = EvaluationResponse::where('type', 'post_test')->where('n_gain_score', '<', 0.3)->count();

        return Inertia::render('Admin/Evaluations/Index', compact(
            'evaluations',
            'typeFilter',
            'moduleFilter',
            'searchQuery',
            'totalEvaluations',
            'totalPosttests',
            'totalPretests',
            'avgPostScore',
            'avgPreScore',
            'avgNGain',
            'highGainCount',
            'mediumGainCount',
            'lowGainCount'
        ));
    }

    public function show(int $id, EvaluationEligibilityService $eligibility): Response
    {
        $this->authorizeAdmin();

        $evaluation = EvaluationResponse::with('user')->findOrFail($id);
        $user = $evaluation->user;

        $rawAnswers = $evaluation->raw_answers ?? [];
        $questionIds = ! empty($rawAnswers) ? array_keys($rawAnswers) : [];

        if (! empty($questionIds)) {
            $questions = TestQuestion::whereIn('id', $questionIds)->get()->keyBy('id');
        } elseif ($evaluation->module_number !== null) {
            $questions = TestQuestion::preTest()->where('module_target', $evaluation->module_number)->get()->keyBy('id');
        } else {
            $questions = TestQuestion::postTest()->get()->keyBy('id');
            if ($questions->isEmpty()) {
                $questions = TestQuestion::all()->keyBy('id');
            }
        }

        $comparisonScore = null;
        $comparisonRawAnswers = [];
        if ($evaluation->type === 'post_test' && $user) {
            $comparisonScore = $eligibility->calculateAggregatePretestScore($user);
        } elseif ($evaluation->type === 'pre_test') {
            $comparisonTest = EvaluationResponse::where('user_id', $evaluation->user_id)
                ->where('type', 'post_test')
                ->latest('submitted_at')
                ->first();
            $comparisonScore = $comparisonTest?->total_score;
            $comparisonRawAnswers = $comparisonTest?->raw_answers ?? [];
        }

        $questionItems = [];
        $correctCount = 0;
        $totalQuestions = count($questions);

        $number = 1;
        foreach ($questions as $qId => $q) {
            $userAns = isset($rawAnswers[$qId]) ? strtoupper((string) $rawAnswers[$qId]) : null;
            $correctAns = strtoupper((string) $q->correct_answer);
            $isCorrect = ($userAns !== null && $userAns === $correctAns);

            if ($isCorrect) {
                $correctCount++;
            }

            $comparisonAns = isset($comparisonRawAnswers[$qId]) ? strtoupper((string) $comparisonRawAnswers[$qId]) : null;

            $questionItems[] = [
                'number' => $number++,
                'id' => $q->id,
                'module_target' => $q->module_target,
                'question_text' => $q->question_text,
                'options' => $q->options ?? [],
                'user_answer' => $userAns,
                'correct_answer' => $correctAns,
                'is_correct' => $isCorrect,
                'explanation' => $q->explanation,
                'comparison_answer' => $comparisonAns,
            ];
        }

        return Inertia::render('Admin/Evaluations/Show', [
            'evaluation' => [
                'id' => $evaluation->id,
                'type' => $evaluation->type,
                'module_number' => $evaluation->module_number,
                'total_score' => $evaluation->total_score,
                'n_gain_score' => $evaluation->n_gain_score,
                'submitted_at' => $evaluation->submitted_at?->format('d M Y, H:i'),
            ],
            'student' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'initials' => $user->initials,
                'nim' => $user->nim,
                'email' => $user->email,
                'prodi' => $user->prodi,
                'fakultas' => $user->fakultas,
            ] : null,
            'questionItems' => $questionItems,
            'correctCount' => $correctCount,
            'totalQuestions' => $totalQuestions,
            'comparisonScore' => $comparisonScore,
        ]);
    }

    public function exportExcel(Request $request, PostTestExcelExportService $exportService): BinaryFileResponse
    {
        $this->authorizeAdmin();

        $filters = [
            'module' => $request->query('module'),
            'q' => $request->query('q'),
        ];

        $exported = $exportService->export($filters);

        return response()->download($exported['path'], $exported['filename'], [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
