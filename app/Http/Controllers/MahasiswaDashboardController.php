<?php

namespace App\Http\Controllers;

use App\Models\BmiLog;
use App\Models\CaseStudy;
use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\ForumThread;
use App\Models\KesproFeed;
use App\Models\ModuleProgress;
use App\Models\PeriodLog;
use App\Services\EvaluationEligibilityService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MahasiswaDashboardController extends Controller
{
    public function index(EvaluationEligibilityService $eligibility): Response
    {
        $user = Auth::user();
        $modules = EducationalModule::with(['topics:id,educational_module_id'])
            ->withCount('topics')
            ->orderBy('module_number')
            ->get();

        $allTopicIds = $modules->flatMap(fn (EducationalModule $m) => $m->topics->pluck('id'));
        $completedTopicIds = ModuleProgress::where('user_id', $user->id)
            ->whereIn('module_topic_id', $allTopicIds)
            ->where('is_completed', true)
            ->pluck('module_topic_id')
            ->flip();

        $moduleItems = $modules->map(function (EducationalModule $m) use ($user, $completedTopicIds): array {
            $doneCount = $m->topics->filter(fn ($t): bool => $completedTopicIds->has($t->id))->count();
            $hasPretest = EvaluationResponse::where('user_id', $user->id)
                ->where('type', 'pre_test')
                ->where(function ($q) use ($m): void {
                    $q->where('module_number', $m->module_number)
                        ->orWhereNull('module_number');
                })
                ->exists();

            return [
                'id' => $m->id,
                'module_number' => $m->module_number,
                'title' => $m->title,
                'estimated_time' => $m->estimated_time,
                'topics_count' => $m->topics_count,
                'completed_topics' => $doneCount,
                'has_pretest' => $hasPretest,
            ];
        });

        $posters = KesproFeed::latest()->take(3)->get();
        $latestPeriod = PeriodLog::where('user_id', $user->id)->latest()->first();
        $latestBmi = BmiLog::where('user_id', $user->id)->latest()->first();
        $recentThreads = ForumThread::withCount('replies')->latest()->take(3)->get();
        $caseStudies = CaseStudy::latest()->take(2)->get();

        $posttestGate = $eligibility->syncAllModulesCompletion($user);
        $avgPreScore = $eligibility->calculateAggregatePretestScore($user);

        return Inertia::render('Mahasiswa/Dashboard', [
            'modules' => $moduleItems,
            'posters' => $posters,
            'latestPeriod' => $latestPeriod ? [
                'start_date' => $latestPeriod->start_date->format('d M Y'),
                'cycle_length' => $latestPeriod->cycle_length,
                'flow_level' => $latestPeriod->flow_level,
                'nrs_pain_score' => $latestPeriod->nrs_pain_score,
            ] : null,
            'latestBmi' => $latestBmi ? [
                'bmi_value' => $latestBmi->bmi_value,
                'category' => $latestBmi->category,
                'advice' => $latestBmi->advice,
            ] : null,
            'recentThreads' => $recentThreads,
            'caseStudies' => $caseStudies,
            'avgPreScore' => $avgPreScore,
            'posttestGate' => $posttestGate,
        ]);
    }
}
