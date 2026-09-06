<?php

namespace App\Services;

use App\Models\User;
use App\Models\EvaluationResponse;
use App\Models\GoogleSheetSyncLog;
use Illuminate\Support\Facades\Log;

class GoogleSheetsSyncService
{
    /**
     * Sinkronisasi data demografi & skor Pre-Test ke Google Sheets
     */
    public static function syncPretestSubmission(User $user, EvaluationResponse $pretest): GoogleSheetSyncLog
    {
        $payload = [
            'timestamp' => now()->toIso8601String(),
            'nama_inisial' => $user->initials ?? $user->name,
            'nim' => $user->nim ?? '-',
            'usia' => $user->usia ?? 18,
            'jenis_kelamin' => $user->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            'agama' => $user->agama ?? 'Islam',
            'program_studi' => $user->prodi ?? '-',
            'fakultas' => $user->fakultas ?? '-',
            'pendidikan_terakhir' => $user->pendidikan_terakhir ?? 'SMA/SMK Sederajat',
            'skor_pretest' => $pretest->total_score,
            'rincian_skor_modul' => $pretest->scores_per_module,
            'status_riset' => 'Pre-Test Completed',
        ];

        // Simulasi sinkronisasi ke Webhook API / Google Sheets Apps Script
        $rowNumber = GoogleSheetSyncLog::count() + 2;
        $sheetRange = "Lembar_Data_Responden!A{$rowNumber}:L{$rowNumber}";

        return GoogleSheetSyncLog::create([
            'user_id' => $user->id,
            'event_type' => 'pre_test_submission',
            'student_identifier' => ($user->initials ?? $user->name) . ' (' . ($user->nim ?? 'NIM') . ')',
            'fakultas_prodi' => ($user->prodi ?? '-') . ' - ' . ($user->fakultas ?? '-'),
            'payload_data' => $payload,
            'sync_status' => 'success',
            'sheet_range' => $sheetRange,
            'synced_at' => now(),
        ]);
    }

    /**
     * Sinkronisasi pembaruan skor Post-Test & N-Gain ke Google Sheets
     */
    public static function syncPosttestSubmission(User $user, EvaluationResponse $posttest, $nGain): GoogleSheetSyncLog
    {
        $pretest = EvaluationResponse::where('user_id', $user->id)->where('type', 'pre_test')->latest()->first();

        $payload = [
            'timestamp' => now()->toIso8601String(),
            'nama_inisial' => $user->initials ?? $user->name,
            'nim' => $user->nim ?? '-',
            'usia' => $user->usia ?? 18,
            'jenis_kelamin' => $user->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            'program_studi' => $user->prodi ?? '-',
            'skor_pretest' => $pretest ? $pretest->total_score : 0,
            'skor_posttest' => $posttest->total_score,
            'n_gain_score' => $nGain,
            'kategori_efektivitas' => $nGain >= 0.7 ? 'Tinggi' : ($nGain >= 0.3 ? 'Sedang' : 'Rendah'),
            'status_riset' => 'Lengkap (Pre & Post Done)',
        ];

        $rowNumber = GoogleSheetSyncLog::count() + 2;
        $sheetRange = "Lembar_Data_Responden!M{$rowNumber}:Q{$rowNumber}";

        return GoogleSheetSyncLog::create([
            'user_id' => $user->id,
            'event_type' => 'post_test_submission',
            'student_identifier' => ($user->initials ?? $user->name) . ' (' . ($user->nim ?? 'NIM') . ')',
            'fakultas_prodi' => ($user->prodi ?? '-') . ' - ' . ($user->fakultas ?? '-'),
            'payload_data' => $payload,
            'sync_status' => 'success',
            'sheet_range' => $sheetRange,
            'synced_at' => now(),
        ]);
    }
}
