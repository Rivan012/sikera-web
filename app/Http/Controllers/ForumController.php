<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        $threads = ForumThread::withCount('replies')->latest()->paginate(15);
        return view('forum.index', compact('threads'));
    }

    public function create()
    {
        return view('forum.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'topic' => 'required|in:Haid,Hubungan,Medis,Darurat',
            'content' => 'required|string|min:10',
        ]);

        ForumThread::create([
            'user_id' => Auth::id(),
            'anonymous_alias' => 'Mahasiswa #' . rand(100, 999),
            'title' => $validated['title'],
            'topic' => $validated['topic'],
            'content' => $validated['content'],
        ]);

        return redirect()->route('forum.index')->with('success', 'Pertanyaan berhasil diposting secara anonim.');
    }

    public function show($id)
    {
        $thread = ForumThread::with(['replies' => fn($q) => $q->oldest()])->findOrFail($id);
        return view('forum.show', compact('thread'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_content' => 'required|string|min:3',
        ]);

        $user = Auth::user();
        $thread = ForumThread::findOrFail($id);

        $displayName = $user->isMahasiswa()
            ? 'Mahasiswa #' . rand(100, 999)
            : ($user->isDosen() ? 'Dosen PA Konselor' : 'Admin SIKERA');

        $roleBadge = $user->role === 'dosen_pa' ? 'dosen_pa' : ($user->role === 'admin' ? 'admin' : 'mahasiswa');

        ForumReply::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'display_name' => $displayName,
            'role_badge' => $roleBadge,
            'reply_content' => $request->reply_content,
        ]);

        if ($user->isDosen() || $user->isAdmin()) {
            $thread->update(['is_answered_by_counselor' => true]);
        }

        return redirect()->route('forum.show', $id)->with('success', 'Balasan berhasil dikirim.');
    }
}
