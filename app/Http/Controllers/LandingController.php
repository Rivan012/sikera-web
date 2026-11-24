<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\EducationalModule;
use App\Models\EmergencyHotline;
use App\Models\KesproFeed;
use Illuminate\Contracts\View\View;
use Throwable;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $modules = [];
        $posters = [];
        $cases = [];
        $hotlines = [];
        $totalTopicsCount = 0;
        $dataLoadError = false;

        try {
            $moduleModels = EducationalModule::query()
                ->select(['id', 'module_number', 'title', 'subtitle', 'description', 'estimated_time'])
                ->with(['topics:id,educational_module_id,topic_code,title,order_index,youtube_video_id,pdf_path'])
                ->orderBy('module_number')
                ->get();

            foreach ($moduleModels as $module) {
                $topicItems = [];
                foreach ($module->topics as $topic) {
                    $topicItems[] = [
                        'code' => (string) $topic->topic_code,
                        'title' => $this->sanitizeCopy($topic->title),
                        'has_video' => filled($topic->youtube_video_id),
                        'has_pdf' => $topic->hasPdf(),
                    ];
                }

                $totalTopicsCount += count($topicItems);

                $modules[] = [
                    'id' => $module->id,
                    'number' => str_pad((string) $module->module_number, 2, '0', STR_PAD_LEFT),
                    'title' => $this->sanitizeCopy($module->title),
                    'subtitle' => $this->sanitizeCopy($module->subtitle),
                    'description' => $this->sanitizeCopy($module->description),
                    'duration' => $this->sanitizeCopy($module->estimated_time ?? '15 Menit'),
                    'topics' => $topicItems,
                ];
            }

            $posterModels = KesproFeed::query()
                ->select(['id', 'title', 'category', 'image_path', 'caption', 'share_text'])
                ->orderBy('id')
                ->take(4)
                ->get();

            foreach ($posterModels as $poster) {
                $relativePath = is_string($poster->image_path) ? ltrim($poster->image_path, '/') : '';
                $hasRealImage = $relativePath !== '' && is_file(public_path($relativePath));

                $posters[] = [
                    'id' => $poster->id,
                    'title' => $this->sanitizeCopy($poster->title),
                    'category' => $this->sanitizeCopy($poster->category),
                    'caption' => $this->sanitizeCopy($poster->caption),
                    'share_url' => 'https://wa.me/?text='.rawurlencode($this->sanitizeCopy($poster->share_text).' '.route('posters.index')),
                    'image_url' => $hasRealImage ? asset($relativePath) : null,
                ];
            }

            $caseModels = CaseStudy::query()
                ->select(['id', 'title', 'category', 'narrative', 'legal_analysis', 'medical_analysis', 'solution_tips'])
                ->orderBy('id')
                ->take(3)
                ->get();

            foreach ($caseModels as $case) {
                $cases[] = [
                    'id' => $case->id,
                    'title' => $this->sanitizeCopy($case->title),
                    'category' => $this->sanitizeCopy($case->category),
                    'narrative' => $this->sanitizeCopy($case->narrative),
                    'legal_analysis' => $this->sanitizeCopy($case->legal_analysis),
                    'medical_analysis' => $this->sanitizeCopy($case->medical_analysis),
                    'solution_tips' => $this->sanitizeCopy($case->solution_tips),
                ];
            }

            $hotlineModels = EmergencyHotline::query()
                ->select(['id', 'name', 'category', 'phone_number', 'address', 'operating_hours', 'description'])
                ->orderBy('id')
                ->get();

            foreach ($hotlineModels as $hotline) {
                $rawPhone = (string) ($hotline->phone_number ?? '');
                $digits = preg_replace('/\D+/', '', $rawPhone) ?? '';
                $isVerifiedPhone = $this->isVerifiedPhoneNumber($digits);

                $hotlines[] = [
                    'id' => $hotline->id,
                    'name' => $this->sanitizeCopy($hotline->name),
                    'category' => $this->sanitizeCopy($hotline->category),
                    'phone_display' => $isVerifiedPhone ? $this->sanitizeCopy($rawPhone) : null,
                    'tel_href' => $isVerifiedPhone ? 'tel:'.$digits : null,
                    'is_verified_phone' => $isVerifiedPhone,
                    'address' => $this->sanitizeCopy($hotline->address),
                    'operating_hours' => $this->sanitizeCopy($hotline->operating_hours),
                    'description' => $this->sanitizeCopy($hotline->description),
                ];
            }
        } catch (Throwable) {
            $dataLoadError = true;
        }

        $bloodSpectrum = [
            [
                'label' => 'Merah Terang',
                'status' => 'Fisiologis (Normal)',
                'tone' => 'normal',
                'swatch_hex' => '#B91C1C',
                'note' => 'Darah segar dengan aliran lancar saat peluruhan dinding rahim di hari ke-1 sampai ke-3 siklus haid.',
            ],
            [
                'label' => 'Cokelat Gelap / Kehitaman',
                'status' => 'Fisiologis (Oksidasi)',
                'tone' => 'normal',
                'swatch_hex' => '#5C2D06',
                'note' => 'Darah sisa pada awal atau akhir siklus yang teroksidasi alami karena keluar lebih perlahan.',
            ],
            [
                'label' => 'Merah Muda Pucat',
                'status' => 'Perlu Diamati',
                'tone' => 'caution',
                'swatch_hex' => '#F4A28C',
                'note' => 'Bercak awal (spotting) atau campuran cairan serviks; bila berulang di luar siklus, evaluasi kadar estrogen.',
            ],
            [
                'label' => 'Hitam Pekat, Gumpalan Besar, & Berbau',
                'status' => 'Rujuk Fasilitas Medis',
                'tone' => 'urgent',
                'swatch_hex' => '#291518',
                'note' => 'Bila gumpalan berdiameter lebih dari 2,5 cm disertai demam atau bau menyengat, periksakan segera ke dokter.',
            ],
        ];

        $figoVolumes = [
            [
                'category' => 'Hipomenore',
                'range' => '< 5 sampai 10 ml',
                'indicator' => 'Bercak singkat kurang dari 2 hari, pembalut jarang terisi.',
            ],
            [
                'category' => 'Eumenore (Normal)',
                'range' => '30 sampai 40 ml',
                'indicator' => 'Rentang fisiologis 5 sampai 80 ml per siklus (skor PBAC < 100), ganti pembalut 3 sampai 5 kali sehari.',
            ],
            [
                'category' => 'Menoragia (HMB)',
                'range' => '> 80 ml',
                'indicator' => 'Pembalut jenuh tiap 1 sampai 2 jam (skor PBAC >= 100), berisiko memicu anemia defisiensi besi.',
            ],
        ];

        return view('welcome', [
            'modules' => $modules,
            'totalTopicsCount' => $totalTopicsCount,
            'posters' => $posters,
            'cases' => $cases,
            'hotlines' => $hotlines,
            'bloodSpectrum' => $bloodSpectrum,
            'figoVolumes' => $figoVolumes,
            'dataLoadError' => $dataLoadError,
        ]);
    }

    private function isVerifiedPhoneNumber(string $digits): bool
    {
        if (strlen($digits) < 7) {
            return false;
        }

        // Seeded placeholder sequences such as 0812-7890-1234 must not render as live emergency tel links.
        $placeholderPatterns = ['78901234', '1234567', '67894321', '000000'];
        foreach ($placeholderPatterns as $pattern) {
            if (str_contains($digits, $pattern)) {
                return false;
            }
        }

        return true;
    }

    private function sanitizeCopy(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $cleaned = preg_replace('/\*([^*]+)\*/', '$1', $text) ?? $text;
        $cleaned = str_replace([' — ', '—', ' -- '], [', ', ', ', ', '], $cleaned);

        return trim($cleaned);
    }
}
