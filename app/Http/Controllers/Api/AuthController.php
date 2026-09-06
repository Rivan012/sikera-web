<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Registrasi Akun Mahasiswa Baru
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi formulir pendaftaran gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $initials = $request->initials ?: collect(explode(' ', $request->name))->map(fn($part) => substr($part, 0, 1))->join('');

        $user = User::create([
            'name' => $request->name,
            'initials' => $initials,
            'email' => $request->email,
            'nim' => $request->nim ? strtoupper($request->nim) : ('MHS' . rand(1000, 9999)),
            'role' => 'mahasiswa',
            'usia' => $request->usia,
            'gender' => $request->gender,
            'agama' => $request->agama,
            'prodi' => $request->prodi,
            'fakultas' => $request->fakultas ?? 'Universitas Bengkulu',
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'is_biodata_filled' => true,
            'pretest_completed' => false,
            'posttest_completed' => false,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('flutter-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran akun mahasiswa berhasil!',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ],
        ], 201);
    }

    /**
     * Login Akun
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter login tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
            ], 401);
        }

        $token = $user->createToken('flutter-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ],
        ]);
    }

    /**
     * Profil User Saat Ini
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil.',
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    /**
     * Perbarui Biodata Mahasiswa
     */
    public function updateBiodata(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'initials' => 'nullable|string|max:50',
            'usia' => 'required|integer|min:15|max:40',
            'gender' => 'required|in:L,P',
            'agama' => 'required|string',
            'prodi' => 'required|string',
            'fakultas' => 'nullable|string',
            'pendidikan_terakhir' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data biodata tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update([
            'initials' => $request->initials ?: $user->initials,
            'usia' => $request->usia,
            'gender' => $request->gender,
            'agama' => $request->agama,
            'prodi' => $request->prodi,
            'fakultas' => $request->fakultas ?? $user->fakultas,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'is_biodata_filled' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Biodata berhasil diperbarui!',
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    /**
     * Logout & Revoke Token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Token telah dihapus.',
        ]);
    }
}
