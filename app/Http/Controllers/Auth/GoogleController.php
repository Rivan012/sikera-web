<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GoogleController extends Controller
{
    // Redirect ke halaman login Google
    public function redirect(Request $request): RedirectResponse
    {
        $callback = parse_url((string) config('services.google.redirect'));

        if (is_array($callback) && isset($callback['scheme'], $callback['host'])) {
            $callbackOrigin = $callback['scheme'].'://'.$callback['host'];

            if (isset($callback['port'])) {
                $callbackOrigin .= ':'.$callback['port'];
            }

            if (strcasecmp($request->getSchemeAndHttpHost(), $callbackOrigin) !== 0) {
                return redirect()->to($callbackOrigin.route('google.start', [], false));
            }
        }

        return $this->start();
    }

    public function start(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    // Menerima callback dari Google
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return redirect()->route('login')->with(
                'warning',
                'Sesi login Google tidak valid atau kedaluwarsa. Silakan mulai ulang login.'
            );
        }

        // Cari user berdasarkan Google ID
        $user = User::where('google_id', $googleUser->id)
            ->first();

        // Jika belum ditemukan, cari berdasarkan email
        if (! $user) {
            $user = User::where('email', $googleUser->email)
                ->first();
        }

        // Jika belum ada, buat akun baru
        if (! $user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => bcrypt(Str::random(32)),
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
            ]);
        } else {
            // Hubungkan akun lama dengan Google
            $user->update([
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
            ]);
        }

        // Login pengguna ke Laravel
        Auth::login($user);

        // Regenerasi session untuk keamanan
        request()->session()->regenerate();

        return redirect('/dashboard');
    }
}
