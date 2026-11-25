<?php

namespace App\Http\Middleware;

use App\Services\EvaluationEligibilityService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'initials' => $user->initials,
                    'email' => $user->email,
                    'nim' => $user->nim,
                    'role' => $user->role,
                    'gender' => $user->gender,
                    'usia' => $user->usia,
                    'prodi' => $user->prodi,
                    'fakultas' => $user->fakultas,
                    'avatar' => $user->avatar,
                    'points' => (int) ($user->points ?? 0),
                    'streak_days' => (int) ($user->streak_days ?? 0),
                    'is_biodata_filled' => (bool) $user->is_biodata_filled,
                    'pretest_completed' => (bool) $user->pretest_completed,
                    'posttest_completed' => (bool) $user->posttest_completed,
                    'google_linked' => $user->google_id !== null,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'info' => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
                'error' => fn () => $request->session()->get('error'),
                'trivia_result' => fn () => $request->session()->get('trivia_result'),
                'myth_result' => fn () => $request->session()->get('myth_result'),
            ],
            'posttestGate' => fn () => ($user && $user->isMahasiswa())
                ? app(EvaluationEligibilityService::class)->syncAllModulesCompletion($user)
                : null,
        ];
    }
}
