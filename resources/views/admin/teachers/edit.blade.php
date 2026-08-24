<x-layout :title="'Edit ' . $teacher->name">
    <section class="section page-header-section">
        <div class="container">
            <a href="{{ route('admin.teachers.index') }}" class="back-link">← Back to Teachers</a>
            <h1 class="page-title">Edit {{ $teacher->name }}</h1>
        </div>
    </section>

    <section class="section">
        <div class="container container-sm">
            <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" enctype="multipart/form-data" class="card">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $teacher->name) }}"
                        class="form-input @error('name') input-error-border @enderror" required>
                    @error('name') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject', $teacher->subject) }}"
                        class="form-input @error('subject') input-error-border @enderror" required>
                    @error('subject') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="4" class="form-textarea">{{ old('description', $teacher->description) }}</textarea>
                    @error('description') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="photo" class="form-label">Photo</label>
                    @if($teacher->photo)
                        <p class="form-hint">Current photo uploaded. Upload new to replace.</p>
                    @endif
                    <input type="file" id="photo" name="photo" class="form-input-file" accept="image/*">
                    @error('photo') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-actions">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </section>
</x-layout>
