<?php

namespace App\Http\Controllers;

use App\Models\KesproFeed;
use Illuminate\Http\Request;

class AdminPosterController extends Controller
{
    public function index()
    {
        $posters = KesproFeed::latest()->get();
        return view('admin.posters.index', compact('posters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'caption' => 'required|string',
            'share_text' => 'nullable|string',
        ]);

        KesproFeed::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'image_path' => '/images/feed-new.svg',
            'caption' => $validated['caption'],
            'share_text' => $validated['share_text'] ?? 'Edukasi SIKERA UNIB',
            'download_count' => 0,
            'share_count' => 0,
        ]);

        return redirect()->route('admin.posters.index')->with('success', 'Poster visual Kaspro baru berhasil diunggah.');
    }

    public function destroy($id)
    {
        $poster = KesproFeed::findOrFail($id);
        $title = $poster->title;
        $poster->delete();

        return redirect()->route('admin.posters.index')->with('success', 'Poster "' . $title . '" berhasil dihapus.');
    }
}
