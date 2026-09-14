<?php

namespace App\Http\Controllers;

use App\Models\EvaluationResponse;
use App\Models\TestQuestion;
use App\Services\PostTestExcelExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminEvaluationController extends Controller
{
    /**
     * Memastikan hanya admin yang dapat mengakses
     */
    private function authorizeAdmin(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman pengelolaan evaluasi.');
        }
    }

    /**
     * Daftar Semua Hasil & Lembar Jawaban Evaluasi Mahasiswa
     */
    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $typeFilter = $request->query('type', 'all'); // all, post_test, pre_test
        $moduleFilter = $request->query('module', 'all'); // all, general, 1, 2, 3, 4
        $searchQuery = $request->query('q');

        $query = EvaluationResponse::with('user');

        // Filter Tipe Evaluasi
        if ($typeFilter && $typeFilter !== 'all') {
            $query->where('type', $typeFilter);
        }

        // Filter Lingkup Modul
        if ($moduleFilter && $moduleFilter !== 'all') {
            if ($moduleFilter === 'general') {
                $query->whereNull('module_number');
            } else {
                $query->where('module_number', (int) $moduleFilter);
            }
        }

        // Pencarian Nama / NIM / Inisial Mahasiswa
        if ($searchQuery) {
            $query->whereHas('user', function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('nim', 'like', "%{$searchQuery}%")
                    ->orWhere('initials', 'like', "%{$searchQuery}%")
                    ->orWhere('prodi', 'like', "%{$searchQuery}%");
            });
        }

        $evaluations = $query->latest('submitted_at')->paginate(15)->withQueryString();

        // Statistik Ringkas untuk Dashboard Header
        $totalEvaluations = EvaluationResponse::count();
        $totalPosttests = EvaluationResponse::where('type', 'post_test')->count();
        $totalPretests = EvaluationResponse::where('type', 'pre_test')->count();

        $avgPostScore = EvaluationResponse::where('type', 'post_test')->avg('total_score');
        $avgPostScore = $avgPostScore ? round($avgPostScore, 1) : 0;

        $avgPreScore = EvaluationResponse::where('type', 'pre_test')->avg('total_score');
        $avgPreScore = $avgPreScore ? round($avgPreScore, 1) : 0;

        $avgNGain = EvaluationResponse::where('type', 'post_test')->whereNotNull('n_gain_score')->avg('n_gain_score');
        $avgNGain = $avgNGain ? round($avgNGain, 3) : 0;

        $highGainCount = EvaluationResponse::where('type', 'post_test')->where('n_gain_score', '>=', 0.7)->count();
        $mediumGainCount = EvaluationResponse::where('type', 'post_test')->whereBetween('n_gain_score', [0.3, 0.699])->count();
        $lowGainCount = EvaluationResponse::where('type', 'post_test')->where('n_gain_score', '<', 0.3)->count();

        return view('admin.evaluations.index', compact(
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

    /**
     * Detail Lembar Jawaban dan Analisis Butir Soal Mahasiswa
     */
    public function show(int $id): View
    {
        $this->authorizeAdmin();

        $evaluation = EvaluationResponse::with('user')->findOrFail($id);
        $user = $evaluation->user;

        // Ambil soal yang relevan
        $rawAnswers = $evaluation->raw_answers ?? [];
        $questionIds = ! empty($rawAnswers) ? array_keys($rawAnswers) : [];

        if (! empty($questionIds)) {
            $questions = TestQuestion::whereIn('id', $questionIds)->get()->keyBy('id');
        } elseif ($evaluation->module_number !== null) {
            $questions = TestQuestion::where('module_target', $evaluation->module_number)->get()->keyBy('id');
        } else {
            $questions = TestQuestion::all()->keyBy('id');
        }

        // Cari tes pembanding (Pre-Test jika sedang melihat Post-Test, atau sebaliknya)
        $comparisonTest = null;
        if ($evaluation->type === 'post_test') {
            $comparisonTest = EvaluationResponse::where('user_id', $evaluation->user_id)
                ->where('type', 'pre_test')
                ->where(function ($q) use ($evaluation) {
                    if ($evaluation->module_number !== null) {
                        $q->where('module_number', $evaluation->module_number)
                            ->orWhereNull('module_number');
                    } else {
                        $q->whereNull('module_number');
                    }
                })
                ->latest('submitted_at')
                ->first();
        } elseif ($evaluation->type === 'pre_test') {
            $comparisonTest = EvaluationResponse::where('user_id', $evaluation->user_id)
                ->where('type', 'post_test')
                ->where(function ($q) use ($evaluation) {
                    if ($evaluation->module_number !== null) {
                        $q->where('module_number', $evaluation->module_number)
                            ->orWhereNull('module_number');
                    } else {
                        $q->whereNull('module_number');
                    }
                })
                ->latest('submitted_at')
                ->first();
        }

        $comparisonRawAnswers = $comparisonTest?->raw_answers ?? [];

        // Susun analisis butir soal per pertanyaan
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
            $comparisonIsCorrect = ($comparisonAns !== null && $comparisonAns === $correctAns);

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
                'comparison_is_correct' => $comparisonIsCorrect,
            ];
        }

        return view('admin.evaluations.show', compact(
            'evaluation',
            'user',
            'questionItems',
            'correctCount',
            'totalQuestions',
            'comparisonTest'
        ));
    }

    /**
     * Ekspor Data Post-Test dan Tabulasi Butir Soal ke Excel (.xlsx)
     */
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
