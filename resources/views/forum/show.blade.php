@extends('layouts.admin')
@section('title', $thread->title)
@section('page-title', 'Forum Diskusi')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('forum.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-brand-500 dark:text-gray-400">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Kembali ke Forum
    </a>

    {{-- Thread --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center gap-2 mb-3">
            <span class="rounded-full px-2 py-0.5 text-xs font-medium
                {{ $thread->topic === 'Darurat' ? 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400' :
                   ($thread->topic === 'Medis' ? 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400' :
                   ($thread->topic === 'Hubungan' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' :
                   'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400')) }}">{{ $thread->topic }}</span>
            @if($thread->is_answered_by_counselor)
            <span class="rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-400">Dijawab Konselor</span>
            @endif
        </div>
        <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $thread->title }}</h2>
        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $thread->anonymous_alias }} &middot; {{ $thread->created_at->diffForHumans() }}</p>
        <div class="mt-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $thread->content }}</div>
    </div>

    {{-- Replies --}}
    <div class="mt-6 space-y-3">
        @foreach($thread->replies as $reply)
        <div class="rounded-xl border {{ $reply->role_badge === 'dosen_pa' || $reply->role_badge === 'admin' ? 'border-brand-200 bg-brand-50/30 dark:border-brand-800 dark:bg-brand-500/5' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]' }} p-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-full {{ $reply->role_badge === 'dosen_pa' ? 'bg-brand-100 text-brand-600' : ($reply->role_badge === 'admin' ? 'bg-warning-100 text-warning-600' : 'bg-gray-100 text-gray-500') }} text-xs font-bold dark:bg-opacity-20">
                    {{ strtoupper(substr($reply->display_name, 0, 1)) }}
                </div>
                <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $reply->display_name }}</span>
                @if($reply->role_badge !== 'mahasiswa')
                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ $reply->role_badge === 'dosen_pa' ? 'Dosen PA' : 'Admin' }}</span>
                @endif
                <span class="text-xs text-gray-400 dark:text-gray-500">&middot; {{ $reply->created_at->diffForHumans() }}</span>
            </div>
            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $reply->reply_content }}</div>
        </div>
        @endforeach
    </div>

    {{-- Reply Form --}}
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-4">Tulis Balasan</h3>
        <form method="POST" action="{{ route('forum.reply', $thread->id) }}" class="space-y-4">
            @csrf
            <textarea name="reply_content" rows="3" required minlength="3"
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white"
                placeholder="Tulis balasan Anda..."></textarea>
            <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Kirim Balasan</button>
        </form>
    </div>
</div>
@endsection
