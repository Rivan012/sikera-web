<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EvaluationResponse;
use App\Models\EducationalModule;
use App\Models\ForumThread;
use App\Models\PeriodLog;
use App\Models\KesproFeed;
use App\Models\UserDailyActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $pretestDone = User::where('role', 'mahasiswa')->where('pretest_completed', true)->count();
        $posttestDone = User::where('role', 'mahasiswa')->where('posttest_completed', true)->count();
        $totalDosen = User::where('role', 'dosen_pa')->count();

        $participationRate = $totalMahasiswa > 0
            ? round(($pretestDone / $totalMahasiswa) * 100, 1)
            : 0;

        $avgNGain = EvaluationResponse::where('type', 'post_test')
            ->whereNotNull('n_gain_score')
            ->avg('n_gain_score');
        $avgNGain = $avgNGain ? round($avgNGain, 3) : 0;

        $nGainCategory = 'Rendah';
        if ($avgNGain >= 0.7) {
            $nGainCategory = 'Tinggi';
        } elseif ($avgNGain >= 0.3) {
            $nGainCategory = 'Sedang';
        }

        $avgPreScore = EvaluationResponse::where('type', 'pre_test')->avg('total_score');
        $avgPostScore = EvaluationResponse::where('type', 'post_test')->avg('total_score');
        $avgPreScore = $avgPreScore ? round($avgPreScore, 1) : 0;
        $avgPostScore = $avgPostScore ? round($avgPostScore, 1) : 0;

        $scoresByModule = [];
        $preTests = EvaluationResponse::where('type', 'pre_test')->get();
        $postTests = EvaluationResponse::where('type', 'post_test')->get();

        for ($i = 1; $i <= 4; $i++) {
            $key = 'Modul ' . $i;
            $preAvg = $preTests->pluck('scores_per_module')->map(fn($s) => $s[$key] ?? 0)->avg();
            $postAvg = $postTests->pluck('scores_per_module')->map(fn($s) => $s[$key] ?? 0)->avg();
            $scoresByModule[] = [
                'modul' => $key,
                'pre' => round($preAvg ?? 0, 1),
                'post' => round($postAvg ?? 0, 1),
            ];
        }

        $fakultasStats = User::where('role', 'mahasiswa')
            ->where('pretest_completed', true)
            ->select('fakultas', DB::raw('count(*) as total'))
            ->groupBy('fakultas')
            ->get();

        $recentUsers = User::where('role', 'mahasiswa')
            ->latest()
            ->take(10)
            ->get();

        $recentEvaluations = EvaluationResponse::with('user')
            ->latest('submitted_at')
            ->take(10)
            ->get();

        $totalThreads = ForumThread::count();
        $unansweredThreads = ForumThread::where('is_answered_by_counselor', false)->count();
        $totalPeriodLogs = PeriodLog::count();
        $totalDailyActivities = UserDailyActivity::count();

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'pretestDone',
            'posttestDone',
            'totalDosen',
            'participationRate',
            'avgNGain',
            'nGainCategory',
            'avgPreScore',
            'avgPostScore',
            'scoresByModule',
            'fakultasStats',
            'recentUsers',
            'recentEvaluations',
            'totalThreads',
            'unansweredThreads',
            'totalPeriodLogs',
            'totalDailyActivities'
        ));
    }
}
