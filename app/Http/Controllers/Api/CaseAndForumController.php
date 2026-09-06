<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\ForumThread;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CaseAndForumController extends Controller
{
    /**
     * Daftar Studi Kasus Nyata Kampus
     */
    public function getCases()
    {
        $cases = CaseStudy::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar studi kasus berhasil dimuat.',
            'data' => [
                'case_studies' => $cases,
            ],
        ]);
    }

    /**
     * Detail Studi Kasus
     */
    public function getCaseDetail($id)
    {
        $case = CaseStudy::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail studi kasus berhasil dimuat.',
            'data' => [
                'case_study' => $case,
            ],
        ]);
    }

    /**
     * Daftar Forum Tanya Jawab Anonim
     */
    public function getThreads(Request $request)
    {
        $threads = ForumThread::withCount('replies')->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Daftar thread forum berhasil dimuat.',
            'data' => [
                'threads' => $threads->items(),
                'current_page' => $threads->currentPage(),
                'last_page' => $threads->lastPage(),
                'total' => $threads->total(),
            ],
        ]);
    }

    /**
     * Buat Pertanyaan Baru di Forum
     */
    public function createThread(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'topic' => 'required|in:Haid,Hubungan,Medis,Darurat',
            'content' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data pertanyaan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $thread = ForumThread::create([
            'user_id' => $user->id,
            'anonymous_alias' => 'Mahasiswa #' . rand(100, 999),
            'title' => $request->title,
            'topic' => $request->topic,
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pertanyaan anonim berhasil diposting.',
            'data' => [
                'thread' => $thread,
            ],
        ], 201);
    }

    /**
     * Detail Thread Forum & Seluruh Balasan
     */
    public function getThreadDetail($id)
    {
        $thread = ForumThread::with(['replies' => fn($q) => $q->oldest()])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail forum diskusi berhasil dimuat.',
            'data' => [
                'thread' => $thread,
            ],
        ]);
    }

    /**
     * Kirim Balasan pada Thread
     */
    public function replyThread(Request $request, $id)
    {
        $user = $request->user();
        $thread = ForumThread::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'reply_content' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Isi balasan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $displayName = $user->isMahasiswa()
            ? 'Mahasiswa #' . rand(100, 999)
            : ($user->isDosen() ? 'Dosen PA Konselor' : 'Admin SIKERA');

        $roleBadge = $user->role === 'dosen_pa' ? 'dosen_pa' : ($user->role === 'admin' ? 'admin' : 'mahasiswa');

        $reply = ForumReply::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'display_name' => $displayName,
            'role_badge' => $roleBadge,
            'reply_content' => $request->reply_content,
        ]);

        if ($user->isDosen() || $user->isAdmin()) {
            $thread->update(['is_answered_by_counselor' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Balasan berhasil dikirim.',
            'data' => [
                'reply' => $reply,
            ],
        ], 201);
    }
}
