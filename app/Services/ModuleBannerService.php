<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ModuleBannerService
{
    /**
     * Palet tema warna untuk banner SVG
     *
     * @var array<string, array{start: string, mid: string, end: string, accent: string, textAccent: string, icon: string}>
     */
    protected array $themes = [
        'teal' => [
            'start' => '#0f766e',
            'mid' => '#115e59',
            'end' => '#042f2e',
            'accent' => '#2dd4bf',
            'textAccent' => '#5eead4',
            'icon' => 'medical_cross',
        ],
        'amber' => [
            'start' => '#b45309',
            'mid' => '#92400e',
            'end' => '#451a03',
            'accent' => '#fbbf24',
            'textAccent' => '#fde68a',
            'icon' => 'heart_spark',
        ],
        'rose' => [
            'start' => '#be123c',
            'mid' => '#9f1239',
            'end' => '#4c0519',
            'accent' => '#fb7185',
            'textAccent' => '#fda4af',
            'icon' => 'flower_ribbon',
        ],
        'indigo' => [
            'start' => '#3730a3',
            'mid' => '#312e81',
            'end' => '#1e1b4b',
            'accent' => '#818cf8',
            'textAccent' => '#a5b4fc',
            'icon' => 'shield_legal',
        ],
        'emerald' => [
            'start' => '#047857',
            'mid' => '#065f46',
            'end' => '#022c22',
            'accent' => '#34d399',
            'textAccent' => '#6ee7b7',
            'icon' => 'leaf_hygiene',
        ],
        'purple' => [
            'start' => '#6b21a8',
            'mid' => '#581c87',
            'end' => '#2e1065',
            'accent' => '#c084fc',
            'textAccent' => '#d8b4fe',
            'icon' => 'star_psychology',
        ],
    ];

    /**
     * Memproses banner berdasarkan mode pilihan pengguna:
     * 1. 'upload': Unggah berkas gambar kustom
     * 2. 'url': Gunakan URL / path gambar teks manual
     * 3. 'auto': Otomatis generate berkas SVG berdesain modern
     *
     * @param  int  $moduleNumber  Nomor modul
     * @param  string  $title  Judul modul
     * @param  string|null  $subtitle  Subjudul / ringkasan modul
     * @param  UploadedFile|null  $uploadedFile  File gambar yang diunggah
     * @param  string|null  $manualUrl  URL atau path gambar manual
     * @param  string  $bannerMode  Mode banner ('auto', 'upload', 'url')
     * @param  string  $colorTheme  Tema warna untuk generate otomatis
     * @return string Path publik web banner
     */
    public function handleBanner(
        int $moduleNumber,
        string $title,
        ?string $subtitle = null,
        ?UploadedFile $uploadedFile = null,
        ?string $manualUrl = null,
        string $bannerMode = 'auto',
        string $colorTheme = 'teal'
    ): string {
        // 1. Jika mode 'upload' dan file tersedia, simpan file gambar kustom
        if (($bannerMode === 'upload' || ($bannerMode === 'auto' && $uploadedFile)) && $uploadedFile) {
            return $this->storeUploadedBanner($moduleNumber, $uploadedFile);
        }

        // 2. Jika mode 'url' dan manualUrl diisi, gunakan URL/path manual tersebut
        if (($bannerMode === 'url' || ($bannerMode === 'auto' && ! empty($manualUrl))) && ! empty(trim((string) $manualUrl))) {
            return trim((string) $manualUrl);
        }

        // 3. Mode 'auto' (atau jika upload/url kosong): Otomatis generate banner SVG kustom
        return $this->generateAndSaveSvgBanner($moduleNumber, $title, $subtitle, $colorTheme);
    }

    /**
     * Menyimpan file banner yang diunggah pengguna ke public/images/banners/uploads/
     */
    public function storeUploadedBanner(int $moduleNumber, UploadedFile $file): string
    {
        $uploadDir = public_path('images/banners/uploads');
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = $file->getClientOriginalExtension() ?: 'png';
        $filename = "banner-module-{$moduleNumber}-".Str::random(8).'-'.time().".{$extension}";
        $file->move($uploadDir, $filename);

        return "/images/banners/uploads/{$filename}";
    }

    /**
     * Otomatis generate dan simpan banner SVG ke public/images/banners/generated/
     */
    public function generateAndSaveSvgBanner(int $moduleNumber, string $title, ?string $subtitle = null, string $theme = 'teal'): string
    {
        $generatedDir = public_path('images/banners/generated');
        if (! is_dir($generatedDir)) {
            mkdir($generatedDir, 0755, true);
        }

        $svgContent = $this->generateSvgString($moduleNumber, $title, $subtitle, $theme);

        $slug = Str::slug(substr($title, 0, 30)) ?: 'modul';
        $filename = "banner-module-{$moduleNumber}-{$slug}.svg";
        $filePath = $generatedDir.DIRECTORY_SEPARATOR.$filename;

        file_put_contents($filePath, $svgContent);

        return "/images/banners/generated/{$filename}";
    }

    /**
     * Membuat string SVG 800x320 dengan desain modern responsif tema SIKERA.
     */
    public function generateSvgString(int $moduleNumber, string $title, ?string $subtitle = null, string $theme = 'teal'): string
    {
        $themeConfig = $this->themes[$theme] ?? $this->themes['teal'];

        $formattedNumber = sprintf('%02d', $moduleNumber);

        // Pisahkan judul jika terlalu panjang menjadi 2 baris
        $cleanTitle = trim($title);
        $titleLine1 = $cleanTitle;
        $titleLine2 = '';

        if (mb_strlen($cleanTitle) > 28) {
            $words = explode(' ', $cleanTitle);
            $half = (int) ceil(count($words) / 2);
            $titleLine1 = implode(' ', array_slice($words, 0, $half));
            $titleLine2 = implode(' ', array_slice($words, $half));
        }

        $subtitleText = $subtitle ? trim($subtitle) : 'Modul Pembelajaran Interaktif Kesehatan Reproduksi Remaja';
        if (mb_strlen($subtitleText) > 75) {
            $subtitleText = mb_substr($subtitleText, 0, 72).'...';
        }

        $xmlTitle1 = htmlspecialchars($titleLine1, ENT_XML1);
        $xmlTitle2 = htmlspecialchars($titleLine2, ENT_XML1);
        $xmlSubtitle = htmlspecialchars($subtitleText, ENT_XML1);

        $gradId = "bgGrad_mod_{$moduleNumber}_".Str::random(4);
        $iconSvg = $this->getThemeIconSvg($themeConfig['icon'], $themeConfig['accent']);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 320" width="100%" height="100%">
  <defs>
    <linearGradient id="{$gradId}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$themeConfig['start']}" />
      <stop offset="55%" stop-color="{$themeConfig['mid']}" />
      <stop offset="100%" stop-color="{$themeConfig['end']}" />
    </linearGradient>
    <linearGradient id="overlayGrad" x1="0%" y1="100%" x2="0%" y2="0%">
      <stop offset="0%" stop-color="#000000" stop-opacity="0.4" />
      <stop offset="100%" stop-color="#000000" stop-opacity="0" />
    </linearGradient>
  </defs>

  <!-- Background Base -->
  <rect width="800" height="320" fill="url(#{$gradId})" />
  <rect width="800" height="320" fill="url(#overlayGrad)" />

  <!-- Geometric Abstract Accents -->
  <circle cx="720" cy="70" r="150" fill="{$themeConfig['accent']}" opacity="0.10" />
  <circle cx="660" cy="240" r="180" fill="{$themeConfig['accent']}" opacity="0.06" />
  <path d="M-40 260 Q 220 180, 460 260 T 840 200 L 840 360 L -40 360 Z" fill="{$themeConfig['end']}" opacity="0.4" />

  <!-- Medical / Scientific Icon Graphic -->
  <g transform="translate(600, 75)" opacity="0.85">
    {$iconSvg}
  </g>

  <!-- Content Group -->
  <g transform="translate(48, 75)">
    <!-- Modul Pill Badge -->
    <rect x="0" y="0" width="116" height="28" rx="14" fill="{$themeConfig['accent']}" fill-opacity="0.22" />
    <text x="58" y="18" font-family="'Plus Jakarta Sans', 'Segoe UI', sans-serif" font-size="12" font-weight="700" fill="{$themeConfig['textAccent']}" text-anchor="middle" letter-spacing="1">MODUL {$formattedNumber}</text>

    <!-- Main Title -->
    <text x="0" y="65" font-family="'Plus Jakarta Sans', 'Segoe UI', sans-serif" font-size="28" font-weight="800" fill="#ffffff">{$xmlTitle1}</text>
    <text x="0" y="98" font-family="'Plus Jakarta Sans', 'Segoe UI', sans-serif" font-size="28" font-weight="800" fill="{$themeConfig['accent']}">{$xmlTitle2}</text>

    <!-- Subtitle -->
    <text x="0" y="132" font-family="'Plus Jakarta Sans', 'Segoe UI', sans-serif" font-size="14" font-weight="400" fill="#f8fafc" opacity="0.9">{$xmlSubtitle}</text>
  </g>
</svg>
SVG;
    }

    /**
     * Ikon vektor pendukung di sisi kanan banner
     */
    protected function getThemeIconSvg(string $iconType, string $color): string
    {
        return match ($iconType) {
            'heart_spark' => <<<SVG
<path d="M50 30 A20 20 0 0 1 90 30 A20 20 0 0 1 130 30 Q130 65 90 95 Q50 65 50 30 Z" fill="{$color}" fill-opacity="0.25" stroke="{$color}" stroke-width="2.5" />
<circle cx="90" cy="35" r="10" fill="{$color}" opacity="0.4" />
<path d="M125 15 L130 25 L140 30 L130 35 L125 45 L120 35 L110 30 L120 25 Z" fill="{$color}" opacity="0.6" />
SVG,
            'shield_legal' => <<<SVG
<path d="M40 20 L90 5 L140 20 L140 65 Q140 105 90 120 Q40 105 40 65 Z" fill="{$color}" fill-opacity="0.2" stroke="{$color}" stroke-width="2.5" />
<path d="M90 30 L90 95 M65 55 L115 55" stroke="{$color}" stroke-width="3" stroke-linecap="round" />
SVG,
            'flower_ribbon' => <<<SVG
<circle cx="85" cy="55" r="45" stroke="{$color}" stroke-width="2" stroke-dasharray="6 4" fill="none" />
<path d="M65 85 C65 55 105 55 105 85 C105 105 65 105 65 85 Z" fill="{$color}" fill-opacity="0.3" stroke="{$color}" stroke-width="2" />
<circle cx="85" cy="55" r="14" fill="{$color}" opacity="0.5" />
SVG,
            'leaf_hygiene' => <<<SVG
<path d="M50 80 Q50 20 110 20 Q110 80 50 80 Z" fill="{$color}" fill-opacity="0.25" stroke="{$color}" stroke-width="2.5" />
<path d="M50 80 Q80 50 110 20" stroke="{$color}" stroke-width="2" />
<circle cx="115" cy="55" r="16" fill="{$color}" fill-opacity="0.2" stroke="{$color}" stroke-width="1.5" />
SVG,
            'star_psychology' => <<<SVG
<circle cx="85" cy="55" r="40" fill="none" stroke="{$color}" stroke-width="2" stroke-dasharray="4 4" />
<circle cx="85" cy="55" r="18" fill="{$color}" fill-opacity="0.3" stroke="{$color}" stroke-width="2" />
<path d="M85 10 L85 100 M40 55 L130 55" stroke="{$color}" stroke-width="1.5" stroke-opacity="0.4" />
SVG,
            default => <<<SVG
<circle cx="85" cy="55" r="45" stroke="{$color}" stroke-width="2.5" stroke-dasharray="6 4" fill="none" />
<path d="M85 25 L85 85 M55 55 L115 55" stroke="{$color}" stroke-width="3" stroke-linecap="round" />
<circle cx="85" cy="55" r="15" fill="{$color}" opacity="0.3" />
SVG
        };
    }
}
