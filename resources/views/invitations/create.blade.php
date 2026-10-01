<x-layout title="Create Invitation">
    <section class="section">
        <div class="container container-sm">
            <div class="notifications-page-header">
                <div>
                    <h1 class="page-title">➕ Create Invitation</h1>
                    <p class="page-subtitle">
                        @if(auth()->user()->isAdmin())
                            Invite a new teacher or student to join BM3 Review
                        @else
                            Invite a new student to join BM3 Review
                        @endif
                    </p>
                </div>
                <a href="{{ route('invitations.index') }}" class="btn btn-ghost btn-sm">← Back</a>
            </div>

            <form method="POST" action="{{ route('invitations.store') }}" class="card admin-form-card">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="form-input @error('name') input-error-border @enderror" required
                        placeholder="e.g. Ahmad Hidayat">
                    @error('name') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-input @error('email') input-error-border @enderror" required
                        placeholder="e.g. ahmad@bm3.sch.id">
                    @error('email') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select id="role" name="role" class="form-select @error('role') input-error-border @enderror" required>
                        @foreach($allowedRoles as $role)
                            <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                {{ $role === 'teacher' ? '🎓 Teacher' : '📚 Student (Siswa)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <p class="input-error">{{ $message }}</p> @enderror
                    <p class="form-hint">
                        @if(auth()->user()->isAdmin())
                            Admins can invite both teachers and students.
                        @else
                            Teachers can only invite students.
                        @endif
                    </p>
                </div>

                <div class="form-group">
                    <div class="invitation-info card" style="background: var(--bg-surface-alt); padding: 1rem; border-radius: var(--radius-md);">
                        <p class="text-sm text-muted">
                            📧 An invitation link will be generated. Share it with the invitee.<br>
                            ⏰ The invitation expires in <strong>7 days</strong>.<br>
                            🔒 The invitee will create their own password when accepting.
                        </p>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('invitations.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg">📩 Send Invitation</button>
                </div>
            </form>
        </div>
    </section>
</x-layout>
