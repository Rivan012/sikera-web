@extends('layouts.auth')
@section('title', 'Post-Test Modul ' . $module->module_number)

@section('content')
<div class="w-full max-w-2xl">
    <div class="mb-6 text-center">
        <span class="rounded-full bg-success-500/10 px-3 py-1 text-xs font-bold text-success-600 dark:bg-success-500/20 dark:text-success-300">
            Evaluasi Pemahaman &bull; Modul {{ $module->module_number }}
        </span>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Post-Test Modul {{ $module->module_number }}</h1>
        <p class="text-xs sm:text-sm font-semibold text-brand-600 dark:text-brand-400 mt-1">{{ $module->title }}</p>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Selamat telah menyelesaikan seluruh materi Modul {{ $module->module_number }}. Jawab kuesioner Post-Test berikut untuk mengukur peningkatan pemahaman dan skor N-Gain Anda.</p>
        @if($pretest)
        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Skor Pre-Test Modul Ini: <span class="font-bold text-brand-600 dark:text-brand-400">{{ $pretest->total_score }}/100</span></p>
        @endif
    </div>

    @if($errors->any())
    <div class="mb-4 rounded-xl bg-error-50 p-4 text-xs font-semibold text-error-700 dark:bg-error-500/10 dark:text-error-400">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('modules.posttest.submit', $module->id) }}" x-data="{ currentStep: 0, totalSteps: {{ $questions->count() }}, answers: {} }">
        @csrf

        @foreach($questions as $index => $question)
        <div x-show="currentStep === {{ $index }}" x-transition class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Pertanyaan {{ $index + 1 }} dari {{ $questions->count() }}</span>
                <span class="rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-bold text-success-600 dark:bg-success-500/15 dark:text-success-400">Post-Test Modul {{ $module->module_number }}</span>
            </div>

            <div class="mb-2 h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                <div class="h-1.5 rounded-full bg-success-500 transition-all duration-300" style="width: {{ (($index + 1) / $questions->count()) * 100 }}%"></div>
            </div>

            <h3 class="mt-5 text-base font-semibold text-gray-800 dark:text-white leading-relaxed">{{ $question->question_text }}</h3>

            <div class="mt-5 space-y-3">
                @foreach($question->options as $option)
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-success-300 hover:bg-success-50/50 dark:border-gray-700 dark:hover:border-success-500/50 dark:hover:bg-success-500/5"
                    :class="answers['q_{{ $question->id }}'] === '{{ $option['label'] }}' ? 'border-success-500 bg-success-50 dark:border-success-400 dark:bg-success-500/10' : ''">
                    <input type="radio" name="q_{{ $question->id }}" value="{{ $option['label'] }}"
                        x-model="answers['q_{{ $question->id }}']"
                        class="mt-0.5 h-4 w-4 border-gray-300 text-success-500 focus:ring-success-500 dark:border-gray-600">
                    <div>
                        <span class="text-xs font-bold text-success-500 dark:text-success-400 mr-1">{{ $option['label'] }}.</span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $option['text'] }}</span>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-between">
                <button type="button" x-show="currentStep > 0" @click="currentStep--"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                    Sebelumnya
                </button>
                <div x-show="currentStep === 0"></div>

                <button type="button" x-show="currentStep < totalSteps - 1" @click="if(answers['q_{{ $question->id }}']) currentStep++"
                    class="rounded-lg bg-success-500 px-5 py-2 text-xs font-bold text-white hover:bg-success-600 disabled:opacity-50"
                    :disabled="!answers['q_{{ $question->id }}']">
                    Selanjutnya
                </button>

                @if($index === $questions->count() - 1)
                <button type="submit" x-show="currentStep === totalSteps - 1"
                    class="rounded-lg bg-success-500 px-5 py-2 text-xs font-bold text-white hover:bg-success-600 disabled:opacity-50"
                    :disabled="!answers['q_{{ $question->id }}']">
                    Kirim Jawaban Post-Test &amp; Simpan Skor
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </form>
</div>
@endsection
