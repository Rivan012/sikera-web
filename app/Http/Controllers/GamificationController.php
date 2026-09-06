<?php

namespace App\Http\Controllers;

use App\Models\TriviaQuestion;
use App\Models\MythFactCard;
use App\Models\UserDailyActivity;
use App\Models\SecretDiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GamificationController extends Controller
{
    public function trivia()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $alreadyAnswered = UserDailyActivity::where('user_id', $user->id)
            ->where('activity_date', $today)
            ->where('activity_type', 'trivia')
            ->first();

        $question = null;
        $result = null;

        if ($alreadyAnswered) {
            $result = session('trivia_result', ['answered' => true, 'message' => 'Anda sudah menjawab trivia hari ini. Kembali besok untuk pertanyaan baru!']);
        } else {
            $question = TriviaQuestion::inRandomOrder()->first();
        }

        return view('gamification.trivia', compact('question', 'result', 'user'));
    }

    public function submitTrivia(Request $request)
    {
        $request->validate(['answer' => 'required|string', 'question_id' => 'required|integer']);

        $user = Auth::user();
        $today = Carbon::today();

        $already = UserDailyActivity::where('user_id', $user->id)
            ->where('activity_date', $today)
            ->where('activity_type', 'trivia')
            ->exists();

        if ($already) {
            return redirect()->route('gamification.trivia')->with('warning', 'Anda sudah menjawab trivia hari ini.');
        }

        $question = TriviaQuestion::findOrFail($request->question_id);
        $isCorrect = $request->answer === $question->answer_key;
        $points = $isCorrect ? 20 : 5;

        UserDailyActivity::create([
            'user_id' => $user->id,
            'activity_date' => $today,
            'activity_type' => 'trivia',
            'points_earned' => $points,
        ]);

        $user->increment('points', $points);
        $this->updateStreak($user);

        $result = [
            'answered' => true,
            'correct' => $isCorrect,
            'points' => $points,
            'explanation' => $question->scientific_explanation,
            'correct_answer' => $question->answer_key,
        ];

        return redirect()->route('gamification.trivia')->with('trivia_result', $result);
    }

    public function mythFact()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $alreadyAnswered = UserDailyActivity::where('user_id', $user->id)
            ->where('activity_date', $today)
            ->where('activity_type', 'myth_fact')
            ->first();

        $card = null;
        $result = null;

        if ($alreadyAnswered) {
            $result = ['answered' => true, 'message' => 'Anda sudah menjawab kartu Mitos/Fakta hari ini.'];
        } else {
            $card = MythFactCard::inRandomOrder()->first();
        }

        return view('gamification.myth_fact', compact('card', 'result', 'user'));
    }

    public function submitMythFact(Request $request)
    {
        $request->validate(['answer' => 'required|in:fact,myth', 'card_id' => 'required|integer']);

        $user = Auth::user();
        $today = Carbon::today();

        $already = UserDailyActivity::where('user_id', $user->id)
            ->where('activity_date', $today)
            ->where('activity_type', 'myth_fact')
            ->exists();

        if ($already) {
            return redirect()->route('gamification.myth_fact');
        }

        $card = MythFactCard::findOrFail($request->card_id);
        $userSaidFact = $request->answer === 'fact';
        $isCorrect = $userSaidFact === $card->is_fact;
        $points = $isCorrect ? 15 : 5;

        UserDailyActivity::create([
            'user_id' => $user->id,
            'activity_date' => $today,
            'activity_type' => 'myth_fact',
            'points_earned' => $points,
        ]);

        $user->increment('points', $points);
        $this->updateStreak($user);

        return redirect()->route('gamification.myth_fact')->with('myth_result', [
            'correct' => $isCorrect,
            'is_fact' => $card->is_fact,
            'explanation' => $card->scientific_fact,
            'points' => $points,
        ]);
    }

    public function diary()
    {
        $user = Auth::user();
        $entries = SecretDiary::where('user_id', $user->id)->latest('entry_date')->take(30)->get();
        return view('gamification.diary', compact('entries', 'user'));
    }

    public function storeDiary(Request $request)
    {
        $validated = $request->validate([
            'mood' => 'required|in:Senang,Cemas,Lelah,Sedih,Tenang',
            'encrypted_note' => 'required|string|max:1000',
            'entry_date' => 'required|date',
        ]);

        SecretDiary::create([
            'user_id' => Auth::id(),
            'mood' => $validated['mood'],
            'encrypted_note' => $validated['encrypted_note'],
            'entry_date' => $validated['entry_date'],
        ]);

        return redirect()->route('gamification.diary')->with('success', 'Catatan harian berhasil disimpan.');
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
