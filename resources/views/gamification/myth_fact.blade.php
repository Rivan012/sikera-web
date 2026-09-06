@extends('layouts.admin')
@section('title', 'Mitos vs Fakta')
@section('page-title', 'Kartu Mitos vs Fakta')

@section('content')
<div class="max-w-2xl mx-auto">
    @php $mythResult = session('myth_result'); @endphp

    @if($mythResult)
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
        <div class="text-center mb-6">
            @if($mythResult['correct'])
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-success-50 text-success-500 dark:bg-success-500/10">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
            </div>
            <h3 class="mt-3 text-lg font-bold text-success-600 dark:text-success-400">Tepat!</h3>
            @else
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-error-50 text-error-500 dark:bg-error-500/10">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </div>
            <h3 class="mt-3 text-lg font-bold text-error-600 dark:text-error-400">Kurang Tepat</h3>
            @endif
            <p class="mt-1 text-sm text-gray-500">Pernyataan tersebut adalah: <strong>{{ $mythResult['is_fact'] ? 'FAKTA' : 'MITOS' }}</strong></p>
            <p class="mt-2 text-sm text-brand-600 font-medium dark:text-brand-400">+{{ $mythResult['points'] }} poin</p>
        </div>
        <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Penjelasan Ilmiah:</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $mythResult['explanation'] }}</p>
        </div>
    </div>
    @elseif($result && isset($result['answered']))
    <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-white/[0.03]">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $result['message'] }}</p>
    </div>
    @elseif($card)
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] sm:p-8" x-data="{ revealed: false }">
        <div class="text-center mb-4">
            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $card->category }}</span>
        </div>

        <div class="rounded-xl bg-gray-50 p-6 dark:bg-gray-800 mb-6">
            <p class="text-base font-medium text-gray-800 dark:text-white text-center leading-relaxed">"{{ $card->statement }}"</p>
        </div>

        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Menurut Anda, pernyataan di atas adalah:</p>

        <div class="grid grid-cols-2 gap-4">
            <form method="POST" action="{{ route('gamification.myth_fact.submit') }}">
                @csrf
                <input type="hidden" name="card_id" value="{{ $card->id }}">
                <input type="hidden" name="answer" value="fact">
                <button type="submit" class="w-full rounded-xl border-2 border-success-200 bg-success-50 px-6 py-4 text-center transition hover:border-success-500 hover:bg-success-100 dark:border-success-800 dark:bg-success-500/10 dark:hover:border-success-500">
                    <span class="text-lg font-bold text-success-600 dark:text-success-400">FAKTA</span>
                    <p class="text-xs text-success-500 mt-1">Pernyataan ini benar</p>
                </button>
            </form>

            <form method="POST" action="{{ route('gamification.myth_fact.submit') }}">
                @csrf
                <input type="hidden" name="card_id" value="{{ $card->id }}">
                <input type="hidden" name="answer" value="myth">
                <button type="submit" class="w-full rounded-xl border-2 border-error-200 bg-error-50 px-6 py-4 text-center transition hover:border-error-500 hover:bg-error-100 dark:border-error-800 dark:bg-error-500/10 dark:hover:border-error-500">
                    <span class="text-lg font-bold text-error-600 dark:text-error-400">MITOS</span>
                    <p class="text-xs text-error-500 mt-1">Pernyataan ini salah</p>
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-white/[0.03]">
        <p class="text-sm text-gray-400">Belum ada kartu Mitos/Fakta tersedia.</p>
    </div>
    @endif
</div>
@endsection
