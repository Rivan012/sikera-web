<?php

namespace App\Http\Controllers;

use App\Models\EducationalModule;
use App\Models\ModuleTopic;
use App\Models\ModuleProgress;
use App\Models\EvaluationResponse;
use Illuminate\Support\Facades\Auth;

class EducationalModuleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $modules = EducationalModule::withCount('topics')->orderBy('module_number')->get();

        $moduleStats = [];
        foreach ($modules as $module) {
            $topicIds = $module->topics->pluck('id');
            $completedCount = ModuleProgress::where('user_id', $user->id)
                ->whereIn('module_topic_id', $topicIds)
                ->where('is_completed', true)
                ->count();

            // Cek status Pre-Test modul ini
            $hasPretest = $user->isAdmin() || $user->isDosen() || EvaluationResponse::where('user_id', $user->id)
                ->where('type', 'pre_test')
                ->where(function($q) use ($module) {
                    $q->where('module_number', $module->module_number)
                      ->orWhereNull('module_number');
                })
                ->exists();

            // Cek status Post-Test modul ini
            $posttest = EvaluationResponse::where('user_id', $user->id)
                ->where('type', 'post_test')
                ->where('module_number', $module->module_number)
                ->latest()
                ->first();

            $percentage = $module->topics_count > 0 ? round(($completedCount / $module->topics_count) * 100) : 0;
            $allTopicsDone = ($completedCount >= $module->topics_count) && $module->topics_count > 0;

            $moduleStats[$module->id] = [
                'completed' => $completedCount,
                'total' => $module->topics_count,
                'percentage' => $percentage,
                'has_pretest' => $hasPretest,
                'all_topics_done' => $allTopicsDone,
                'has_posttest' => (bool)$posttest,
                'posttest_score' => $posttest ? $posttest->total_score : null,
                'n_gain' => $posttest ? $posttest->n_gain_score : null,
            ];
        }

        return view('modules.index', compact('modules', 'moduleStats'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $module = EducationalModule::with(['topics' => fn($q) => $q->orderBy('order_index')])->findOrFail($id);

        // Jika mahasiswa dan belum mengerjakan Pre-Test modul ini -> Gating ke Pre-Test
        if ($user->isMahasiswa()) {
            $hasPretest = EvaluationResponse::where('user_id', $user->id)
                ->where('type', 'pre_test')
                ->where(function($q) use ($module) {
                    $q->where('module_number', $module->module_number)
                      ->orWhereNull('module_number');
                })
                ->exists();

            if (!$hasPretest) {
                return redirect()->route('modules.pretest.show', $module->id)
                    ->with('warning', 'Materi Modul ' . $module->module_number . ' terkunci! Anda wajib mengerjakan Pre-Test Modul ' . $module->module_number . ' terlebih dahulu.');
            }
        }

        $completedTopicIds = ModuleProgress::where('user_id', $user->id)
            ->whereIn('module_topic_id', $module->topics->pluck('id'))
            ->where('is_completed', true)
            ->pluck('module_topic_id')
            ->toArray();

        $allTopicsDone = count($completedTopicIds) >= $module->topics->count() && $module->topics->count() > 0;

        $posttest = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'post_test')
            ->where('module_number', $module->module_number)
            ->latest()
            ->first();

        return view('modules.show', compact('module', 'completedTopicIds', 'allTopicsDone', 'posttest'));
    }

    public function showTopic($moduleId, $topicId)
    {
        $user = Auth::user();
        $module = EducationalModule::findOrFail($moduleId);
        $topic = ModuleTopic::where('educational_module_id', $moduleId)->findOrFail($topicId);

        // Jika mahasiswa dan belum mengerjakan Pre-Test modul ini -> Gating ke Pre-Test
        if ($user->isMahasiswa()) {
            $hasPretest = EvaluationResponse::where('user_id', $user->id)
                ->where('type', 'pre_test')
                ->where(function($q) use ($module) {
                    $q->where('module_number', $module->module_number)
                      ->orWhereNull('module_number');
                })
                ->exists();

            if (!$hasPretest) {
                return redirect()->route('modules.pretest.show', $module->id)
                    ->with('warning', 'Silakan selesaikan Pre-Test Modul ' . $module->module_number . ' terlebih dahulu untuk membaca materi.');
            }
        }

        // Tandai materi telah selesai dibaca
        $progress = ModuleProgress::firstOrCreate(
            ['user_id' => $user->id, 'module_topic_id' => $topic->id],
            ['is_completed' => false]
        );

        if (!$progress->is_completed) {
            $progress->update(['is_completed' => true, 'completed_at' => now()]);
            $user->increment('points', 10);
        }

        // Prev/Next navigation
        $allTopics = ModuleTopic::where('educational_module_id', $moduleId)->orderBy('order_index')->get();
        $currentIndex = $allTopics->search(fn($t) => $t->id === $topic->id);
        $prevTopic = $currentIndex > 0 ? $allTopics[$currentIndex - 1] : null;
        $nextTopic = $currentIndex < $allTopics->count() - 1 ? $allTopics[$currentIndex + 1] : null;

        // Cek apakah seluruh materi modul ini telah selesai dibaca
        $completedCount = ModuleProgress::where('user_id', $user->id)
            ->whereIn('module_topic_id', $allTopics->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $allTopicsCompleted = $completedCount >= $allTopics->count();

        return view('modules.topic', compact('module', 'topic', 'prevTopic', 'nextTopic', 'allTopicsCompleted'));
    }
}
