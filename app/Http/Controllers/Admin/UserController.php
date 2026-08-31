<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('reviews');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'suspended') {
                $query->where('is_suspended', true);
            } elseif ($request->input('status') === 'active') {
                $query->where('is_suspended', false);
            }
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleSuspend(User $user)
    {
        // Don't allow suspending admins
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot suspend admin users.');
        }

        $user->update(['is_suspended' => !$user->is_suspended]);

        $action = $user->is_suspended ? 'suspended' : 'unsuspended';

        return back()->with('success', "User @{$user->username} has been {$action}.");
    }
}
