<?php

namespace App\Services;

use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\ModuleProgress;
use App\Models\User;

class EvaluationEligibilityService
{
    public const POSTTEST_DELAY_DAYS = 3;

    /**
     * Sinkronisasi status ketuntasan seluruh modul mahasiswa dan hitung jeda 3 hari Post-Test.
     *
     * @return array{
     *     state: string,
     *     total_modules: int,
     *     completed_modules: int,
     *     total_topics: int,
     *     completed_topics: int,
     *     all_modules_completed: bool,
     *     all_modules_completed_at: string|null,
     *     posttest_available_at: string|null,
     *     is_posttest_unlocked: bool,
     *     remaining_seconds: int,
     *     delay_days: int,
     *     has_completed_posttest: bool,
     *     latest_posttest: array{total_score: int, pre_score: float, n_gain_score: float|null, effectiveness_category: string, submitted_at: string|null}|null
     * }
     */
    public function syncAllModulesCompletion(User $user): array
    {
        $modules = EducationalModule::with(['topics:id,educational_module_id'])
            ->orderBy('module_number')
            ->get();

        $activeModules = $modules->filter(fn (EducationalModule $m): bool => $m->topics->isNotEmpty());
        $allTopicIds = $activeModules->flatMap(fn (EducationalModule $m) => $m->topics->pluck('id'))->values();

        $completedTopicIds = $allTopicIds->isEmpty()
            ? collect()
            : ModuleProgress::where('user_id', $user->id)
                ->whereIn('module_topic_id', $allTopicIds)
                ->where('is_completed', true)
                ->pluck('module_topic_id')
                ->flip();

        $completedModules = $activeModules->filter(function (EducationalModule $mod) use ($completedTopicIds): bool {
            return $mod->topics->every(fn ($topic): bool => $completedTopicIds->has($topic->id));
        })->count();

        $totalModules = $activeModules->count();
        $totalTopics = $allTopicIds->count();
        $completedTopics = $completedTopicIds->count();
        $allCompleted = $totalTopics > 0 && $completedTopics >= $totalTopics;

        $latestPosttestModel = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'post_test')
            ->whereNull('module_number')
            ->latest('submitted_at')
            ->first();

        $hasCompletedPosttest = (bool) $latestPosttestModel || (bool) $user->posttest_completed;

        if ($user->isMahasiswa()) {
            if ($allCompleted && $user->all_modules_completed_at === null) {
                $user->forceFill(['all_modules_completed_at' => now()])->save();
            } elseif (! $allCompleted && ! $hasCompletedPosttest && $user->all_modules_completed_at !== null) {
                $user->forceFill(['all_modules_completed_at' => null])->save();
            }
        }

        $completedAt = $user->all_modules_completed_at;
        $availableAt = $completedAt?->copy()->addDays(self::POSTTEST_DELAY_DAYS);

        $now = now();
        $remainingSeconds = ($allCompleted && $availableAt && $now->lessThan($availableAt))
            ? max(1, (int) ceil($now->diffInSeconds($availableAt, false)))
            : 0;

        $isUnlocked = $user->isAdmin()
            || $user->isDosen()
            || ($allCompleted && $availableAt !== null && $now->greaterThanOrEqualTo($availableAt));

        $state = 'locked_incomplete';
        if ($hasCompletedPosttest) {
            $state = 'completed';
        } elseif ($isUnlocked) {
            $state = 'ready';
        } elseif ($allCompleted) {
            $state = 'waiting_delay';
        }

        $preScore = $this->calculateAggregatePretestScore($user);
        $latestPosttest = null;
        if ($latestPosttestModel) {
            $nGain = $latestPosttestModel->n_gain_score ?? $this->calculateNGain($preScore, (float) $latestPosttestModel->total_score);
            $latestPosttest = [
                'total_score' => (int) $latestPosttestModel->total_score,
                'pre_score' => $preScore,
                'n_gain_score' => $nGain,
                'effectiveness_category' => $this->categorizeNGain($nGain),
                'submitted_at' => $latestPosttestModel->submitted_at?->toIso8601String(),
            ];
        }

        return [
            'state' => $state,
            'total_modules' => $totalModules,
            'completed_modules' => $completedModules,
            'total_topics' => $totalTopics,
            'completed_topics' => $completedTopics,
            'all_modules_completed' => $allCompleted,
            'all_modules_completed_at' => $completedAt?->toIso8601String(),
            'posttest_available_at' => $availableAt?->toIso8601String(),
            'is_posttest_unlocked' => $isUnlocked,
            'remaining_seconds' => $remainingSeconds,
            'delay_days' => self::POSTTEST_DELAY_DAYS,
            'has_completed_posttest' => $hasCompletedPosttest,
            'latest_posttest' => $latestPosttest,
        ];
    }

    /**
     * Hitung rata-rata skor Pre-Test keseluruhan dari Pre-Test tiap modul milik mahasiswa.
     */
    public function calculateAggregatePretestScore(User $user): float
    {
        $responses = EvaluationResponse::where('user_id', $user->id)
            ->where('type', 'pre_test')
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();

        if ($responses->isEmpty()) {
            return 0.0;
        }

        $perModuleLatest = $responses
            ->whereNotNull('module_number')
            ->unique('module_number');

        if ($perModuleLatest->isNotEmpty()) {
            return round((float) $perModuleLatest->avg('total_score'), 1);
        }

        $generalLatest = $responses->firstWhere('module_number', null);

        return $generalLatest ? round((float) $generalLatest->total_score, 1) : 0.0;
    }

    public function calculateNGain(float $preScore, float $postScore): float
    {
        if ((100.0 - $preScore) <= 0.0) {
            return $postScore >= 100.0 ? 1.0 : 0.0;
        }

        return round(($postScore - $preScore) / (100.0 - $preScore), 3);
    }

    public function categorizeNGain(?float $nGain): string
    {
        if ($nGain === null) {
            return 'Rendah';
        }

        if ($nGain >= 0.7) {
            return 'Tinggi';
        }

        if ($nGain >= 0.3) {
            return 'Sedang';
        }

        return 'Rendah';
    }
}
