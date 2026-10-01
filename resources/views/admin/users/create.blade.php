<x-layout title="Create User">
    <x-admin-nav title="Create New User" subtitle="Create a new user account directly">
        <x-slot:actions>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">
                ← Back to Users
            </a>
        </x-slot:actions>
    </x-admin-nav>

    <section class="section admin-section">
        <div class="container container-sm">
            <form method="POST" action="{{ route('admin.users.store') }}" class="card admin-form-card">
                @csrf

                <div class="form-group">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                        class="form-input @error('username') input-error-border @enderror" required
                        placeholder="e.g. ahmad_siswa" autocomplete="off">
                    @error('username') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="form-input @error('name') input-error-border @enderror" required
                        placeholder="e.g. Ahmad Hidayat">
                    @error('name') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-input @error('email') input-error-border @enderror" required
                        placeholder="e.g. ahmad@bm3.sch.id" autocomplete="off">
                    @error('email') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select id="role" name="role" class="form-select @error('role') input-error-border @enderror" required>
                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>📚 Student (Siswa)</option>
                        <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>🎓 Teacher</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>⚡ Admin</option>
                    </select>
                    @error('role') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" id="password" name="password"
                        class="form-input @error('password') input-error-border @enderror" required
                        placeholder="Set a password" autocomplete="new-password">
                    @error('password') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="form-input" required placeholder="Confirm the password" autocomplete="new-password">
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg">👤 Create User</button>
                </div>
            </form>
        </div>
    </section>
</x-layout>
