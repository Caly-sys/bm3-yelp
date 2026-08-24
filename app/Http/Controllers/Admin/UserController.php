<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('reviews')
            ->orderByDesc('created_at')
            ->paginate(20);

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
