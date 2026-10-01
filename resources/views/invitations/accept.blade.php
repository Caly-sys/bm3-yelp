<x-layout title="Accept Invitation">
    <section class="section">
        <div class="container container-sm">
            <div class="card admin-form-card" style="margin-top: 2rem;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <h1 class="page-title">📩 You're Invited!</h1>
                    <p class="page-subtitle">
                        You've been invited to join BM3 Review as a
                        <strong>{{ $invitation->role === 'teacher' ? '🎓 Teacher' : '📚 Student' }}</strong>
                        by <strong>{{ '@' . $invitation->inviter->username }}</strong>
                    </p>
                </div>

                @if($existingUser)
                    <div class="invitation-info card" style="background: var(--bg-surface-alt); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                        <p class="text-sm">
                            ℹ️ An account with the email <strong>{{ $invitation->email }}</strong> already exists.
                            Please <a href="{{ route('login') }}">log in</a> to accept this invitation.
                        </p>
                    </div>
                @else
                    <p class="text-sm text-muted" style="margin-bottom: 1.5rem; text-align: center;">
                        Create your account to get started. Your name and email have been pre-filled.
                    </p>

                    <form method="POST" action="{{ route('invitations.accept.process', $invitation->token) }}">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-input" value="{{ $invitation->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" value="{{ $invitation->email }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-input" value="{{ ucfirst($invitation->role) }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}"
                                class="form-input @error('username') input-error-border @enderror" required
                                placeholder="Choose a username" autocomplete="username">
                            @error('username') <p class="input-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password"
                                class="form-input @error('password') input-error-border @enderror" required
                                placeholder="Choose a password" autocomplete="new-password">
                            @error('password') <p class="input-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-input" required placeholder="Confirm your password" autocomplete="new-password">
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('home') }}" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-lg">✅ Accept & Create Account</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>
</x-layout>
