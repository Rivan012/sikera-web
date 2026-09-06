<?php

namespace App\Http\Controllers;

use App\Models\EducationalModule;
use App\Models\KesproFeed;
use App\Models\PeriodLog;
use App\Models\BmiLog;
use App\Models\ForumThread;
use App\Models\CaseStudy;
use App\Models\EvaluationResponse;
use Illuminate\Support\Facades\Auth;

class MahasiswaDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Modul Edukasi & Video
        $modules = EducationalModule::withCount('topics')->orderBy('module_number')->get();

        // 2. Projek Visual Poster (KesproFeed)
        $posters = KesproFeed::latest()->take(3)->get();

        // 3. Kalender Haid & IMT
        $latestPeriod = PeriodLog::where('user_id', $user->id)->latest()->first();
        $latestBmi = BmiLog::where('user_id', $user->id)->latest()->first();

        // 4. Diskusi Anonim & Kasus
        $recentThreads = ForumThread::withCount('replies')->latest()->take(3)->get();
        $caseStudies = CaseStudy::latest()->take(2)->get();

        // Status Evaluasi
        $pretest = EvaluationResponse::where('user_id', $user->id)->where('type', 'pre_test')->latest()->first();
        $posttest = EvaluationResponse::where('user_id', $user->id)->where('type', 'post_test')->latest()->first();

        return view('mahasiswa.dashboard', compact(
            'user',
            'modules',
            'posters',
            'latestPeriod',
            'latestBmi',
            'recentThreads',
            'caseStudies',
            'pretest',
            'posttest'
        ));
    }
}
