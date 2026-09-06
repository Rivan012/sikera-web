<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TriviaQuestion;
use App\Models\MythFactCard;
use App\Models\UserDailyActivity;
use App\Models\SecretDiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class GamificationController extends Controller
{
    /**
     * Trivia Harian (Status / Pertanyaan Hari Ini)
     */
    public function getTrivia(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $alreadyAnswered = UserDailyActivity::where('user_id', $user->id)
            ->where('activity_date', $today)
            ->where('activity_type', 'trivia')
            ->first();

        if ($alreadyAnswered) {
            return response()->json([
                'success' => true,
                'message' => 'Anda sudah menyelesaikan trivia hari ini. Kembali besok untuk pertanyaan baru!',
                'data' => [
                    'already_answered' => true,
                    'points_earned' => $alreadyAnswered->points_earned,
                    'streak_days' => $user->streak_days,
                    'total_points' => $user->points,
                ],
            ]);
        }

        $question = TriviaQuestion::inRandomOrder()->first();

        return response()->json([
            'success' => true,
            'message' => 'Pertanyaan trivia harian berhasil dimuat.',
            'data' => [
                'already_answered' => false,
                'streak_days' => $user->streak_days,
                'total_points' => $user->points,
                'question' => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'choices' => $question->choices,
                ],
            ],
        ]);
    }

    /**
     * Submit Jawaban Trivia Harian
     */
    public function submitTrivia(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $validator = Validator::make($request->all(), [
            'question_id' => 'required|integer',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Jawaban trivia tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $already = UserDailyActivity::where('user_id', $user->id)
            ->where('activity_date', $today)
            ->where('activity_type', 'trivia')
            ->exists();

        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah menjawab trivia hari ini.',
            ], 400);
        }

        $question = TriviaQuestion::findOrFail($request->question_id);
        $isCorrect = ($request->answer === $question->answer_key);
        $points = $isCorrect ? 20 : 5;

        UserDailyActivity::create([
            'user_id' => $user->id,
            'activity_date' => $today,
            'activity_type' => 'trivia',
            'points_earned' => $points,
        ]);

        $user->increment('points', $points);
        $this->updateStreak($user);

        return response()->json([
            'success' => true,
            'message' => $isCorrect ? 'Jawaban Anda Benar!' : 'Jawaban Kurang Tepat.',
            'data' => [
                'is_correct' => $isCorrect,
                'correct_answer' => $question->answer_key,
                'scientific_explanation' => $question->scientific_explanation,
                'points_earned' => $points,
                'streak_days' => $user->fresh()->streak_days,
                'total_points' => $user->fresh()->points,
            ],
        ]);
    }

    /**
     * Mitos vs Fakta Harian
     */
    public function getMythFact(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $alreadyAnswered = UserDailyActivity::where('user_id', $user->id)
            ->where('activity_date', $today)
            ->where('activity_type', 'myth_fact')
            ->first();

        if ($alreadyAnswered) {
            return response()->json([
                'success' => true,
                'message' => 'Anda sudah menyelesaikan kartu Mitos/Fakta hari ini.',
                'data' => [
                    'already_answered' => true,
                    'points_earned' => $alreadyAnswered->points_earned,
                ],
            ]);
        }

        $card = MythFactCard::inRandomOrder()->first();

        return response()->json([
            'success' => true,
            'message' => 'Kartu Mitos vs Fakta berhasil dimuat.',
            'data' => [
                'already_answered' => false,
                'card' => [
                    'id' => $card->id,
                    'category' => $card->category,
                    'statement' => $card->statement,
                ],
            ],
        ]);
    }

    /**
     * Submit Jawaban Mitos vs Fakta
     */
    public function submitMythFact(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $validator = Validator::make($request->all(), [
            'card_id' => 'required|integer',
            'answer' => 'required|in:fact,myth',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Pilihan tidak valid (harus fact atau myth).',
                'errors' => $validator->errors(),
            ], 422);
        }

        $already = UserDailyActivity::where('user_id', $user->id)
            ->where('activity_date', $today)
            ->where('activity_type', 'myth_fact')
            ->exists();

        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah menjawab kartu Mitos/Fakta hari ini.',
            ], 400);
        }

        $card = MythFactCard::findOrFail($request->card_id);
        $userSaidFact = ($request->answer === 'fact');
        $isCorrect = ($userSaidFact === $card->is_fact);
        $points = $isCorrect ? 15 : 5;

        UserDailyActivity::create([
            'user_id' => $user->id,
            'activity_date' => $today,
            'activity_type' => 'myth_fact',
            'points_earned' => $points,
        ]);

        $user->increment('points', $points);
        $this->updateStreak($user);

        return response()->json([
            'success' => true,
            'message' => $isCorrect ? 'Tebakan Tepat!' : 'Tebakan Kurang Tepat.',
            'data' => [
                'is_correct' => $isCorrect,
                'is_fact' => $card->is_fact,
                'status_text' => $card->is_fact ? 'FAKTA' : 'MITOS',
                'scientific_fact' => $card->scientific_fact,
                'points_earned' => $points,
                'streak_days' => $user->fresh()->streak_days,
                'total_points' => $user->fresh()->points,
            ],
        ]);
    }

    /**
     * Daftar Catatan Mood / Diary
     */
    public function getDiary(Request $request)
    {
        $user = $request->user();
        $entries = SecretDiary::where('user_id', $user->id)->latest('entry_date')->take(30)->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat catatan mood berhasil dimuat.',
            'data' => [
                'entries' => $entries,
            ],
        ]);
    }

    /**
     * Simpan Catatan Mood Baru
     */
    public function storeDiary(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'mood' => 'required|in:Senang,Cemas,Lelah,Sedih,Tenang',
            'encrypted_note' => 'required|string|max:1000',
            'entry_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data catatan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $entry = SecretDiary::create([
            'user_id' => $user->id,
            'mood' => $request->mood,
            'encrypted_note' => $request->encrypted_note,
            'entry_date' => $request->entry_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan suasana hati berhasil disimpan.',
            'data' => [
                'entry' => $entry,
            ],
        ], 201);
    }

    private function updateStreak($user)
    {
        $yesterday = Carbon::yesterday();
        if ($user->last_activity_date && Carbon::parse($user->last_activity_date)->equalTo($yesterday)) {
            $user->increment('streak_days');
        } elseif (!$user->last_activity_date || !Carbon::parse($user->last_activity_date)->equalTo(Carbon::today())) {
            $user->update(['streak_days' => 1]);
        }
        $user->update(['last_activity_date' => Carbon::today()]);
    }
}
