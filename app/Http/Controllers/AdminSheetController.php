<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GoogleSheetSyncLog;
use Illuminate\Http\Request;

class AdminSheetController extends Controller
{
    public function index()
    {
        $syncLogs = GoogleSheetSyncLog::with('user')->latest()->take(20)->get();
        $totalSyncCount = GoogleSheetSyncLog::count();
        $successSyncCount = GoogleSheetSyncLog::where('sync_status', 'success')->count();

        // Rekap Data Lengkap Responden untuk Spreadsheet View
        $respondenData = User::where('role', 'mahasiswa')
            ->with(['evaluations' => fn($q) => $q->latest()])
            ->get()
            ->map(function ($mhs) {
                $pre = $mhs->evaluations->firstWhere('type', 'pre_test');
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
                    'skor_pretest' => $pre ? $pre->total_score : '-',
                    'skor_posttest' => $post ? $post->total_score : '-',
                    'n_gain' => $post && $post->n_gain_score !== null ? $post->n_gain_score : '-',
                    'status' => $post ? 'Lengkap' : ($pre ? 'Pre-Test Selesai' : 'Belum Mulai'),
                ];
            });

        return view('admin.sheets.index', compact(
            'syncLogs',
            'totalSyncCount',
            'successSyncCount',
            'respondenData'
        ));
    }
}
