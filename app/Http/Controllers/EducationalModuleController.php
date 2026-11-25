<?php

namespace App\Http\Controllers;

use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\ModuleProgress;
use App\Models\ModuleTopic;
use App\Models\User;
use App\Services\EvaluationEligibilityService;
use App\Services\ModuleTopicPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
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

    public function index(EvaluationEligibilityService $eligibility): Response
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

        $moduleItems = [];
        foreach ($modules as $module) {
            $completedCount = $module->topics->filter(
                fn (ModuleTopic $topic): bool => $completedTopicIds->has($topic->id)
            )->count();

            $pdfCount = $module->topics->filter(
                fn (ModuleTopic $topic): bool => $topic->hasPdf()
            )->count();

            $hasPretest = $this->hasCompletedPretest($user, $module);
            $percentage = $module->topics_count > 0 ? (int) round(($completedCount / $module->topics_count) * 100) : 0;
            $allTopicsDone = ($completedCount >= $module->topics_count) && $module->topics_count > 0;

            $moduleItems[] = [
                'id' => $module->id,
                'module_number' => $module->module_number,
                'title' => $module->title,
                'subtitle' => $module->subtitle,
                'description' => $module->description,
                'banner_image' => $module->banner_image ?: '/images/banners/banner-module-'.(($module->module_number % 4) ?: 4).'.svg',
                'estimated_time' => $module->estimated_time,
                'completed' => $completedCount,
                'total' => $module->topics_count,
                'pdf_count' => $pdfCount,
                'percentage' => $percentage,
                'has_pretest' => $hasPretest,
                'all_topics_done' => $allTopicsDone,
            ];
        }

        return Inertia::render('Modules/Index', [
            'modules' => $moduleItems,
            'posttestGate' => $eligibility->syncAllModulesCompletion($user),
        ]);
    }

    public function show(int $id, EvaluationEligibilityService $eligibility): Response|RedirectResponse
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

        $topics = $module->topics->map(fn (ModuleTopic $topic): array => [
            'id' => $topic->id,
            'topic_code' => $topic->topic_code,
            'title' => $topic->title,
            'has_pdf' => $topic->hasPdf(),
            'pdf_size_label' => $topic->formattedPdfSize(),
            'has_video' => filled($topic->youtube_video_id),
            'is_completed' => in_array($topic->id, $completedTopicIds, true),
        ]);

        return Inertia::render('Modules/Show', [
            'module' => [
                'id' => $module->id,
                'module_number' => $module->module_number,
                'title' => $module->title,
                'subtitle' => $module->subtitle,
                'description' => $module->description,
                'banner_image' => $module->banner_image ?: '/images/banners/banner-module-'.(($module->module_number % 4) ?: 4).'.svg',
                'estimated_time' => $module->estimated_time,
                'topics_count' => $module->topics->count(),
                'pdf_count' => $module->topics->filter(fn (ModuleTopic $t): bool => $t->hasPdf())->count(),
            ],
            'topics' => $topics,
            'allTopicsDone' => $allTopicsDone,
            'posttestGate' => $eligibility->syncAllModulesCompletion($user),
        ]);
    }

    public function showTopic(
        int $moduleId,
        int $topicId,
        ModuleTopicPdfService $pdfService,
        EvaluationEligibilityService $eligibility
    ): Response|RedirectResponse {
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

        $posttestGate = $eligibility->syncAllModulesCompletion($user);

        $allTopics = ModuleTopic::where('educational_module_id', $moduleId)->orderBy('order_index')->get();
        $currentIndex = $allTopics->search(fn (ModuleTopic $t): bool => $t->id === $topic->id);
        $prevTopic = $currentIndex > 0 ? $allTopics[$currentIndex - 1] : null;
        $nextTopic = $currentIndex < $allTopics->count() - 1 ? $allTopics[$currentIndex + 1] : null;

        $hasPdfFile = $pdfService->exists($topic->pdf_path);
        $pdfUrl = $hasPdfFile ? route('modules.topic.pdf', [$module->id, $topic->id]) : null;

        return Inertia::render('Modules/Topic', [
            'module' => [
                'id' => $module->id,
                'module_number' => $module->module_number,
                'title' => $module->title,
            ],
            'topic' => [
                'id' => $topic->id,
                'topic_code' => $topic->topic_code,
                'title' => $topic->title,
                'pdf_original_name' => $topic->pdf_original_name,
                'pdf_size_label' => $topic->formattedPdfSize(),
                'pdf_version' => (string) ($topic->updated_at?->timestamp ?? $topic->pdf_size ?? 1),
                'youtube_video_id' => $topic->youtube_video_id,
                'content_html' => $topic->content_html,
            ],
            'allTopics' => $allTopics->map(fn (ModuleTopic $item): array => [
                'id' => $item->id,
                'topic_code' => $item->topic_code,
                'title' => $item->title,
            ]),
            'prevTopic' => $prevTopic ? [
                'id' => $prevTopic->id,
                'topic_code' => $prevTopic->topic_code,
                'title' => $prevTopic->title,
            ] : null,
            'nextTopic' => $nextTopic ? [
                'id' => $nextTopic->id,
                'topic_code' => $nextTopic->topic_code,
                'title' => $nextTopic->title,
            ] : null,
            'hasPdfFile' => $hasPdfFile,
            'pdfUrl' => $pdfUrl,
            'posttestGate' => $posttestGate,
        ]);
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
