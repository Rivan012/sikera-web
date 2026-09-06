@extends('layouts.admin')
@section('title', 'Catatan Harian')
@section('page-title', 'Catatan Harian & Suasana Hati')

@section('content')
<div class="grid grid-cols-12 gap-6">
    {{-- Form --}}
    <div class="col-span-12 lg:col-span-5">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-5">Tulis Catatan Hari Ini</h3>
            <form method="POST" action="{{ route('gamification.diary.store') }}" class="space-y-4" x-data="{ mood: '' }">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Suasana Hati</label>
                    <div class="flex gap-3">
                        @php
                        $moods = [
                            'Senang' => ['color' => 'bg-success-100 border-success-300 text-success-700', 'active' => 'bg-success-500 border-success-500 text-white'],
                            'Tenang' => ['color' => 'bg-blue-light-100 border-blue-light-300 text-blue-light-700', 'active' => 'bg-blue-light-500 border-blue-light-500 text-white'],
                            'Cemas' => ['color' => 'bg-warning-100 border-warning-300 text-warning-700', 'active' => 'bg-warning-500 border-warning-500 text-white'],
                            'Lelah' => ['color' => 'bg-orange-100 border-orange-300 text-orange-700', 'active' => 'bg-orange-500 border-orange-500 text-white'],
                            'Sedih' => ['color' => 'bg-error-100 border-error-300 text-error-700', 'active' => 'bg-error-500 border-error-500 text-white'],
                        ];
                        @endphp
                        @foreach($moods as $label => $classes)
                        <label class="cursor-pointer">
                            <input type="radio" name="mood" value="{{ $label }}" x-model="mood" class="hidden" required>
                            <span class="inline-block rounded-lg border px-3 py-1.5 text-xs font-medium transition"
                                :class="mood === '{{ $label }}' ? '{{ $classes['active'] }}' : '{{ $classes['color'] }}'">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
                    <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                    <textarea name="encrypted_note" rows="4" required maxlength="1000"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white"
                        placeholder="Bagaimana perasaanmu hari ini? Tuliskan keluhan fisik atau suasana hati..."></textarea>
                </div>

                <button type="submit" class="w-full rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Simpan Catatan</button>
            </form>
        </div>
    </div>

    {{-- Entries List --}}
    <div class="col-span-12 lg:col-span-7">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-4">Riwayat Catatan</h3>
            @if($entries->count() > 0)
            <div class="space-y-3">
                @foreach($entries as $entry)
                @php
                $moodColors = [
                    'Senang' => 'bg-success-50 text-success-600 border-success-200',
                    'Tenang' => 'bg-blue-light-50 text-blue-light-600 border-blue-light-200',
                    'Cemas' => 'bg-warning-50 text-warning-600 border-warning-200',
                    'Lelah' => 'bg-orange-50 text-orange-600 border-orange-200',
                    'Sedih' => 'bg-error-50 text-error-600 border-error-200',
                ];
                $moodClass = $moodColors[$entry->mood] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                @endphp
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="rounded-full border px-2 py-0.5 text-xs font-medium {{ $moodClass }}">{{ $entry->mood }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $entry->entry_date->format('d M Y') }}</span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $entry->encrypted_note }}</p>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-8">Belum ada catatan harian. Mulai tulis catatan pertamamu hari ini.</p>
            @endif
        </div>
    </div>
</div>
@endsection
