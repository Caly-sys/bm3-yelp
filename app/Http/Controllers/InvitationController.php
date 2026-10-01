<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Notifications\AccountCreatedNotification;
use App\Notifications\InvitationAcceptedNotification;
use App\Notifications\InvitationReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class InvitationController extends Controller
{
    /**
     * Show the list of invitations sent by the current user (teacher or admin).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin() && !$user->isTeacher()) {
            abort(403, 'Unauthorized.');
        }

        $invitations = Invitation::where('invited_by', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        // Auto-expire pending invitations that have passed their expiry
        Invitation::where('invited_by', $user->id)
            ->expiredPending()
            ->update(['status' => 'expired']);

        return view('invitations.index', compact('invitations'));
    }

    /**
     * Show the form to create a new invitation.
     */
    public function create(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin() && !$user->isTeacher()) {
            abort(403, 'Unauthorized.');
        }

        // Teachers can only invite students; admins can invite students and teachers
        $allowedRoles = $user->isAdmin() ? ['student', 'teacher'] : ['student'];

        return view('invitations.create', compact('allowedRoles'));
    }

    /**
     * Store a new invitation.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin() && !$user->isTeacher()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:student,teacher'],
        ]);

        $role = $request->input('role');

        // RBAC: Teachers can ONLY invite students
        if ($user->isTeacher() && $role !== 'student') {
            abort(403, 'Teachers can only invite students.');
        }

        // Only admins can invite teachers
        if ($role === 'teacher' && !$user->isAdmin()) {
            abort(403, 'Only admins can invite teachers.');
        }

        $email = $request->input('email');

        // Check if email already has an account
        if (User::where('email', $email)->exists()) {
            return back()->withInput()->with('error', 'A user with this email already exists.');
        }

        // Check for duplicate pending invitation
        $existingInvitation = Invitation::where('email', $email)
            ->where('role', $role)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return back()->withInput()->with('error', 'A pending invitation already exists for this email and role.');
        }

        $invitation = Invitation::create([
            'token' => Invitation::generateToken(),
            'email' => $email,
            'name' => $request->input('name'),
            'role' => $role,
            'invited_by' => $user->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // If the user already has an account (different email check passed but same person?),
        // send them a notification. Otherwise the invitation link is what matters.
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $existingUser->notify(new InvitationReceivedNotification($invitation));
        }

        return redirect()->route('invitations.index')
            ->with('success', "Invitation sent to {$email} as {$role}!");
    }

    /**
     * Show the invitation acceptance form (public, token-based).
     */
    public function showAcceptForm(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        // Auto-expire
        if ($invitation->status === 'pending' && $invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
        }

        if ($invitation->status !== 'pending') {
            $statusMessage = match($invitation->status) {
                'accepted' => 'This invitation has already been used.',
                'expired' => 'This invitation has expired.',
                'cancelled' => 'This invitation has been cancelled.',
                default => 'This invitation is no longer valid.',
            };
            return view('invitations.invalid', ['message' => $statusMessage]);
        }

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return view('invitations.invalid', ['message' => 'This invitation has expired.']);
        }

        // Check if user already has an account with this email
        $existingUser = User::where('email', $invitation->email)->first();

        return view('invitations.accept', compact('invitation', 'existingUser'));
    }

    /**
     * Process invitation acceptance - create account.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return redirect()->route('home')->with('error', 'This invitation is no longer valid.');
        }

        // Check if email already has an account
        $existingUser = User::where('email', $invitation->email)->first();

        if ($existingUser) {
            // If user already exists, just mark invitation as accepted
            $invitation->markAsAccepted($existingUser);

            // Notify the inviter
            $inviter = $invitation->inviter;
            if ($inviter) {
                $inviter->notify(new InvitationAcceptedNotification($invitation, $existingUser));
            }

            return redirect()->route('home')
                ->with('success', 'Invitation accepted! You already have an account.');
        }

        // Validate new account fields
        $request->validate([
            'username' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the user with the invited role
        $user = User::create([
            'username' => $request->input('username'),
            'name' => $invitation->name,
            'email' => $invitation->email,
            'password' => Hash::make($request->input('password')),
            'role' => $invitation->role,
        ]);

        // Mark invitation as accepted
        $invitation->markAsAccepted($user);

        // Notify the inviter
        $inviter = $invitation->inviter;
        if ($inviter) {
            $inviter->notify(new InvitationAcceptedNotification($invitation, $user));
        }

        // Notify all admins about new account
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            // Don't double-notify the inviter if they're an admin
            if ($inviter && $admin->id === $inviter->id) {
                continue;
            }
            $admin->notify(new AccountCreatedNotification($user, $inviter));
        }

        // Log in the new user
        auth()->login($user);

        return redirect()->route('home')
            ->with('success', 'Welcome! Your account has been created successfully.');
    }

    /**
     * Cancel a pending invitation.
     */
    public function cancel(Request $request, Invitation $invitation)
    {
        $user = $request->user();

        // Only the inviter or an admin can cancel
        if ($invitation->invited_by !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation cannot be cancelled.');
        }

        $invitation->markAsCancelled();

        return back()->with('success', 'Invitation cancelled.');
    }
}
