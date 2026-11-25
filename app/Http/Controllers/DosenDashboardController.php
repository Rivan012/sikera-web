<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\ForumThread;
use App\Models\KesproFeed;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DosenDashboardController extends Controller
{
    public function index(): Response
    {
        if (! Auth::check() || (! Auth::user()->isDosen() && ! Auth::user()->isAdmin())) {
            abort(403);
        }

        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $pretestDone = User::where('role', 'mahasiswa')->where('pretest_completed', true)->count();
        $posttestDone = User::where('role', 'mahasiswa')->where('posttest_completed', true)->count();

        $avgPre = round((float) (EvaluationResponse::where('type', 'pre_test')->avg('total_score') ?: 0), 1);
        $avgPost = round((float) (EvaluationResponse::where('type', 'post_test')->avg('total_score') ?: 0), 1);
        $avgNGain = round((float) (EvaluationResponse::where('type', 'post_test')->whereNotNull('n_gain_score')->avg('n_gain_score') ?: 0), 3);

        $fakultasBreakdown = User::where('role', 'mahasiswa')
            ->select(
                'fakultas',
                DB::raw('count(*) as total_mhs'),
                DB::raw('sum(case when pretest_completed=1 then 1 else 0 end) as pre_done'),
                DB::raw('sum(case when posttest_completed=1 then 1 else 0 end) as post_done')
            )
            ->groupBy('fakultas')
            ->get();

        $modules = EducationalModule::withCount('topics')->orderBy('module_number')->get();
        $posters = KesproFeed::latest()->take(6)->get();
        $caseStudies = CaseStudy::all();

        $unansweredThreads = ForumThread::withCount('replies')
            ->where('is_answered_by_counselor', false)
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dosen/Dashboard', compact(
            'totalMahasiswa',
            'pretestDone',
            'posttestDone',
            'avgPre',
            'avgPost',
            'avgNGain',
            'fakultasBreakdown',
            'modules',
            'posters',
            'caseStudies',
            'unansweredThreads'
        ));
    }
}
