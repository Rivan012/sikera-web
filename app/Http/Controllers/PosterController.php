<?php

namespace App\Http\Controllers;

use App\Models\KesproFeed;
use Illuminate\Http\Request;

class PosterController extends Controller
{
    public function index()
    {
        $posters = KesproFeed::latest()->get();
        return view('posters.index', compact('posters'));
    }

    public function download($id)
    {
        $poster = KesproFeed::findOrFail($id);
        $poster->increment('download_count');

        return back()->with('success', 'Poster "' . $poster->title . '" berhasil diunduh ke galeri lokal.');
    }

    public function share($id)
    {
        $poster = KesproFeed::findOrFail($id);
        $poster->increment('share_count');

        return response()->json([
            'status' => 'success',
            'share_count' => $poster->share_count,
            'share_url' => url('/posters/' . $poster->id),
            'share_text' => $poster->share_text,
        ]);
    }
}
