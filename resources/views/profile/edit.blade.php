<x-layout title="Edit Profile">
    <section class="section page-header-section">
        <div class="container">
            <a href="{{ route('profile.show') }}" class="back-link">← Back to Profile</a>
            <h1 class="page-title">Edit Profile</h1>
        </div>
    </section>

    <section class="section">
        <div class="container container-sm">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                        class="form-input @error('username') input-error-border @enderror" required>
                    @error('username')
                        <p class="input-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Display Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="form-input @error('name') input-error-border @enderror" required>
                    @error('name')
                        <p class="input-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="avatar" class="form-label">Avatar</label>
                    <input type="file" id="avatar" name="avatar" class="form-input-file" accept="image/*">
                    @error('avatar')
                        <p class="input-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('profile.show') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </section>
</x-layout>
