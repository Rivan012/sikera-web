<?php

namespace App\Http\Controllers;

use App\Models\GoogleSheetSyncLog;
use App\Models\User;
use App\Services\EvaluationEligibilityService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminSheetController extends Controller
{
    public function index(EvaluationEligibilityService $eligibility): Response
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $syncLogs = GoogleSheetSyncLog::with('user')->latest()->take(20)->get();
        $totalSyncCount = GoogleSheetSyncLog::count();
        $successSyncCount = GoogleSheetSyncLog::where('sync_status', 'success')->count();

        $respondenData = User::where('role', 'mahasiswa')
            ->with(['evaluations' => fn ($q) => $q->latest()])
            ->get()
            ->map(function (User $mhs) use ($eligibility): array {
                $preScore = $eligibility->calculateAggregatePretestScore($mhs);
                $hasPre = $mhs->evaluations->contains('type', 'pre_test');
                $post = $mhs->evaluations->firstWhere('type', 'post_test');

                return [
                    'nim' => $mhs->nim ?? '-',
                    'nama_inisial' => $mhs->initials ?? $mhs->name,
                    'usia' => $mhs->usia ?? 18,
                    'gender' => $mhs->gender === 'L' ? 'L' : 'P',
                    'agama' => $mhs->agama ?? 'Islam',
                    'prodi' => $mhs->prodi ?? '-',
                    'fakultas' => $mhs->fakultas ?? '-',
                    'pendidikan_terakhir' => $mhs->pendidikan_terakhir ?? 'SMA/SMK Sederajat',
                    'skor_pretest' => $hasPre ? $preScore : '-',
                    'skor_posttest' => $post ? $post->total_score : '-',
                    'n_gain' => $post && $post->n_gain_score !== null ? $post->n_gain_score : '-',
                    'status' => $post ? 'Lengkap' : ($hasPre ? 'Pre-Test Selesai' : 'Belum Mulai'),
                ];
            });

        return Inertia::render('Admin/Sheets/Index', compact(
            'syncLogs',
            'totalSyncCount',
            'successSyncCount',
            'respondenData'
        ));
    }
}
