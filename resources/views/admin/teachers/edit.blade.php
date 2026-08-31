<x-layout :title="'Edit ' . $teacher->name">
    <x-admin-nav :title="'Edit: ' . $teacher->name" subtitle="Update profile information and subjects for this teacher">
        <x-slot:actions>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-ghost btn-sm">
                ← Back to Teachers
            </a>
            <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-outline btn-sm" target="_blank">
                View Public Profile ↗
            </a>
        </x-slot:actions>
    </x-admin-nav>

    <section class="section admin-section">
        <div class="container container-sm">
            <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" enctype="multipart/form-data" class="card admin-form-card">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Teacher Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $teacher->name) }}"
                        class="form-input @error('name') input-error-border @enderror" required>
                    @error('name') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="subject" class="form-label">Subject / Mata Pelajaran <span class="text-danger">*</span></label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject', $teacher->subject) }}"
                        class="form-input @error('subject') input-error-border @enderror" required>
                    @error('subject') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Biography & Description</label>
                    <textarea id="description" name="description" rows="4" class="form-textarea">{{ old('description', $teacher->description) }}</textarea>
                    @error('description') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="photo" class="form-label">Profile Photo</label>
                    @if($teacher->photo)
                        <div class="admin-photo-preview mb-2">
                            <img src="{{ Storage::url($teacher->photo) }}" alt="{{ $teacher->name }}" class="admin-preview-img">
                            <span class="text-muted text-xs">Current photo uploaded. Choose new file to replace.</span>
                        </div>
                    @endif
                    <input type="file" id="photo" name="photo" class="form-input-file" accept="image/*">
                    @error('photo') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </section>
</x-layout>
