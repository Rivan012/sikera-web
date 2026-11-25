<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\StoreModuleTopicRequest;
use App\Http\Requests\UpdateModuleTopicRequest;
use App\Models\EducationalModule;
use App\Models\ModuleTopic;
use App\Models\TestQuestion;
use App\Services\ModuleBannerService;
use App\Services\ModuleExcelImportService;
use App\Services\ModuleTopicPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class AdminModuleController extends Controller
{
    private function authorizeAdmin(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman pengelolaan modul.');
        }
    }

    public function index(): InertiaResponse
    {
        $this->authorizeAdmin();

        $modules = EducationalModule::with(['topics' => fn ($q) => $q->orderBy('order_index')])
            ->orderBy('module_number')
            ->get();

        $totalTopics = $modules->sum(fn (EducationalModule $m): int => $m->topics->count());
        $pdfTopicsCount = $modules->sum(
            fn (EducationalModule $m): int => $m->topics->filter(fn (ModuleTopic $t): bool => $t->hasPdf())->count()
        );

        $moduleItems = $modules->map(fn (EducationalModule $m): array => [
            'id' => $m->id,
            'module_number' => $m->module_number,
            'title' => $m->title,
            'subtitle' => $m->subtitle,
            'description' => $m->description,
            'banner_image' => $m->banner_image ?: '/images/banners/banner-module-'.(($m->module_number % 4) ?: 4).'.svg',
            'raw_banner_image' => $m->banner_image,
            'estimated_time' => $m->estimated_time,
            'topics' => $m->topics->map(fn (ModuleTopic $tp): array => [
                'id' => $tp->id,
                'topic_code' => $tp->topic_code,
                'title' => $tp->title,
                'youtube_video_id' => $tp->youtube_video_id,
                'has_pdf' => $tp->hasPdf(),
                'pdf_original_name' => $tp->pdf_original_name,
                'pdf_size_label' => $tp->formattedPdfSize(),
            ]),
        ]);

        return Inertia::render('Admin/Modules/Index', [
            'modules' => $moduleItems,
            'totalTopics' => $totalTopics,
            'pdfTopicsCount' => $pdfTopicsCount,
            'nextModuleNumber' => ((int) $modules->max('module_number')) + 1,
        ]);
    }

    public function store(
        StoreModuleRequest $request,
        ModuleBannerService $bannerService,
        ModuleTopicPdfService $pdfService
    ): RedirectResponse {
        $validated = $request->validated();

        $bannerPath = $bannerService->handleBanner(
            moduleNumber: (int) $validated['module_number'],
            title: $validated['title'],
            subtitle: $validated['subtitle'] ?? null,
            uploadedFile: $request->file('banner_file'),
            manualUrl: $validated['banner_image'] ?? null,
            bannerMode: $request->input('banner_mode', 'auto'),
            colorTheme: $request->input('banner_theme', 'teal')
        );

        $pdfData = $pdfService->store($request->file('pdf_file'));

        try {
            $module = DB::transaction(function () use ($validated, $bannerPath, $pdfData): EducationalModule {
                $module = EducationalModule::create([
                    'module_number' => $validated['module_number'],
                    'title' => $validated['title'],
                    'subtitle' => $validated['subtitle'] ?? null,
                    'description' => $validated['description'],
                    'banner_image' => $bannerPath,
                    'estimated_time' => $validated['estimated_time'],
                ]);

                ModuleTopic::create([
                    'educational_module_id' => $module->id,
                    'topic_code' => $validated['topic_code'],
                    'title' => $validated['topic_title'],
                    'youtube_video_id' => $validated['youtube_video_id'] ?? null,
                    'content_html' => null,
                    'pdf_path' => $pdfData['pdf_path'],
                    'pdf_original_name' => $pdfData['pdf_original_name'],
                    'pdf_size' => $pdfData['pdf_size'],
                    'order_index' => 1,
                ]);

                return $module;
            });
        } catch (Throwable $e) {
            $pdfService->delete($pdfData['pdf_path']);

            throw $e;
        }

        return redirect()->route('admin.modules.index')
            ->with('success', 'Modul #'.$module->module_number.' ("'.$module->title.'") dan PDF subbab pertama berhasil dipublikasikan.');
    }

    public function updateBanner(Request $request, int $id, ModuleBannerService $bannerService): RedirectResponse
    {
        $this->authorizeAdmin();

        $module = EducationalModule::findOrFail($id);

        $validated = $request->validate([
            'banner_mode' => 'nullable|string|in:auto,upload,url',
            'banner_image' => 'nullable|string|max:500',
            'banner_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'banner_theme' => 'nullable|string|in:teal,amber,rose,indigo,emerald,purple',
        ]);

        $bannerPath = $bannerService->handleBanner(
            moduleNumber: (int) $module->module_number,
            title: $module->title,
            subtitle: $module->subtitle,
            uploadedFile: $request->file('banner_file'),
            manualUrl: $validated['banner_image'] ?? null,
            bannerMode: $request->input('banner_mode', 'auto'),
            colorTheme: $request->input('banner_theme', 'teal')
        );

        $module->update(['banner_image' => $bannerPath]);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Banner Modul #'.$module->module_number.' berhasil diperbarui.');
    }

    public function previewBannerSvg(Request $request, ModuleBannerService $bannerService): Response
    {
        $this->authorizeAdmin();

        $number = (int) $request->query('number', 1);
        $title = (string) $request->query('title', 'Judul Modul Edukasi');
        $subtitle = $request->query('subtitle');
        $theme = (string) $request->query('theme', 'teal');

        $svg = $bannerService->generateSvgString($number, $title, $subtitle, $theme);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    public function storeTopic(
        StoreModuleTopicRequest $request,
        int $moduleId,
        ModuleTopicPdfService $pdfService
    ): RedirectResponse {
        $module = EducationalModule::findOrFail($moduleId);
        $validated = $request->validated();

        $pdfData = $pdfService->store($request->file('pdf_file'));
        $orderIndex = ((int) $module->topics()->max('order_index')) + 1;

        try {
            ModuleTopic::create([
                'educational_module_id' => $module->id,
                'topic_code' => $validated['topic_code'],
                'title' => $validated['title'],
                'youtube_video_id' => $validated['youtube_video_id'] ?? null,
                'content_html' => null,
                'pdf_path' => $pdfData['pdf_path'],
                'pdf_original_name' => $pdfData['pdf_original_name'],
                'pdf_size' => $pdfData['pdf_size'],
                'order_index' => $orderIndex,
            ]);
        } catch (Throwable $e) {
            $pdfService->delete($pdfData['pdf_path']);

            throw $e;
        }

        return redirect()->route('admin.modules.index')
            ->with('success', 'Subbab '.$validated['topic_code'].' (PDF flipbook) berhasil ditambahkan ke Modul '.$module->module_number.'.');
    }

    public function updateTopic(
        UpdateModuleTopicRequest $request,
        int $id,
        ModuleTopicPdfService $pdfService
    ): RedirectResponse {
        $topic = ModuleTopic::findOrFail($id);
        $validated = $request->validated();

        $attributes = [
            'topic_code' => $validated['topic_code'],
            'title' => $validated['title'],
            'youtube_video_id' => $validated['youtube_video_id'] ?? null,
        ];

        $newPdfPath = null;
        if ($request->hasFile('pdf_file')) {
            $pdfData = $pdfService->store($request->file('pdf_file'), $topic->pdf_path);
            $newPdfPath = $pdfData['pdf_path'];
            $attributes = array_merge($attributes, $pdfData);
        }

        try {
            $topic->update($attributes);
        } catch (Throwable $e) {
            if ($newPdfPath !== null) {
                $pdfService->delete($newPdfPath);
            }

            throw $e;
        }

        $suffix = $newPdfPath !== null ? ' beserta berkas PDF flipbook baru' : '';

        return redirect()->route('admin.modules.index')
            ->with('success', 'Subbab '.$topic->topic_code.' ("'.$topic->title.'")'.$suffix.' berhasil diperbarui.');
    }

    public function destroy(int $id, ModuleTopicPdfService $pdfService): RedirectResponse
    {
        $this->authorizeAdmin();

        $module = EducationalModule::with('topics:id,educational_module_id,pdf_path')->findOrFail($id);
        $moduleNumber = $module->module_number;
        $title = $module->title;
        $pdfPaths = $module->topics->pluck('pdf_path')->filter()->all();

        TestQuestion::preTest()->where('module_target', $moduleNumber)->delete();
        $module->delete();

        foreach ($pdfPaths as $path) {
            $pdfService->delete($path);
        }

        return redirect()->route('admin.modules.index')
            ->with('success', 'Modul #'.$moduleNumber.' ("'.$title.'") beserta seluruh subbabnya berhasil dihapus.');
    }

    public function destroyTopic(int $id, ModuleTopicPdfService $pdfService): RedirectResponse
    {
        $this->authorizeAdmin();

        $topic = ModuleTopic::findOrFail($id);
        $title = $topic->title;
        $topicCode = $topic->topic_code;
        $pdfPath = $topic->pdf_path;

        $topic->delete();
        $pdfService->delete($pdfPath);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Subbab '.$topicCode.' ("'.$title.'") berhasil dihapus.');
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $this->authorizeAdmin();

        $filePath = public_path('templates/format_modul_sikera.xlsx');

        if (! file_exists($filePath)) {
            abort(404, 'File template format modul Excel belum ditemukan.');
        }

        return response()->download($filePath, 'Format_Modul_Edukasi_SIKERA.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importExcel(Request $request, ModuleExcelImportService $importService): RedirectResponse
    {
        $this->authorizeAdmin();

        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:15360'],
            'overwrite' => ['nullable'],
        ], [
            'excel_file.required' => 'Pilih file Excel (.xlsx) terlebih dahulu.',
            'excel_file.file' => 'Berkas yang diunggah tidak valid.',
            'excel_file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
            'excel_file.max' => 'Ukuran file Excel maksimal 15 MB.',
        ]);

        try {
            $overwrite = $request->has('overwrite') ? (bool) $request->input('overwrite') : true;
            $result = $importService->import($request->file('excel_file')->getRealPath(), $overwrite);

            $msg = "Berhasil mengimpor file Excel. {$result['modules_created']} modul baru dibuat, {$result['modules_updated']} diperbarui, ".
                ($result['topics_created'] + $result['topics_updated']).' subbab, dan '.
                ($result['questions_created'] + $result['questions_updated']).' butir soal kuesioner berhasil disinkronkan.';

            return redirect()->route('admin.modules.index')->with('success', $msg);
        } catch (Throwable $e) {
            return redirect()->route('admin.modules.index')
                ->withErrors(['error' => 'Gagal mengimpor file Excel: '.$e->getMessage()]);
        }
    }
}
