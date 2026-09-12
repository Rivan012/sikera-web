<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    private function authorizeAdmin(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman pengelolaan pengguna.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $roleFilter = $request->query('role', 'all');
        $searchQuery = $request->query('q');

        $usersQuery = User::query();

        if ($roleFilter && $roleFilter !== 'all') {
            $usersQuery->where('role', $roleFilter);
        }

        if ($searchQuery) {
            $usersQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('email', 'like', "%{$searchQuery}%")
                    ->orWhere('nim', 'like', "%{$searchQuery}%")
                    ->orWhere('initials', 'like', "%{$searchQuery}%");
            });
        }

        $users = $usersQuery->latest()->get();

        $totalAllUsers = User::count();
        $totalMhsUsers = User::where('role', 'mahasiswa')->count();
        $totalDosenUsers = User::where('role', 'dosen_pa')->count();
        $totalAdminUsers = User::where('role', 'admin')->count();

        return view('admin.users.index', compact(
            'users',
            'roleFilter',
            'searchQuery',
            'totalAllUsers',
            'totalMhsUsers',
            'totalDosenUsers',
            'totalAdminUsers'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initials' => 'nullable|string|max:50',
            'email' => 'required|string|email|max:255|unique:users,email',
            'nim' => 'nullable|string|max:50|unique:users,nim',
            'role' => 'required|in:mahasiswa,dosen_pa,admin',
            'usia' => 'nullable|integer|min:10|max:100',
            'gender' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:100',
            'prodi' => 'nullable|string|max:255',
            'fakultas' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'points' => 'nullable|integer|min:0',
            'is_biodata_filled' => 'nullable|boolean',
            'pretest_completed' => 'nullable|boolean',
            'posttest_completed' => 'nullable|boolean',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $initials = ! empty($validated['initials'])
            ? $validated['initials']
            : collect(explode(' ', $validated['name']))->map(fn ($part) => substr($part, 0, 1))->join('');

        User::create([
            'name' => $validated['name'],
            'initials' => strtoupper($initials),
            'email' => $validated['email'],
            'nim' => ! empty($validated['nim']) ? strtoupper($validated['nim']) : null,
            'role' => $validated['role'],
            'usia' => $validated['usia'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'agama' => $validated['agama'] ?? null,
            'prodi' => $validated['prodi'] ?? null,
            'fakultas' => $validated['fakultas'] ?? null,
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
            'points' => $validated['points'] ?? 0,
            'is_biodata_filled' => $request->boolean('is_biodata_filled'),
            'pretest_completed' => $request->boolean('pretest_completed'),
            'posttest_completed' => $request->boolean('posttest_completed'),
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$validated['name']} berhasil ditambahkan.");
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initials' => 'nullable|string|max:50',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nim' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:mahasiswa,dosen_pa,admin',
            'usia' => 'nullable|integer|min:10|max:100',
            'gender' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:100',
            'prodi' => 'nullable|string|max:255',
            'fakultas' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'points' => 'nullable|integer|min:0',
            'is_biodata_filled' => 'nullable|boolean',
            'pretest_completed' => 'nullable|boolean',
            'posttest_completed' => 'nullable|boolean',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $initials = ! empty($validated['initials'])
            ? $validated['initials']
            : ($user->initials ?: collect(explode(' ', $validated['name']))->map(fn ($part) => substr($part, 0, 1))->join(''));

        $user->name = $validated['name'];
        $user->initials = strtoupper($initials);
        $user->email = $validated['email'];
        $user->nim = ! empty($validated['nim']) ? strtoupper($validated['nim']) : null;
        $user->role = $validated['role'];
        $user->usia = $validated['usia'] ?? null;
        $user->gender = $validated['gender'] ?? null;
        $user->agama = $validated['agama'] ?? null;
        $user->prodi = $validated['prodi'] ?? null;
        $user->fakultas = $validated['fakultas'] ?? null;
        $user->pendidikan_terakhir = $validated['pendidikan_terakhir'] ?? null;
        $user->points = $validated['points'] ?? 0;
        $user->is_biodata_filled = $request->boolean('is_biodata_filled');
        $user->pretest_completed = $request->boolean('pretest_completed');
        $user->posttest_completed = $request->boolean('posttest_completed');

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.']);
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$userName} berhasil dihapus.");
    }
}
