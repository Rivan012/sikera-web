<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\ModuleProgress;
use App\Models\ModuleTopic;
use App\Models\TestQuestion;
use App\Services\GoogleSheetsSyncService;
use App\Services\ModuleExcelImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    /**
     * Daftar Semua Modul Pembelajaran
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $modules = EducationalModule::withCount('topics')->orderBy('module_number')->get()->map(function ($mod) use ($user) {
            $topicIds = $mod->topics->pluck('id');
            $completedCount = ModuleProgress::where('user_id', $user->id)
                ->whereIn('module_topic_id', $topicIds)
                ->where('is_completed', true)
                ->count();

            $hasPretest = EvaluationResponse::where('user_id', $user->id)
                ->where('type', 'pre_test')
                ->where(function ($q) use ($mod) {
                    $q->where('module_number', $mod->module_number)
                        ->orWhereNull('module_number');
                })
                ->exists();

            $posttest = EvaluationResponse::where('user_id', $user->id)
                ->where('type', 'post_test')
                ->where('module_number', $mod->module_number)
                ->latest()
                ->first();

            $allTopicsDone = ($completedCount >= $mod->topics_count) && $mod->topics_count > 0;

            return [
                'id' => $mod->id,
                'module_number' => $mod->module_number,
                'title' => $mod->title,
                'subtitle' => $mod->subtitle,
                'description' => $mod->description,
                'banner_image' => url($mod->banner_image ?: "/images/banners/banner-module-{$mod->module_number}.svg"),
                'estimated_time' => $mod->estimated_time,
                'total_topics' => $mod->topics_count,
                'completed_topics' => $completedCount,
                'progress_percentage' => $mod->topics_count > 0 ? round(($completedCount / $mod->topics_count) * 100) : 0,
                'is_locked' => ! $hasPretest,
                'has_pretest' => $hasPretest,
                'all_topics_done' => $allTopicsDone,
                'has_posttest' => (bool) $posttest,
                'posttest_score' => $posttest ? $posttest->total_score : null,
                'n_gain' => $posttest ? $posttest->n_gain_score : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar modul berhasil dimuat.',
            'data' => [
                'modules' => $modules,
            ],
        ]);
    }

    /**
     * Detail Modul & Daftar Submateri
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $module = EducationalModule::with(['topics' => fn ($q) => $q->orderBy('order_index')])->findOrFail($id);

        $hasPretest = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'pre_test')
            ->where(function ($q) use ($module) {
                $q->where('module_number', $module->module_number)
                    ->orWhereNull('module_number');
            })
            ->exists();

        $completedTopicIds = ModuleProgress::where('user_id', $user->id)
            ->whereIn('module_topic_id', $module->topics->pluck('id'))
            ->where('is_completed', true)
            ->pluck('module_topic_id')
            ->toArray();

        $topics = $module->topics->map(function ($tp) use ($completedTopicIds, $hasPretest) {
            return [
                'id' => $tp->id,
                'topic_code' => $tp->topic_code,
                'title' => $tp->title,
                'has_video' => (bool) $tp->youtube_video_id,
                'is_completed' => in_array($tp->id, $completedTopicIds),
                'is_locked' => ! $hasPretest,
            ];
        });

        $allTopicsDone = count($completedTopicIds) >= $module->topics->count() && $module->topics->count() > 0;

        $posttest = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'post_test')
            ->where('module_number', $module->module_number)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Detail modul berhasil dimuat.',
            'data' => [
                'module' => [
                    'id' => $module->id,
                    'module_number' => $module->module_number,
                    'title' => $module->title,
                    'subtitle' => $module->subtitle,
                    'description' => $module->description,
                    'banner_image' => url($module->banner_image ?: "/images/banners/banner-module-{$module->module_number}.svg"),
                    'estimated_time' => $module->estimated_time,
                ],
                'is_locked' => ! $hasPretest,
                'has_pretest' => $hasPretest,
                'all_topics_done' => $allTopicsDone,
                'has_posttest' => (bool) $posttest,
                'posttest_score' => $posttest ? $posttest->total_score : null,
                'n_gain' => $posttest ? $posttest->n_gain_score : null,
                'topics' => $topics,
            ],
        ]);
    }

    /**
     * Soal Pre-Test Khusus Modul
     */
    public function getPretestQuestions($moduleId)
    {
        $module = EducationalModule::findOrFail($moduleId);
        $questions = TestQuestion::where('module_target', $module->module_number)->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'module_target' => $q->module_target,
                'question_text' => $q->question_text,
                'options' => $q->options,
            ];
        });

        if ($questions->isEmpty()) {
            $questions = TestQuestion::all()->map(function ($q) {
                return [
                    'id' => $q->id,
                    'module_target' => $q->module_target,
                    'question_text' => $q->question_text,
                    'options' => $q->options,
                ];
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Soal Pre-Test Modul '.$module->module_number.' berhasil dimuat.',
            'data' => [
                'module_id' => $module->id,
                'module_number' => $module->module_number,
                'module_title' => $module->title,
                'total_questions' => $questions->count(),
                'questions' => $questions,
            ],
        ]);
    }

    /**
     * Submit Jawaban Pre-Test Modul
     */
    public function submitPretest(Request $request, $moduleId)
    {
        $user = $request->user();
        $module = EducationalModule::findOrFail($moduleId);
        $questions = TestQuestion::where('module_target', $module->module_number)->get();

        if ($questions->isEmpty()) {
            $questions = TestQuestion::all();
        }

        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Format jawaban tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $answers = $request->input('answers'); // { "1": "A", "2": "B" }
        $correctCount = 0;

        foreach ($questions as $q) {
            $userAns = $answers[$q->id] ?? null;
            if ($userAns && strtoupper($userAns) === strtoupper($q->correct_answer)) {
                $correctCount++;
            }
        }

        $totalScore = count($questions) > 0 ? round(($correctCount / count($questions)) * 100) : 0;

        $pretest = EvaluationResponse::create([
            'user_id' => $user->id,
            'type' => 'pre_test',
            'module_number' => $module->module_number,
            'raw_answers' => $answers,
            'scores_per_module' => ['Modul '.$module->module_number => $totalScore],
            'total_score' => $totalScore,
            'submitted_at' => now(),
        ]);

        $user->increment('points', 25);
        $user->update(['pretest_completed' => true]);

        GoogleSheetsSyncService::syncPretestSubmission($user, $pretest);

        return response()->json([
            'success' => true,
            'message' => 'Pre-Test Modul '.$module->module_number.' selesai! Akses materi telah dibuka.',
            'data' => [
                'module_id' => $module->id,
                'module_number' => $module->module_number,
                'total_questions' => count($questions),
                'correct_answers' => $correctCount,
                'score' => $totalScore,
                'points_earned' => 25,
            ],
        ]);
    }

    /**
     * Membaca Submateri Modul (Konten HTML & Video)
     */
    public function readTopic(Request $request, $moduleId, $topicId)
    {
        $user = $request->user();
        $module = EducationalModule::findOrFail($moduleId);
        $topic = ModuleTopic::where('educational_module_id', $moduleId)->findOrFail($topicId);

        // Cek Gating Pre-Test
        $hasPretest = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'pre_test')
            ->where(function ($q) use ($module) {
                $q->where('module_number', $module->module_number)
                    ->orWhereNull('module_number');
            })
            ->exists();

        if (! $hasPretest) {
            return response()->json([
                'success' => false,
                'message' => 'Materi terkunci! Silakan selesaikan Pre-Test Modul '.$module->module_number.' terlebih dahulu.',
                'requires_pretest' => true,
                'module_id' => $module->id,
            ], 403);
        }

        // Tandai materi selesai dibaca & beri poin
        $progress = ModuleProgress::firstOrCreate(
            ['user_id' => $user->id, 'module_topic_id' => $topic->id],
            ['is_completed' => false]
        );

        $pointsAwarded = 0;
        if (! $progress->is_completed) {
            $progress->update(['is_completed' => true, 'completed_at' => now()]);
            $user->increment('points', 10);
            $pointsAwarded = 10;
        }

        // Prev & Next navigation
        $allTopics = ModuleTopic::where('educational_module_id', $moduleId)->orderBy('order_index')->get();
        $currentIndex = $allTopics->search(fn ($t) => $t->id === $topic->id);
        $prevTopic = $currentIndex > 0 ? $allTopics[$currentIndex - 1] : null;
        $nextTopic = $currentIndex < $allTopics->count() - 1 ? $allTopics[$currentIndex + 1] : null;

        $completedCount = ModuleProgress::where('user_id', $user->id)
            ->whereIn('module_topic_id', $allTopics->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $allTopicsCompleted = $completedCount >= $allTopics->count();

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil dimuat.',
            'data' => [
                'topic' => [
                    'id' => $topic->id,
                    'topic_code' => $topic->topic_code,
                    'title' => $topic->title,
                    'youtube_video_id' => $topic->youtube_video_id,
                    'youtube_embed_url' => $topic->youtube_video_id ? "https://www.youtube.com/embed/{$topic->youtube_video_id}" : null,
                    'content_html' => $topic->content_html,
                ],
                'module' => [
                    'id' => $module->id,
                    'module_number' => $module->module_number,
                    'title' => $module->title,
                ],
                'points_earned' => $pointsAwarded,
                'prev_topic_id' => $prevTopic ? $prevTopic->id : null,
                'next_topic_id' => $nextTopic ? $nextTopic->id : null,
                'all_topics_completed' => $allTopicsCompleted,
                'ready_for_posttest' => $allTopicsCompleted,
            ],
        ]);
    }

    /**
     * Soal Post-Test Khusus Modul
     */
    public function getPosttestQuestions(Request $request, $moduleId)
    {
        $user = $request->user();
        $module = EducationalModule::findOrFail($moduleId);

        $pretest = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'pre_test')
            ->where(function ($q) use ($module) {
                $q->where('module_number', $module->module_number)
                    ->orWhereNull('module_number');
            })
            ->latest()
            ->first();

        $questions = TestQuestion::where('module_target', $module->module_number)->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'module_target' => $q->module_target,
                'question_text' => $q->question_text,
                'options' => $q->options,
            ];
        });

        if ($questions->isEmpty()) {
            $questions = TestQuestion::all()->map(function ($q) {
                return [
                    'id' => $q->id,
                    'module_target' => $q->module_target,
                    'question_text' => $q->question_text,
                    'options' => $q->options,
                ];
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Soal Post-Test Modul '.$module->module_number.' berhasil dimuat.',
            'data' => [
                'module_id' => $module->id,
                'module_number' => $module->module_number,
                'module_title' => $module->title,
                'pretest_score' => $pretest ? $pretest->total_score : null,
                'total_questions' => $questions->count(),
                'questions' => $questions,
            ],
        ]);
    }

    /**
     * Submit Jawaban Post-Test Modul & Hitung N-Gain
     */
    public function submitPosttest(Request $request, $moduleId)
    {
        $user = $request->user();
        $module = EducationalModule::findOrFail($moduleId);
        $questions = TestQuestion::where('module_target', $module->module_number)->get();

        if ($questions->isEmpty()) {
            $questions = TestQuestion::all();
        }

        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Format jawaban tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $answers = $request->input('answers');
        $correctCount = 0;

        foreach ($questions as $q) {
            $userAns = $answers[$q->id] ?? null;
            if ($userAns && strtoupper($userAns) === strtoupper($q->correct_answer)) {
                $correctCount++;
            }
        }

        $postScore = count($questions) > 0 ? round(($correctCount / count($questions)) * 100) : 0;

        $pretest = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'pre_test')
            ->where(function ($q) use ($module) {
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
            'raw_answers' => $answers,
            'scores_per_module' => ['Modul '.$module->module_number => $postScore],
            'total_score' => $postScore,
            'n_gain_score' => $nGain,
            'submitted_at' => now(),
        ]);

        $user->increment('points', 50);
        $user->update(['posttest_completed' => true]);

        GoogleSheetsSyncService::syncPosttestSubmission($user, $posttest, $nGain);

        return response()->json([
            'success' => true,
            'message' => 'Post-Test Modul '.$module->module_number.' selesai! Peningkatan skor berhasil dihitung.',
            'data' => [
                'module_id' => $module->id,
                'module_number' => $module->module_number,
                'pretest_score' => $preScore,
                'posttest_score' => $postScore,
                'n_gain_score' => $nGain,
                'effectiveness_category' => $nGain >= 0.7 ? 'Tinggi' : ($nGain >= 0.3 ? 'Sedang' : 'Rendah'),
                'points_earned' => 50,
            ],
        ]);
    }

    /**
     * Hapus Modul Edukasi (Khusus Admin)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang memiliki hak akses untuk menghapus modul edukasi.',
            ], 403);
        }

        $module = EducationalModule::find($id);

        if (! $module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul edukasi tidak ditemukan.',
            ], 404);
        }

        $moduleNumber = $module->module_number;
        $title = $module->title;

        // Bersihkan soal kuesioner terkait modul ini
        TestQuestion::where('module_target', $moduleNumber)->delete();

        // Hapus modul (module_topics dan module_progress otomatis terhapus via foreign key cascade)
        $module->delete();

        return response()->json([
            'success' => true,
            'message' => "Modul #{$moduleNumber} (\"{$title}\") beserta seluruh submateri berhasil dihapus.",
        ]);
    }

    /**
     * Unggah & Import Modul Edukasi via Excel (Khusus Admin)
     */
    public function importExcel(Request $request, ModuleExcelImportService $importService)
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang memiliki hak akses untuk mengimpor modul edukasi.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:15360'],
            'overwrite' => ['nullable'],
        ], [
            'excel_file.required' => 'Pilih file Excel (.xlsx) terlebih dahulu.',
            'excel_file.file' => 'Berkas yang diunggah tidak valid.',
            'excel_file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
            'excel_file.max' => 'Ukuran file Excel maksimal 15 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi file gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $overwrite = $request->has('overwrite') ? filter_var($request->input('overwrite'), FILTER_VALIDATE_BOOLEAN) : true;
            $result = $importService->import($request->file('excel_file')->getRealPath(), $overwrite);

            return response()->json([
                'success' => true,
                'message' => 'File Excel modul edukasi berhasil diimpor.',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor file Excel: '.$e->getMessage(),
            ], 400);
        }
    }
}
