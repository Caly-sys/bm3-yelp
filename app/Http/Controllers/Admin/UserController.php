<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountCreatedNotification;
use App\Notifications\UserSuspendedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a new user account (admin-only).
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:student,teacher,admin'],
        ]);

        $user = User::create([
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
        ]);

        // Notify all other admins
        $admins = User::where('role', 'admin')
            ->where('id', '!=', $request->user()->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new AccountCreatedNotification($user, $request->user()));
        }

        $roleName = ucfirst($request->input('role'));
        return redirect()->route('admin.users.index')
            ->with('success', "{$roleName} account @{$user->username} created successfully!");
    }

    public function toggleSuspend(User $user)
    {
        // Don't allow suspending admins
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot suspend admin users.');
        }

        $user->update(['is_suspended' => !$user->is_suspended]);

        // Notify the affected user
        $user->notify(new UserSuspendedNotification($user, $user->is_suspended));

        $action = $user->is_suspended ? 'suspended' : 'unsuspended';

        return back()->with('success', "User @{$user->username} has been {$action}.");
    }
}
