<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\GoogleProvider;

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

        $initials = $request->initials ?: collect(explode(' ', $request->name))->map(fn ($part) => substr($part, 0, 1))->join('');

        $user = User::create([
            'name' => $request->name,
            'initials' => $initials,
            'email' => $request->email,
            'nim' => $request->nim ? strtoupper($request->nim) : ('MHS'.rand(1000, 9999)),
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
    public function login(Request $request): JsonResponse
    {
        if ($request->has('id_token')) {
            return $this->loginWithGoogle($request);
        }

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

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
            ], 401);
        }

        return $this->loginResponse($user);
    }

    private function loginWithGoogle(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'id_token' => ['bail', 'required', 'string', 'min:101', 'max:8192', 'regex:/\A[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\z/'],
        ]);

        try {
            $identity = $this->resolveGoogleProviderForToken($credentials['id_token'])
                ->userFromToken($credentials['id_token']);
        } catch (Exception) {
            return response()->json(['success' => false, 'message' => 'Token Google gagal diverifikasi. Silakan masuk kembali.'], 401);
        }

        $claims = $identity->getRaw();
        if (! is_string($identity->id) || $identity->id === '' || ! filter_var($identity->email, FILTER_VALIDATE_EMAIL)
            || ($claims['email_verified'] ?? false) !== true || ($claims['exp'] ?? 0) <= time()) {
            return response()->json(['success' => false, 'message' => 'Identitas Google tidak dapat diverifikasi.'], 401);
        }

        $user = User::where('google_id', $identity->id)->first();
        if ($user) {
            return $this->loginResponse($user);
        }

        if (! str_ends_with(strtolower($identity->email), '@gmail.com') && empty($claims['hd'])) {
            return response()->json(['success' => false, 'message' => 'Untuk alamat email ini, silakan masuk dengan email dan kata sandi SIKERA.'], 401);
        }

        $user = User::firstOrCreate(['email' => $identity->email], fn () => [
            'name' => $identity->name ?: $identity->email,
            'google_id' => $identity->id,
            'avatar' => $identity->avatar,
            'password' => Hash::make(Str::random(64)),
        ]);

        if ($user->google_id === null) {
            User::whereKey($user->id)->whereNull('google_id')->update([
                'google_id' => $identity->id,
                'avatar' => $identity->avatar,
            ]);
        }
        $user->refresh();

        if ($user->google_id !== $identity->id) {
            return response()->json(['success' => false, 'message' => 'Akun SIKERA ini sudah terhubung dengan akun Google lain.'], 401);
        }

        return $this->loginResponse($user);
    }

    private function resolveGoogleProviderForToken(string $idToken): AbstractProvider
    {
        $defaultClientId = trim((string) config('services.google.client_id', ''));
        $allowedClientIds = $this->allowedGoogleClientIds();
        $tokenAudience = $this->extractJwtAudience($idToken);

        if ($tokenAudience !== null
            && $tokenAudience !== $defaultClientId
            && in_array($tokenAudience, $allowedClientIds, true)) {
            return Socialite::buildProvider(GoogleProvider::class, [
                'client_id' => $tokenAudience,
                'client_secret' => '',
                'redirect' => '',
            ]);
        }

        return Socialite::driver('google');
    }

    /**
     * @return list<string>
     */
    private function allowedGoogleClientIds(): array
    {
        $configured = config('services.google.allowed_client_ids');
        $candidates = is_array($configured) ? $configured : [];

        foreach (['client_id', 'android_client_id', 'ios_client_id'] as $key) {
            $value = config("services.google.{$key}");
            if (is_string($value) && trim($value) !== '') {
                $candidates[] = trim($value);
            }
        }

        return array_values(array_unique(array_filter(
            array_map(static fn ($id) => is_string($id) ? trim($id) : '', $candidates),
            static fn (string $id) => $id !== '',
        )));
    }

    private function extractJwtAudience(string $idToken): ?string
    {
        $segments = explode('.', $idToken);
        if (count($segments) !== 3) {
            return null;
        }

        $payload = strtr($segments[1], '-_', '+/');
        $padding = strlen($payload) % 4;
        if ($padding > 0) {
            $payload .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($payload, true);
        if (! is_string($decoded)) {
            return null;
        }

        $claims = json_decode($decoded, true);
        if (! is_array($claims)) {
            return null;
        }

        $aud = $claims['aud'] ?? null;

        return is_string($aud) && trim($aud) !== '' ? trim($aud) : null;
    }

    private function loginResponse(User $user): JsonResponse
    {
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
