<?php

namespace App\Services;

use App\Models\EvaluationResponse;
use App\Models\TestQuestion;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class PostTestExcelExportService
{
    /**
     * Generate berkas Excel (.xlsx) untuk data Post-Test.
     *
     * @param  array<string, mixed>  $filters
     * @return array{path: string, filename: string}
     */
    public function export(array $filters = []): array
    {
        $data = $this->prepareDataset($filters);

        $timestamp = now()->format('Ymd_His');
        $filename = "Data_Post_Test_SIKERA_{$timestamp}.xlsx";
        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $outputXlsxPath = $tempDir.DIRECTORY_SEPARATOR.$filename;

        // 1. Prioritaskan pembuatan menggunakan Python openpyxl untuk visualisasi multi-sheet berkualitas tinggi
        $success = $this->generateWithPython($data, $outputXlsxPath);

        // 2. Jika Python tidak tersedia atau gagal, gunakan fallback generator mandiri
        if (! $success || ! file_exists($outputXlsxPath) || filesize($outputXlsxPath) === 0) {
            $this->generateFallbackXls($data, $outputXlsxPath);
        }

        return [
            'path' => $outputXlsxPath,
            'filename' => $filename,
        ];
    }

    /**
     * Mempersiapkan dataset Post-Test, Pre-Test matching, dan tabulasi butir soal.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function prepareDataset(array $filters = []): array
    {
        $query = EvaluationResponse::with('user')
            ->where('type', 'post_test');

        if (! empty($filters['module']) && $filters['module'] !== 'all') {
            if ($filters['module'] === 'general') {
                $query->whereNull('module_number');
            } else {
                $query->where('module_number', (int) $filters['module']);
            }
        }

        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->whereHas('user', function (Builder $builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('nim', 'like', "%{$q}%")
                    ->orWhere('initials', 'like', "%{$q}%")
                    ->orWhere('prodi', 'like', "%{$q}%");
            });
        }

        $posttests = $query->orderBy('submitted_at', 'desc')->get();

        // Ambil bank soal sesuai filter evaluasi
        if (! empty($filters['module']) && is_numeric($filters['module'])) {
            $questions = TestQuestion::preTest()->where('module_target', (int) $filters['module'])->orderBy('id')->get();
        } else {
            $questions = TestQuestion::postTest()->orderBy('id')->get();
            if ($questions->isEmpty()) {
                $questions = TestQuestion::orderBy('module_target')->orderBy('id')->get();
            }
        }

        if ($questions->isEmpty()) {
            $questions = TestQuestion::orderBy('id')->get();
        }

        $eligibilityService = app(EvaluationEligibilityService::class);
        $rekapRows = [];
        $jawabanRows = [];
        $binerRows = [];

        $no = 1;
        foreach ($posttests as $post) {
            $user = $post->user;
            $rawAnswers = $post->raw_answers ?? [];

            if ($post->module_number !== null) {
                $pretest = EvaluationResponse::where('user_id', $post->user_id)
                    ->where('type', 'pre_test')
                    ->where(function ($q) use ($post) {
                        $q->where('module_number', $post->module_number)
                            ->orWhereNull('module_number');
                    })
                    ->latest('submitted_at')
                    ->first();
                $preScore = $pretest ? $pretest->total_score : 0;
            } else {
                $preScore = $user ? $eligibilityService->calculateAggregatePretestScore($user) : 0;
            }
            $postScore = $post->total_score;
            $gain = $postScore - $preScore;

            $nGain = $post->n_gain_score;
            if ($nGain === null && (100 - $preScore) > 0) {
                $nGain = round($gain / (100 - $preScore), 3);
            }

            $effectiveness = 'Rendah';
            if ($nGain !== null) {
                if ($nGain >= 0.7) {
                    $effectiveness = 'Tinggi';
                } elseif ($nGain >= 0.3) {
                    $effectiveness = 'Sedang';
                }
            }

            $userAnswersList = [];
            $binaryScoresList = [];
            $correctCount = 0;

            foreach ($questions as $qItem) {
                $ans = isset($rawAnswers[$qItem->id]) ? strtoupper((string) $rawAnswers[$qItem->id]) : '-';
                $userAnswersList[] = $ans;

                $isCorrect = ($ans !== '-' && $ans === strtoupper((string) $qItem->correct_answer));
                if ($isCorrect) {
                    $correctCount++;
                    $binaryScoresList[] = 1;
                } else {
                    $binaryScoresList[] = 0;
                }
            }

            $modTargetText = $post->module_number ? "Modul {$post->module_number}" : 'Ujian Umum (4 Modul)';

            $rekapRows[] = [
                'no' => $no,
                'timestamp' => $post->submitted_at ? $post->submitted_at->format('Y-m-d H:i:s') : '-',
                'nim' => $user?->nim ?? '-',
                'name' => $user?->name ?? 'Mahasiswa',
                'initials' => $user?->initials ?? '-',
                'gender' => $user?->gender ?? '-',
                'usia' => $user?->usia ?? '-',
                'prodi' => $user?->prodi ?? '-',
                'fakultas' => $user?->fakultas ?? '-',
                'agama' => $user?->agama ?? '-',
                'pendidikan' => $user?->pendidikan_terakhir ?? '-',
                'module_target' => $modTargetText,
                'pre_score' => $preScore,
                'post_score' => $postScore,
                'gain' => $gain,
                'n_gain' => $nGain ?? 0.0,
                'effectiveness' => $effectiveness,
                'correct_count' => $correctCount,
                'total_questions' => count($questions),
            ];

            $jawabanRows[] = [
                'no' => $no,
                'nim' => $user?->nim ?? '-',
                'initials' => $user?->initials ?? '-',
                'name' => $user?->name ?? 'Mahasiswa',
                'module_target' => $modTargetText,
                'post_score' => $postScore,
                'answers' => $userAnswersList,
            ];

            $binerRows[] = [
                'no' => $no,
                'nim' => $user?->nim ?? '-',
                'initials' => $user?->initials ?? '-',
                'name' => $user?->name ?? 'Mahasiswa',
                'module_target' => $modTargetText,
                'correct_count' => $correctCount,
                'binary_scores' => $binaryScoresList,
            ];

            $no++;
        }

        $questionsList = $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'module_target' => $q->module_target,
                'question_text' => $q->question_text,
                'options' => $q->options ?? [],
                'correct_answer' => $q->correct_answer,
                'explanation' => $q->explanation ?? '',
            ];
        })->toArray();

        return [
            'rekap_rows' => $rekapRows,
            'questions' => $questionsList,
            'jawaban_rows' => $jawabanRows,
            'biner_rows' => $binerRows,
        ];
    }

    /**
     * Eksekusi script Python openpyxl untuk membuat file Excel berformat rapi.
     *
     * @param  array<string, mixed>  $data
     */
    protected function generateWithPython(array $data, string $outputPath): bool
    {
        $scriptPath = base_path('scripts/export_posttest_excel.py');
        if (! file_exists($scriptPath)) {
            return false;
        }

        $jsonTempPath = tempnam(sys_get_temp_dir(), 'sikera_posttest_');
        file_put_contents($jsonTempPath, json_encode($data, JSON_UNESCAPED_UNICODE));

        try {
            $pythonCmd = windows_os() ? 'python' : 'python3';
            $result = Process::timeout(30)->run([$pythonCmd, $scriptPath, $jsonTempPath, $outputPath]);

            if ($result->successful() && file_exists($outputPath) && filesize($outputPath) > 0) {
                @unlink($jsonTempPath);

                return true;
            }

            Log::warning('Python Excel exporter failed, falling back to native PHP generator: '.$result->errorOutput());
        } catch (Exception $e) {
            Log::warning('Python Excel exporter exception: '.$e->getMessage());
        } finally {
            if (file_exists($jsonTempPath)) {
                @unlink($jsonTempPath);
            }
        }

        return false;
    }

    /**
     * Fallback generator jika Python tidak dapat dijalankan (XML Spreadsheet 2003 kompatibel penuh dengan Excel).
     *
     * @param  array<string, mixed>  $data
     */
    protected function generateFallbackXls(array $data, string $outputPath): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>'."\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            .'xmlns:o="urn:schemas-microsoft-com:office:office" '
            .'xmlns:x="urn:schemas-microsoft-com:office:excel" '
            .'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" '
            .'xmlns:html="http://www.w3.org/TR/REC-html40">'."\n";

        // Styles
        $xml .= '<Styles>'."\n";
        $xml .= '<Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Center"/><Font ss:FontName="Calibri" ss:Size="10"/></Style>'."\n";
        $xml .= '<Style ss:ID="Header"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/></Borders><Font ss:FontName="Calibri" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/><Interior ss:Color="#0F766E" ss:Pattern="Solid"/></Style>'."\n";
        $xml .= '<Style ss:ID="CellCenter"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/></Borders></Style>'."\n";
        $xml .= '<Style ss:ID="CellLeft"><Alignment ss:Horizontal="Left" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/></Borders></Style>'."\n";
        $xml .= '<Style ss:ID="CellBoldCenter"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/></Borders><Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1"/></Style>'."\n";
        $xml .= '</Styles>'."\n";

        // Sheet 1: Rekap Nilai & N-Gain
        $xml .= '<Worksheet ss:Name="Rekap Nilai &amp; N-Gain">'."\n<Table>\n";
        $headers1 = ['No.', 'Timestamp', 'NIM', 'Nama Responden', 'Inisial', 'Gender', 'Usia', 'Program Studi', 'Fakultas', 'Agama', 'Pendidikan Terakhir', 'Target Ujian', 'Skor Pre-Test', 'Skor Post-Test', 'Gain', 'N-Gain Score', 'Kategori Efektivitas', 'Benar', 'Total Soal'];
        $xml .= '<Row ss:Height="24">'."\n";
        foreach ($headers1 as $h) {
            $xml .= '<Cell ss:StyleID="Header"><Data ss:Type="String">'.htmlspecialchars($h, ENT_XML1).'</Data></Cell>'."\n";
        }
        $xml .= "</Row>\n";

        foreach ($data['rekap_rows'] as $r) {
            $xml .= '<Row ss:Height="18">'."\n";
            $xml .= '<Cell ss:StyleID="CellCenter"><Data ss:Type="Number">'.$r['no'].'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellCenter"><Data ss:Type="String">'.htmlspecialchars((string) $r['timestamp'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellLeft"><Data ss:Type="String">'.htmlspecialchars((string) $r['nim'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellLeft"><Data ss:Type="String">'.htmlspecialchars((string) $r['name'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellCenter"><Data ss:Type="String">'.htmlspecialchars((string) $r['initials'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellCenter"><Data ss:Type="String">'.htmlspecialchars((string) $r['gender'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellCenter"><Data ss:Type="String">'.htmlspecialchars((string) $r['usia'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellLeft"><Data ss:Type="String">'.htmlspecialchars((string) $r['prodi'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellLeft"><Data ss:Type="String">'.htmlspecialchars((string) $r['fakultas'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellLeft"><Data ss:Type="String">'.htmlspecialchars((string) $r['agama'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellLeft"><Data ss:Type="String">'.htmlspecialchars((string) $r['pendidikan'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellCenter"><Data ss:Type="String">'.htmlspecialchars((string) $r['module_target'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellBoldCenter"><Data ss:Type="Number">'.$r['pre_score'].'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellBoldCenter"><Data ss:Type="Number">'.$r['post_score'].'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellBoldCenter"><Data ss:Type="Number">'.$r['gain'].'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellBoldCenter"><Data ss:Type="Number">'.$r['n_gain'].'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellCenter"><Data ss:Type="String">'.htmlspecialchars((string) $r['effectiveness'], ENT_XML1).'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellCenter"><Data ss:Type="Number">'.$r['correct_count'].'</Data></Cell>'."\n";
            $xml .= '<Cell ss:StyleID="CellCenter"><Data ss:Type="Number">'.$r['total_questions'].'</Data></Cell>'."\n";
            $xml .= "</Row>\n";
        }
        $xml .= "</Table>\n</Worksheet>\n";

        $xml .= "</Workbook>\n";

        file_put_contents($outputPath, $xml);
    }
}
