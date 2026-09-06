<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
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
}
