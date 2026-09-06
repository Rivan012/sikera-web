<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EducationalModule;
use App\Models\KesproFeed;
use App\Models\PeriodLog;
use App\Models\BmiLog;
use App\Models\ForumThread;
use App\Models\CaseStudy;
use App\Models\EvaluationResponse;
use App\Models\ModuleProgress;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Ringkasan Beranda / Dashboard Mobile Mahasiswa
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isFemale = ($user->gender === 'P');

        // 1. Modul Pembelajaran & Progress
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

            return [
                'id' => $mod->id,
                'module_number' => $mod->module_number,
                'title' => $mod->title,
                'subtitle' => $mod->subtitle,
                'banner_image' => url($mod->banner_image ?: "/images/banners/banner-module-{$mod->module_number}.svg"),
                'estimated_time' => $mod->estimated_time,
                'total_topics' => $mod->topics_count,
                'completed_topics' => $completedCount,
                'progress_percentage' => $mod->topics_count > 0 ? round(($completedCount / $mod->topics_count) * 100) : 0,
                'has_pretest' => $hasPretest,
                'has_posttest' => (bool)$posttest,
                'n_gain' => $posttest ? $posttest->n_gain_score : null,
            ];
        });

        // 2. Poster Kaspro Pilihan
        $posters = KesproFeed::latest()->take(3)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'category' => $p->category,
                'image_url' => url($p->image_path),
                'caption' => $p->caption,
                'download_count' => $p->download_count,
                'share_count' => $p->share_count,
            ];
        });

        // 3. Self-Care Terakhir
        $latestPeriod = null;
        if ($isFemale) {
            $period = PeriodLog::where('user_id', $user->id)->latest()->first();
            if ($period) {
                $latestPeriod = [
                    'start_date' => $period->start_date->format('Y-m-d'),
                    'cycle_length' => $period->cycle_length,
                    'flow_level' => $period->flow_level,
                    'nrs_pain_score' => $period->nrs_pain_score,
                ];
            }
        }

        $latestBmi = null;
        $bmi = BmiLog::where('user_id', $user->id)->latest()->first();
        if ($bmi) {
            $latestBmi = [
                'bmi_value' => $bmi->bmi_value,
                'category' => $bmi->category,
                'advice' => $bmi->advice,
                'measured_at' => $bmi->created_at->format('Y-m-d H:i'),
            ];
        }

        // 4. Kasus & Forum Terkini
        $recentThreadsCount = ForumThread::count();
        $caseStudiesCount = CaseStudy::count();

        return response()->json([
            'success' => true,
            'message' => 'Data beranda berhasil dimuat.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'initials' => $user->initials,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'points' => $user->points,
                    'streak_days' => $user->streak_days,
                ],
                'modules' => $modules,
                'posters' => $posters,
                'selfcare' => [
                    'is_female' => $isFemale,
                    'latest_period' => $latestPeriod,
                    'latest_bmi' => $latestBmi,
                ],
                'stats' => [
                    'total_threads' => $recentThreadsCount,
                    'total_cases' => $caseStudiesCount,
                ],
            ],
        ]);
    }
}
