<?php

namespace App\Http\Controllers;

use App\Models\EducationalModule;
use App\Models\ModuleTopic;
use Illuminate\Http\Request;

class AdminModuleController extends Controller
{
    public function index()
    {
        $modules = EducationalModule::with(['topics' => fn($q) => $q->orderBy('order_index')])->orderBy('module_number')->get();
        return view('admin.modules.index', compact('modules'));
    }

    public function store(Request $request)
    {
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

        return redirect()->route('admin.modules.index')->with('success', 'Modul edukasi #' . $module->module_number . ' ("' . $module->title . '") berhasil diunggah!');
    }

    public function storeTopic(Request $request, $moduleId)
    {
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

        return redirect()->route('admin.modules.index')->with('success', 'Submateri ' . $validated['topic_code'] . ' berhasil ditambahkan ke Modul ' . $module->module_number . '!');
    }

    public function destroy($id)
    {
        $module = EducationalModule::findOrFail($id);
        $title = $module->title;
        $module->delete();

        return redirect()->route('admin.modules.index')->with('success', 'Modul "' . $title . '" berhasil dihapus.');
    }

    public function destroyTopic($id)
    {
        $topic = ModuleTopic::findOrFail($id);
        $title = $topic->title;
        $topic->delete();

        return redirect()->route('admin.modules.index')->with('success', 'Submateri "' . $title . '" berhasil dihapus.');
    }
}
