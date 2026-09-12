<?php

namespace App\Http\Controllers;

use App\Models\EducationalModule;
use App\Models\ModuleTopic;
use App\Models\TestQuestion;
use App\Services\ModuleExcelImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminModuleController extends Controller
{
    private function authorizeAdmin(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman pengelolaan modul.');
        }
    }

    public function index()
    {
        $this->authorizeAdmin();

        $modules = EducationalModule::with(['topics' => fn ($q) => $q->orderBy('order_index')])
            ->orderBy('module_number')
            ->get();

        return view('admin.modules.index', compact('modules'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'module_number' => 'required|integer|unique:educational_modules,module_number',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'banner_image' => 'nullable|string',
            'estimated_time' => 'required|string',
            'topic_title' => 'required|string|max:255',
            'topic_code' => 'required|string|max:10',
            'youtube_video_id' => 'nullable|string',
            'content_html' => 'required|string',
        ]);

        $bannerIndex = (($validated['module_number'] % 4) ?: 4);
        $bannerPath = $validated['banner_image'] ?: "/images/banners/banner-module-{$bannerIndex}.svg";

        $module = EducationalModule::create([
            'module_number' => $validated['module_number'],
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'description' => $validated['description'],
            'banner_image' => $bannerPath,
            'estimated_time' => $validated['estimated_time'],
        ]);

        ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => $validated['topic_code'],
            'title' => $validated['topic_title'],
            'youtube_video_id' => $validated['youtube_video_id'],
            'content_html' => $validated['content_html'],
            'order_index' => 1,
        ]);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Modul edukasi #'.$module->module_number.' ("'.$module->title.'") berhasil diunggah!');
    }

    public function storeTopic(Request $request, $moduleId)
    {
        $this->authorizeAdmin();

        $module = EducationalModule::findOrFail($moduleId);

        $validated = $request->validate([
            'topic_code' => 'required|string|max:10',
            'title' => 'required|string|max:255',
            'youtube_video_id' => 'nullable|string',
            'content_html' => 'required|string',
        ]);

        $orderIndex = $module->topics()->count() + 1;

        ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => $validated['topic_code'],
            'title' => $validated['title'],
            'youtube_video_id' => $validated['youtube_video_id'],
            'content_html' => $validated['content_html'],
            'order_index' => $orderIndex,
        ]);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Submateri '.$validated['topic_code'].' berhasil ditambahkan ke Modul '.$module->module_number.'!');
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();

        $module = EducationalModule::findOrFail($id);
        $moduleNumber = $module->module_number;
        $title = $module->title;

        // Bersihkan soal kuesioner terkait modul ini jika ada
        TestQuestion::where('module_target', $moduleNumber)->delete();

        // Hapus modul (module_topics dan module_progress otomatis terhapus via foreign key cascade)
        $module->delete();

        return redirect()->route('admin.modules.index')
            ->with('success', 'Modul #'.$moduleNumber.' ("'.$title.'") beserta seluruh submaterinya berhasil dihapus.');
    }

    public function destroyTopic($id)
    {
        $this->authorizeAdmin();

        $topic = ModuleTopic::findOrFail($id);
        $title = $topic->title;
        $topicCode = $topic->topic_code;
        $topic->delete();

        return redirect()->route('admin.modules.index')
            ->with('success', 'Submateri '.$topicCode.' ("'.$title.'") berhasil dihapus.');
    }

    public function downloadTemplate()
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

    public function importExcel(Request $request, ModuleExcelImportService $importService)
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

            $msg = "Berhasil mengimpor file Excel! {$result['modules_created']} modul baru dibuat, {$result['modules_updated']} diperbarui, ".
                ($result['topics_created'] + $result['topics_updated']).' submateri, dan '.
                ($result['questions_created'] + $result['questions_updated']).' butir soal kuesioner berhasil disinkronkan.';

            return redirect()->route('admin.modules.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('admin.modules.index')
                ->withErrors(['error' => 'Gagal mengimpor file Excel: '.$e->getMessage()]);
        }
    }
}
