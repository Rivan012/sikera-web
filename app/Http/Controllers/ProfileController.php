<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', ['user' => $request->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->saveProfile($request);

        return to_route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateApi(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->saveProfile($request);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => ['user' => $user],
        ]);
    }

    private function saveProfile(UpdateProfileRequest $request): User
    {
        $user = $request->user();
        $user->fill($request->safe()->except('current_password'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($user->isMahasiswa()) {
            $user->is_biodata_filled = true;
        }

        $user->save();

        return $user;
    }
}
