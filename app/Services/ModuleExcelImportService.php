<?php

namespace App\Services;

use App\Models\EducationalModule;
use App\Models\ModuleTopic;
use App\Models\TestQuestion;
use Exception;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class ModuleExcelImportService
{
    /**
     * Import modul, submateri (topik), dan bank soal dari file Excel (.xlsx).
     *
     * @param  string  $filePath  Path absolut file .xlsx
     * @param  bool  $overwriteExisting  Apakah menimpa modul/topik jika nomor sudah ada
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function import(string $filePath, bool $overwriteExisting = true): array
    {
        if (! file_exists($filePath)) {
            throw new Exception("File Excel tidak ditemukan pada path: {$filePath}");
        }

        // 1. Coba parse menggunakan Python openpyxl jika tersedia
        $parsed = $this->parseWithPython($filePath);

        // 2. Jika Python gagal atau tidak tersedia, gunakan parser bawaan PHP ZipArchive
        if (! $parsed || empty($parsed['success'])) {
            $parsed = $this->parseWithNativeZip($filePath);
        }

        if (empty($parsed['modules']) && empty($parsed['topics']) && empty($parsed['questions'])) {
            throw new Exception('File Excel tidak memiliki data modul, submateri, atau soal yang valid sesuai format template.');
        }

        return DB::transaction(function () use ($parsed, $overwriteExisting) {
            $modulesCreated = 0;
            $modulesUpdated = 0;
            $topicsCreated = 0;
            $topicsUpdated = 0;
            $questionsCreated = 0;
            $questionsUpdated = 0;

            // 1. Proses Modul Utama
            $moduleMap = [];
            foreach ($parsed['modules'] as $modData) {
                $modNum = (int) $modData['module_number'];
                $existing = EducationalModule::where('module_number', $modNum)->first();

                if ($existing) {
                    if ($overwriteExisting) {
                        $existing->update([
                            'title' => $modData['title'],
                            'subtitle' => $modData['subtitle'] ?? $existing->subtitle,
                            'description' => $modData['description'],
                            'estimated_time' => $modData['estimated_time'] ?? $existing->estimated_time,
                            'banner_image' => $modData['banner_image'] ?: $existing->banner_image,
                            'badge_icon' => $modData['badge_icon'] ?: $existing->badge_icon,
                        ]);
                        $modulesUpdated++;
                    }
                    $moduleMap[$modNum] = $existing;
                } else {
                    $newMod = EducationalModule::create([
                        'module_number' => $modNum,
                        'title' => $modData['title'],
                        'subtitle' => $modData['subtitle'] ?? null,
                        'description' => $modData['description'],
                        'estimated_time' => $modData['estimated_time'] ?? '15 Menit',
                        'banner_image' => $modData['banner_image'] ?: null,
                        'badge_icon' => $modData['badge_icon'] ?: null,
                    ]);
                    $moduleMap[$modNum] = $newMod;
                    $modulesCreated++;
                }
            }

            // 2. Proses Submateri (Topik)
            foreach ($parsed['topics'] as $tpData) {
                $modTarget = (int) $tpData['module_target'];
                $module = $moduleMap[$modTarget] ?? EducationalModule::where('module_number', $modTarget)->first();

                if (! $module) {
                    continue;
                }

                $existingTopic = ModuleTopic::where('educational_module_id', $module->id)
                    ->where(function ($q) use ($tpData) {
                        $q->where('topic_code', $tpData['topic_code'])
                            ->orWhere('title', $tpData['title']);
                    })
                    ->first();

                if ($existingTopic) {
                    if ($overwriteExisting) {
                        $existingTopic->update([
                            'topic_code' => $tpData['topic_code'],
                            'title' => $tpData['title'],
                            'order_index' => $tpData['order_index'],
                            'youtube_video_id' => $tpData['youtube_video_id'] ?? $existingTopic->youtube_video_id,
                            'content_html' => $tpData['content_html'],
                        ]);
                        $topicsUpdated++;
                    }
                } else {
                    ModuleTopic::create([
                        'educational_module_id' => $module->id,
                        'topic_code' => $tpData['topic_code'],
                        'title' => $tpData['title'],
                        'order_index' => $tpData['order_index'],
                        'youtube_video_id' => $tpData['youtube_video_id'] ?? null,
                        'content_html' => $tpData['content_html'],
                    ]);
                    $topicsCreated++;
                }
            }

            // 3. Proses Bank Soal Kuesioner
            foreach ($parsed['questions'] as $qData) {
                $modTarget = (int) $qData['module_target'];

                $existingQ = TestQuestion::where('module_target', $modTarget)
                    ->where('question_text', $qData['question_text'])
                    ->first();

                if ($existingQ) {
                    if ($overwriteExisting) {
                        $existingQ->update([
                            'options' => $qData['options'],
                            'correct_answer' => $qData['correct_answer'],
                            'explanation' => $qData['explanation'] ?? $existingQ->explanation,
                        ]);
                        $questionsUpdated++;
                    }
                } else {
                    TestQuestion::create([
                        'module_target' => $modTarget,
                        'question_text' => $qData['question_text'],
                        'options' => $qData['options'],
                        'correct_answer' => $qData['correct_answer'],
                        'explanation' => $qData['explanation'] ?? null,
                    ]);
                    $questionsCreated++;
                }
            }

            return [
                'success' => true,
                'modules_created' => $modulesCreated,
                'modules_updated' => $modulesUpdated,
                'topics_created' => $topicsCreated,
                'topics_updated' => $topicsUpdated,
                'questions_created' => $questionsCreated,
                'questions_updated' => $questionsUpdated,
                'total_modules_in_file' => count($parsed['modules']),
                'total_topics_in_file' => count($parsed['topics']),
                'total_questions_in_file' => count($parsed['questions']),
            ];
        });
    }

    /**
     * Eksekusi parser Python scripts/parse_module_excel.py
     *
     * @return array<string, mixed>|null
     */
    protected function parseWithPython(string $filePath): ?array
    {
        $scriptPath = base_path('scripts/parse_module_excel.py');
        if (! file_exists($scriptPath)) {
            return null;
        }

        $command = 'python '.escapeshellarg($scriptPath).' '.escapeshellarg($filePath).' 2>&1';
        $output = shell_exec($command);

        if (! $output) {
            return null;
        }

        $decoded = json_decode(trim($output), true);
        if (is_array($decoded) && ! empty($decoded['success'])) {
            return $decoded;
        }

        return null;
    }

    /**
     * Parser cadangan menggunakan native PHP ZipArchive & SimpleXML
     *
     * @return array<string, mixed>
     */
    protected function parseWithNativeZip(string $filePath): array
    {
        if (! class_exists('ZipArchive')) {
            throw new Exception('Ekstensi PHP ZipArchive tidak terpasang dan Python parser tidak tersedia.');
        }

        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            throw new Exception('Gagal membaca struktur file Excel (.xlsx). Pastikan file tidak rusak.');
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml) {
            $xml = simplexml_load_string($sharedXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $workbookXml = simplexml_load_string($zip->getFromName('xl/workbook.xml') ?: '');
        $relsXml = simplexml_load_string($zip->getFromName('xl/_rels/workbook.xml.rels') ?: '');

        if (! $workbookXml || ! $relsXml) {
            $zip->close();
            throw new Exception('Struktur lembar kerja Excel tidak valid.');
        }

        $sheetTargets = [];
        foreach ($relsXml->Relationship as $rel) {
            $sheetTargets[(string) $rel['Id']] = (string) $rel['Target'];
        }

        $sheets = [];
        foreach ($workbookXml->sheets->sheet as $sheet) {
            $name = (string) $sheet['name'];
            $rId = (string) $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
            $target = $sheetTargets[$rId] ?? '';
            if (str_starts_with($target, '/')) {
                $target = substr($target, 1);
            } else {
                $target = 'xl/'.$target;
            }
            $sheets[$name] = $target;
        }

        $modules = [];
        $topics = [];
        $questions = [];

        foreach ($sheets as $sheetName => $sheetPath) {
            $xmlContent = $zip->getFromName($sheetPath);
            if (! $xmlContent) {
                continue;
            }
            $rows = $this->parseSheetRows($xmlContent, $sharedStrings);
            $lower = strtolower($sheetName);

            if (str_contains($lower, 'modul') && str_contains($lower, 'utama')) {
                foreach ($rows as $r) {
                    $modNum = $r[0] ?? null;
                    $title = trim($r[1] ?? '');
                    if (! is_numeric($modNum) || empty($title) || str_contains(strtolower($title), 'judul')) {
                        continue;
                    }
                    $modules[] = [
                        'module_number' => (int) $modNum,
                        'title' => $title,
                        'subtitle' => ! empty($r[2]) ? trim($r[2]) : null,
                        'description' => trim($r[3] ?? ''),
                        'estimated_time' => ! empty($r[4]) ? trim($r[4]) : '15 Menit',
                        'banner_image' => ! empty($r[5]) ? trim($r[5]) : null,
                        'badge_icon' => ! empty($r[6]) ? trim($r[6]) : null,
                    ];
                }
            } elseif (str_contains($lower, 'topik') || str_contains($lower, 'submateri')) {
                foreach ($rows as $r) {
                    $modTarget = $r[0] ?? null;
                    $title = trim($r[3] ?? '');
                    if (! is_numeric($modTarget) || empty($title) || str_contains(strtolower($title), 'judul')) {
                        continue;
                    }
                    $videoId = ! empty($r[4]) ? trim($r[4]) : null;
                    if ($videoId && (str_contains($videoId, 'youtube.com') || str_contains($videoId, 'youtu.be'))) {
                        if (preg_match('/(?:v=|youtu\.be\/)([\w\-]+)/', $videoId, $m)) {
                            $videoId = $m[1];
                        }
                    }
                    $topics[] = [
                        'module_target' => (int) $modTarget,
                        'topic_code' => ! empty($r[1]) ? trim($r[1]) : "{$modTarget}.1",
                        'order_index' => is_numeric($r[2] ?? null) ? (int) $r[2] : 1,
                        'title' => $title,
                        'youtube_video_id' => $videoId,
                        'content_html' => trim($r[5] ?? ''),
                    ];
                }
            } elseif (str_contains($lower, 'soal') || str_contains($lower, 'kuesioner')) {
                foreach ($rows as $r) {
                    $modTarget = $r[0] ?? null;
                    $qText = trim($r[2] ?? '');
                    if (! is_numeric($modTarget) || empty($qText) || str_contains(strtolower($qText), 'pertanyaan')) {
                        continue;
                    }
                    $correct = strtoupper(trim($r[7] ?? 'A'));
                    if (! in_array($correct, ['A', 'B', 'C', 'D'])) {
                        $correct = 'A';
                    }
                    $questions[] = [
                        'module_target' => (int) $modTarget,
                        'question_text' => $qText,
                        'options' => [
                            'A' => trim($r[3] ?? ''),
                            'B' => trim($r[4] ?? ''),
                            'C' => trim($r[5] ?? ''),
                            'D' => trim($r[6] ?? ''),
                        ],
                        'correct_answer' => $correct,
                        'explanation' => ! empty($r[8]) ? trim($r[8]) : null,
                    ];
                }
            }
        }

        $zip->close();

        return [
            'success' => true,
            'modules' => $modules,
            'topics' => $topics,
            'questions' => $questions,
        ];
    }

    /**
     * Parse row cells dari XML sheet Excel
     *
     * @param  array<int, string>  $sharedStrings
     * @return array<int, array<int, string>>
     */
    protected function parseSheetRows(string $xmlContent, array $sharedStrings): array
    {
        $xml = simplexml_load_string($xmlContent);
        if (! $xml || ! isset($xml->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowIdx = (int) $row['r'];
            $rowCells = [];
            foreach ($row->c as $c) {
                $cellRef = (string) $c['r'];
                preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches);
                $colLetter = $matches[1] ?? 'A';
                $colIdx = $this->colLetterToIndex($colLetter);

                $type = (string) $c['t'];
                if ($type === 's') {
                    $val = $sharedStrings[(int) $c->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $val = (string) ($c->is->t ?? '');
                } else {
                    $val = (string) $c->v;
                }
                $rowCells[$colIdx] = $val;
            }
            $rows[$rowIdx] = $rowCells;
        }

        return $rows;
    }

    /**
     * Konversi huruf kolom Excel (A, B, C... AA) ke 0-indexed index
     */
    protected function colLetterToIndex(string $col): int
    {
        $col = strtoupper($col);
        $idx = 0;
        for ($i = 0; $i < strlen($col); $i++) {
            $idx = $idx * 26 + (ord($col[$i]) - ord('A') + 1);
        }

        return $idx - 1;
    }
}
