<x-layout title="Login">
    <section class="auth-section">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Login to your BM3 Review account</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-input @error('email') input-error-border @enderror"
                        required autofocus autocomplete="username">
                    @error('email')
                        <p class="input-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password"
                        class="form-input @error('password') input-error-border @enderror"
                        required autocomplete="current-password">
                    @error('password')
                        <p class="input-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group form-check-group">
                    <label class="form-check">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">Login</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
            </div>
        </div>
    </section>
</x-layout>
