<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EvaluationResponse;
use App\Models\EducationalModule;
use App\Models\KesproFeed;
use App\Models\ForumThread;
use App\Models\CaseStudy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DosenDashboardController extends Controller
{
    public function index()
    {
        $dosen = Auth::user();

        // 1. Pantau Rata-rata Skor Mahasiswa
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $pretestDone = User::where('role', 'mahasiswa')->where('pretest_completed', true)->count();
        $posttestDone = User::where('role', 'mahasiswa')->where('posttest_completed', true)->count();

        $avgPre = EvaluationResponse::where('type', 'pre_test')->avg('total_score') ?: 0;
        $avgPost = EvaluationResponse::where('type', 'post_test')->avg('total_score') ?: 0;
        $avgNGain = EvaluationResponse::where('type', 'post_test')->whereNotNull('n_gain_score')->avg('n_gain_score') ?: 0;

        $fakultasBreakdown = User::where('role', 'mahasiswa')
            ->select('fakultas', DB::raw('count(*) as total_mhs'), DB::raw('sum(case when pretest_completed=1 then 1 else 0 end) as pre_done'), DB::raw('sum(case when posttest_completed=1 then 1 else 0 end) as post_done'))
            ->groupBy('fakultas')
            ->get();

        // 2. Bahan Bimbingan & Konseling Kampus
        $modules = EducationalModule::with('topics')->get();
        $posters = KesproFeed::all();
        $caseStudies = CaseStudy::all();

        // Forum konsultasi yang membutuhkan respon konselor
        $unansweredThreads = ForumThread::withCount('replies')
            ->where('is_answered_by_counselor', false)
            ->latest()
            ->take(5)
            ->get();

        return view('dosen.dashboard', compact(
            'dosen',
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
