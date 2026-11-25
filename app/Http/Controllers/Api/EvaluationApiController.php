<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EvaluationResponse;
use App\Models\TestQuestion;
use App\Services\EvaluationEligibilityService;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvaluationApiController extends Controller
{
    public function status(Request $request, EvaluationEligibilityService $eligibility): JsonResponse
    {
        $user = $request->user();
        $gate = $eligibility->syncAllModulesCompletion($user);

        return response()->json([
            'success' => true,
            'message' => 'Status kelayakan Post-Test berhasil dimuat.',
            'data' => [
                'pretest_average_score' => $eligibility->calculateAggregatePretestScore($user),
                'posttest_gate' => $gate,
            ],
        ]);
    }

    public function getQuestions(Request $request, EvaluationEligibilityService $eligibility): JsonResponse
    {
        $user = $request->user();
        $gate = $eligibility->syncAllModulesCompletion($user);

        if ($user->isMahasiswa() && ! $gate['is_posttest_unlocked']) {
            $reason = $gate['all_modules_completed'] ? 'waiting_period' : 'modules_incomplete';
            $message = $gate['all_modules_completed']
                ? 'Post-Test baru dapat dibuka 3 hari setelah seluruh modul selesai dipelajari.'
                : 'Selesaikan seluruh modul pembelajaran terlebih dahulu sebelum mengakses Post-Test.';

            return response()->json([
                'success' => false,
                'reason' => $reason,
                'message' => $message,
                'data' => [
                    'posttest_gate' => $gate,
                ],
            ], 403);
        }

        $questions = TestQuestion::postTest()->orderBy('id')->get();
        if ($questions->isEmpty()) {
            $questions = TestQuestion::orderBy('id')->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Soal Post-Test berhasil dimuat.',
            'data' => [
                'pretest_average_score' => $eligibility->calculateAggregatePretestScore($user),
                'total_questions' => $questions->count(),
                'questions' => $questions->map(fn (TestQuestion $q): array => [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'options' => $q->options,
                ]),
                'posttest_gate' => $gate,
            ],
        ]);
    }

    public function submit(Request $request, EvaluationEligibilityService $eligibility): JsonResponse
    {
        $user = $request->user();
        $gate = $eligibility->syncAllModulesCompletion($user);

        if ($user->isMahasiswa() && ! $gate['is_posttest_unlocked']) {
            $reason = $gate['all_modules_completed'] ? 'waiting_period' : 'modules_incomplete';

            return response()->json([
                'success' => false,
                'reason' => $reason,
                'message' => 'Post-Test belum dapat dikirim. Pastikan seluruh modul selesai dan jeda 3 hari telah terpenuhi.',
                'data' => [
                    'posttest_gate' => $gate,
                ],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'answers' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Format jawaban tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $questions = TestQuestion::postTest()->orderBy('id')->get();
        if ($questions->isEmpty()) {
            $questions = TestQuestion::orderBy('id')->get();
        }

        $answers = $request->input('answers');
        $rawAnswers = [];
        $correctCount = 0;

        foreach ($questions as $q) {
            $userAns = isset($answers[$q->id]) ? strtoupper(trim((string) $answers[$q->id])) : null;
            if ($userAns !== null && $userAns !== '') {
                $rawAnswers[$q->id] = $userAns;
            }
            if ($userAns && $userAns === strtoupper($q->correct_answer)) {
                $correctCount++;
            }
        }

        $postScore = $questions->isNotEmpty()
            ? (int) round(($correctCount / $questions->count()) * 100)
            : 0;

        $preScore = $eligibility->calculateAggregatePretestScore($user);
        $nGain = $eligibility->calculateNGain($preScore, (float) $postScore);
        $category = $eligibility->categorizeNGain($nGain);

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

        return response()->json([
            'success' => true,
            'message' => 'Post-Test selesai dikerjakan. Skor akhir dan N-Gain telah disimpan.',
            'data' => [
                'pretest_score' => $preScore,
                'posttest_score' => $postScore,
                'n_gain_score' => $nGain,
                'effectiveness_category' => $category,
                'correct_answers' => $correctCount,
                'total_questions' => $questions->count(),
                'points_earned' => 100,
            ],
        ]);
    }
}
