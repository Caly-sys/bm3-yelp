<x-layout title="Register">
    <section class="auth-section">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Join BM3 Review</h1>
                <p>Create your account to start reviewing teachers</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                        class="form-input @error('username') input-error-border @enderror"
                        required autofocus placeholder="e.g. student_123">
                    @error('username')
                        <p class="input-error">{{ $message }}</p>
                    @enderror
                    <p class="form-hint">Letters, numbers, dashes, and underscores only.</p>
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="form-input @error('name') input-error-border @enderror"
                        required>
                    @error('name')
                        <p class="input-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-input @error('email') input-error-border @enderror"
                        required>
                    @error('email')
                        <p class="input-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password"
                        class="form-input @error('password') input-error-border @enderror"
                        required autocomplete="new-password">
                    @error('password')
                        <p class="input-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="form-input" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </section>
</x-layout>
