<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isMahasiswa()) {
                if (!$user->is_biodata_filled) {
                    return redirect()->route('biodata.show');
                }
                return redirect()->route('mahasiswa.dashboard');
            } elseif ($user->isDosen()) {
                return redirect()->route('dosen.dashboard');
            } else {
                return redirect()->route('admin.dashboard');
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isMahasiswa()) {
                if (!$user->is_biodata_filled) {
                    return redirect()->route('biodata.show');
                }
                return redirect()->route('mahasiswa.dashboard');
            } elseif ($user->isDosen()) {
                return redirect()->route('dosen.dashboard');
            } else {
                return redirect()->route('admin.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initials' => 'nullable|string|max:50',
            'usia' => 'required|integer|min:15|max:40',
            'gender' => 'required|in:L,P',
            'agama' => 'required|string',
            'prodi' => 'required|string',
            'fakultas' => 'nullable|string',
            'pendidikan_terakhir' => 'required|string',
            'nim' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $initials = $validated['initials'] ?: collect(explode(' ', $validated['name']))->map(fn($part) => substr($part, 0, 1))->join('');

        $user = User::create([
            'name' => $validated['name'],
            'initials' => $initials,
            'email' => $validated['email'],
            'nim' => $validated['nim'] ? strtoupper($validated['nim']) : ('MHS' . rand(1000, 9999)),
            'role' => 'mahasiswa',
            'usia' => $validated['usia'],
            'gender' => $validated['gender'],
            'agama' => $validated['agama'],
            'prodi' => $validated['prodi'],
            'fakultas' => $validated['fakultas'] ?? 'Universitas Bengkulu',
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
            'is_biodata_filled' => true,
            'pretest_completed' => false,
            'posttest_completed' => false,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Akun berhasil dibuat! Selamat datang di Menu Utama SIKERA.');
    }

    public function showBiodata()
    {
        $user = Auth::user();
        if ($user->is_biodata_filled) {
            return redirect()->route('mahasiswa.dashboard');
        }
        return view('auth.biodata', compact('user'));
    }

    public function saveBiodata(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'initials' => 'nullable|string|max:50',
            'usia' => 'required|integer|min:15|max:40',
            'gender' => 'required|in:L,P',
            'agama' => 'required|string',
            'prodi' => 'required|string',
            'fakultas' => 'nullable|string',
            'pendidikan_terakhir' => 'required|string',
        ]);

        $user->update([
            'initials' => $validated['initials'] ?: $user->initials,
            'usia' => $validated['usia'],
            'gender' => $validated['gender'],
            'agama' => $validated['agama'],
            'prodi' => $validated['prodi'],
            'fakultas' => $validated['fakultas'] ?? $user->fakultas,
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
            'is_biodata_filled' => true,
        ]);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Biodata berhasil disimpan!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari akun.');
    }

    public function forceLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Sesi Anda telah diakhiri.');
    }
}
