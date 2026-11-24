<?php

namespace App\Http\Controllers;

use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\ModuleProgress;
use App\Models\ModuleTopic;
use App\Models\User;
use App\Services\ModuleTopicPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EducationalModuleController extends Controller
{
    private function hasCompletedPretest(User $user, EducationalModule $module): bool
    {
        if ($user->isAdmin() || $user->isDosen()) {
            return true;
        }

        return EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'pre_test')
            ->where(function ($q) use ($module): void {
                $q->where('module_number', $module->module_number)
                    ->orWhereNull('module_number');
            })
            ->exists();
    }

    public function index(): View
    {
        $user = Auth::user();
        $modules = EducationalModule::with(['topics:id,educational_module_id,pdf_path'])
            ->withCount('topics')
            ->orderBy('module_number')
            ->get();

        $allTopicIds = $modules->flatMap(fn (EducationalModule $m) => $m->topics->pluck('id'));
        $completedTopicIds = ModuleProgress::where('user_id', $user->id)
            ->whereIn('module_topic_id', $allTopicIds)
            ->where('is_completed', true)
            ->pluck('module_topic_id')
            ->flip();

        $moduleStats = [];
        foreach ($modules as $module) {
            $completedCount = $module->topics->filter(
                fn (ModuleTopic $topic): bool => $completedTopicIds->has($topic->id)
            )->count();

            $pdfCount = $module->topics->filter(
                fn (ModuleTopic $topic): bool => $topic->hasPdf()
            )->count();

            $hasPretest = $this->hasCompletedPretest($user, $module);

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
                'pdf_count' => $pdfCount,
                'percentage' => $percentage,
                'has_pretest' => $hasPretest,
                'all_topics_done' => $allTopicsDone,
                'has_posttest' => (bool) $posttest,
                'posttest_score' => $posttest ? $posttest->total_score : null,
                'n_gain' => $posttest ? $posttest->n_gain_score : null,
            ];
        }

        return view('modules.index', compact('modules', 'moduleStats'));
    }

    public function show(int $id): View|RedirectResponse
    {
        $user = Auth::user();
        $module = EducationalModule::with(['topics' => fn ($q) => $q->orderBy('order_index')])->findOrFail($id);

        if ($user->isMahasiswa() && ! $this->hasCompletedPretest($user, $module)) {
            return redirect()->route('modules.pretest.show', $module->id)
                ->with('warning', 'Materi Modul '.$module->module_number.' terkunci. Kerjakan Pre-Test Modul '.$module->module_number.' terlebih dahulu.');
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

    public function showTopic(int $moduleId, int $topicId, ModuleTopicPdfService $pdfService): View|RedirectResponse
    {
        $user = Auth::user();
        $module = EducationalModule::findOrFail($moduleId);
        $topic = ModuleTopic::where('educational_module_id', $moduleId)->findOrFail($topicId);

        if ($user->isMahasiswa() && ! $this->hasCompletedPretest($user, $module)) {
            return redirect()->route('modules.pretest.show', $module->id)
                ->with('warning', 'Selesaikan Pre-Test Modul '.$module->module_number.' terlebih dahulu untuk membaca subbab.');
        }

        $progress = ModuleProgress::firstOrCreate(
            ['user_id' => $user->id, 'module_topic_id' => $topic->id],
            ['is_completed' => false]
        );

        if (! $progress->is_completed) {
            $progress->update(['is_completed' => true, 'completed_at' => now()]);
            $user->increment('points', 10);
        }

        $allTopics = ModuleTopic::where('educational_module_id', $moduleId)->orderBy('order_index')->get();
        $currentIndex = $allTopics->search(fn (ModuleTopic $t): bool => $t->id === $topic->id);
        $prevTopic = $currentIndex > 0 ? $allTopics[$currentIndex - 1] : null;
        $nextTopic = $currentIndex < $allTopics->count() - 1 ? $allTopics[$currentIndex + 1] : null;

        $completedTopicIds = ModuleProgress::where('user_id', $user->id)
            ->whereIn('module_topic_id', $allTopics->pluck('id'))
            ->where('is_completed', true)
            ->pluck('module_topic_id')
            ->toArray();

        $allTopicsCompleted = count($completedTopicIds) >= $allTopics->count();
        $hasPdfFile = $pdfService->exists($topic->pdf_path);
        $pdfUrl = $hasPdfFile ? route('modules.topic.pdf', [$module->id, $topic->id]) : null;

        return view('modules.topic', compact(
            'module',
            'topic',
            'allTopics',
            'completedTopicIds',
            'prevTopic',
            'nextTopic',
            'allTopicsCompleted',
            'hasPdfFile',
            'pdfUrl'
        ));
    }

    public function streamTopicPdf(
        Request $request,
        int $moduleId,
        int $topicId,
        ModuleTopicPdfService $pdfService
    ): BinaryFileResponse|RedirectResponse {
        $user = Auth::user();
        $module = EducationalModule::findOrFail($moduleId);
        $topic = ModuleTopic::where('educational_module_id', $moduleId)->findOrFail($topicId);

        if ($user->isMahasiswa() && ! $this->hasCompletedPretest($user, $module)) {
            if ($request->expectsJson() || $request->ajax()) {
                abort(403, 'Selesaikan Pre-Test Modul '.$module->module_number.' terlebih dahulu.');
            }

            return redirect()->route('modules.pretest.show', $module->id)
                ->with('warning', 'Selesaikan Pre-Test Modul '.$module->module_number.' terlebih dahulu untuk membuka berkas PDF materi.');
        }

        return $pdfService->streamResponse($topic);
    }
}
