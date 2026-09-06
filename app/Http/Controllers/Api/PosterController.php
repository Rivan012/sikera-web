<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KesproFeed;
use Illuminate\Http\Request;

class PosterController extends Controller
{
    /**
     * Galeri Poster Kaspro
     */
    public function index()
    {
        $posters = KesproFeed::latest()->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'category' => $p->category,
                'image_url' => url($p->image_path),
                'caption' => $p->caption,
                'share_text' => $p->share_text,
                'download_count' => $p->download_count,
                'share_count' => $p->share_count,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar poster Kaspro berhasil dimuat.',
            'data' => [
                'posters' => $posters,
            ],
        ]);
    }

    /**
     * Catat Download Poster
     */
    public function download($id)
    {
        $poster = KesproFeed::findOrFail($id);
        $poster->increment('download_count');

        return response()->json([
            'success' => true,
            'message' => 'Statistik unduhan berhasil dicatat.',
            'data' => [
                'id' => $poster->id,
                'download_count' => $poster->download_count,
                'image_url' => url($poster->image_path),
            ],
        ]);
    }

    /**
     * Catat Share Poster
     */
    public function share($id)
    {
        $poster = KesproFeed::findOrFail($id);
        $poster->increment('share_count');

        return response()->json([
            'success' => true,
            'message' => 'Statistik share berhasil dicatat.',
            'data' => [
                'id' => $poster->id,
                'share_count' => $poster->share_count,
                'share_text' => $poster->share_text,
                'share_url' => url('/posters'),
            ],
        ]);
    }
}
