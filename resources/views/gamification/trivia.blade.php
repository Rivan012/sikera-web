@extends('layouts.admin')
@section('title', 'Trivia Harian')
@section('page-title', 'Kuis Harian Kesehatan Reproduksi')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5 rounded-full bg-warning-50 px-3 py-1 dark:bg-warning-500/10">
                <svg class="fill-warning-500" width="16" height="16" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/><path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                <span class="text-xs font-semibold text-warning-600 dark:text-warning-400">Streak: {{ $user->streak_days }} hari</span>
            </div>
            <div class="flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 dark:bg-brand-500/10">
                <span class="text-xs font-semibold text-brand-600 dark:text-brand-400">{{ $user->points }} poin</span>
            </div>
        </div>
    </div>

    @php $triviaResult = session('trivia_result', $result); @endphp

    @if($triviaResult && isset($triviaResult['answered']))
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
            @if(isset($triviaResult['correct']))
                <div class="text-center mb-6">
                    @if($triviaResult['correct'])
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-success-50 text-success-500 dark:bg-success-500/10">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    </div>
                    <h3 class="mt-3 text-lg font-bold text-success-600 dark:text-success-400">Jawaban Benar!</h3>
                    @else
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-error-50 text-error-500 dark:bg-error-500/10">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                    </div>
                    <h3 class="mt-3 text-lg font-bold text-error-600 dark:text-error-400">Jawaban Salah</h3>
                    <p class="text-sm text-gray-500 mt-1">Jawaban benar: {{ $triviaResult['correct_answer'] }}</p>
                    @endif
                    <p class="mt-2 text-sm text-brand-600 font-medium dark:text-brand-400">+{{ $triviaResult['points'] }} poin</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Penjelasan Ilmiah:</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $triviaResult['explanation'] }}</p>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $triviaResult['message'] ?? 'Anda sudah menyelesaikan trivia hari ini.' }}</p>
                </div>
            @endif
        </div>
    @elseif($question)
        <form method="POST" action="{{ route('gamification.trivia.submit') }}">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white leading-relaxed">{{ $question->question }}</h3>
                <div class="mt-5 space-y-3" x-data="{ selected: '' }">
                    @foreach($question->choices as $choice)
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-brand-300 dark:border-gray-700"
                        :class="selected === '{{ $choice }}' ? 'border-brand-500 bg-brand-50 dark:border-brand-400 dark:bg-brand-500/10' : ''">
                        <input type="radio" name="answer" value="{{ $choice }}" x-model="selected" required class="h-4 w-4 border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $choice }}</span>
                    </label>
                    @endforeach
                </div>
                <button type="submit" class="mt-6 w-full rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Kirim Jawaban</button>
            </div>
        </form>
    @else
        <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-400">Belum ada pertanyaan trivia tersedia.</p>
        </div>
    @endif
</div>
@endsection
