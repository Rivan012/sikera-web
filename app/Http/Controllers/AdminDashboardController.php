<?php

namespace App\Http\Controllers;

use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\ForumThread;
use App\Models\PeriodLog;
use App\Models\TestQuestion;
use App\Models\User;
use App\Models\UserDailyActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(): Response
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $pretestDone = User::where('role', 'mahasiswa')->where('pretest_completed', true)->count();
        $posttestDone = User::where('role', 'mahasiswa')->where('posttest_completed', true)->count();
        $totalDosen = User::where('role', 'dosen_pa')->count();

        $participationRate = $totalMahasiswa > 0
            ? round(($pretestDone / $totalMahasiswa) * 100, 1)
            : 0;

        $avgNGain = round((float) (EvaluationResponse::where('type', 'post_test')
            ->whereNotNull('n_gain_score')
            ->avg('n_gain_score') ?: 0), 3);

        $nGainCategory = $avgNGain >= 0.7 ? 'Tinggi' : ($avgNGain >= 0.3 ? 'Sedang' : 'Rendah');

        $avgPreScore = round((float) (EvaluationResponse::where('type', 'pre_test')->avg('total_score') ?: 0), 1);
        $avgPostScore = round((float) (EvaluationResponse::where('type', 'post_test')->avg('total_score') ?: 0), 1);

        $modules = EducationalModule::orderBy('module_number')->get();
        $scoresByModule = [];
        foreach ($modules as $mod) {
            $preAvg = EvaluationResponse::where('type', 'pre_test')
                ->where('module_number', $mod->module_number)
                ->avg('total_score');

            if ($preAvg === null) {
                $key = 'Modul '.$mod->module_number;
                $preAvg = EvaluationResponse::where('type', 'pre_test')
                    ->whereNull('module_number')
                    ->get()
                    ->pluck('scores_per_module')
                    ->map(fn ($s) => is_array($s) && isset($s[$key]) ? $s[$key] : null)
                    ->filter(fn ($v) => $v !== null)
                    ->avg();
            }

            $scoresByModule[] = [
                'modul' => 'Modul '.$mod->module_number,
                'pre' => round((float) ($preAvg ?? 0), 1),
                'post' => $avgPostScore,
            ];
        }

        $fakultasStats = User::where('role', 'mahasiswa')
            ->where('pretest_completed', true)
            ->select('fakultas', DB::raw('count(*) as total'))
            ->groupBy('fakultas')
            ->get();

        $recentEvaluations = EvaluationResponse::with('user')
            ->latest('submitted_at')
            ->take(10)
            ->get()
            ->map(fn (EvaluationResponse $ev): array => [
                'id' => $ev->id,
                'type' => $ev->type,
                'module_number' => $ev->module_number,
                'total_score' => $ev->total_score,
                'n_gain_score' => $ev->n_gain_score,
                'submitted_at' => $ev->submitted_at?->format('d M Y, H:i'),
                'user_name' => $ev->user?->name ?? 'Mahasiswa',
                'user_nim' => $ev->user?->nim ?? '-',
            ]);

        return Inertia::render('Admin/Dashboard', [
            'totalMahasiswa' => $totalMahasiswa,
            'pretestDone' => $pretestDone,
            'posttestDone' => $posttestDone,
            'totalDosen' => $totalDosen,
            'participationRate' => $participationRate,
            'avgNGain' => $avgNGain,
            'nGainCategory' => $nGainCategory,
            'avgPreScore' => $avgPreScore,
            'avgPostScore' => $avgPostScore,
            'scoresByModule' => $scoresByModule,
            'fakultasStats' => $fakultasStats,
            'recentEvaluations' => $recentEvaluations,
            'totalPretestQuestions' => TestQuestion::preTest()->count(),
            'totalPosttestQuestions' => TestQuestion::postTest()->count(),
            'totalThreads' => ForumThread::count(),
            'unansweredThreads' => ForumThread::where('is_answered_by_counselor', false)->count(),
            'totalPeriodLogs' => PeriodLog::count(),
            'totalDailyActivities' => UserDailyActivity::count(),
        ]);
    }
}
