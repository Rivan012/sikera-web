@extends('layouts.admin')
@section('title', 'Forum Anonim')
@section('page-title', 'Forum Tanya Jawab Anonim')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500 dark:text-gray-400">Ruang aman untuk bertanya seputar kesehatan reproduksi. Identitas Anda dirahasiakan.</p>
    <a href="{{ route('forum.create') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Buat Pertanyaan</a>
</div>

<div class="space-y-3">
    @forelse($threads as $thread)
    <a href="{{ route('forum.show', $thread->id) }}" class="group block rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-brand-300 hover:shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-500/50">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium
                        {{ $thread->topic === 'Darurat' ? 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400' :
                           ($thread->topic === 'Medis' ? 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400' :
                           ($thread->topic === 'Hubungan' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' :
                           'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400')) }}">{{ $thread->topic }}</span>
                    @if($thread->is_answered_by_counselor)
                    <span class="rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-400">Dijawab Konselor</span>
                    @endif
                </div>
                <h3 class="text-sm font-semibold text-gray-800 group-hover:text-brand-600 dark:text-white">{{ $thread->title }}</h3>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $thread->anonymous_alias }} &middot; {{ $thread->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex-shrink-0 flex items-center gap-1 text-gray-400 dark:text-gray-500">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                <span class="text-xs font-medium">{{ $thread->replies_count }}</span>
            </div>
        </div>
    </a>
    @empty
    <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-white/[0.03]">
        <p class="text-sm text-gray-400">Belum ada pertanyaan. Jadilah yang pertama bertanya secara anonim.</p>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $threads->links() }}</div>
@endsection
