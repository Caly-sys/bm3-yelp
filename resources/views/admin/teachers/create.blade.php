<x-layout title="Add Teacher">
    <x-admin-nav title="Add New Teacher" subtitle="Create a new teacher profile in the BM3 directory">
        <x-slot:actions>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-ghost btn-sm">
                ← Back to Teachers
            </a>
        </x-slot:actions>
    </x-admin-nav>

    <section class="section admin-section">
        <div class="container container-sm">
            <form method="POST" action="{{ route('admin.teachers.store') }}" enctype="multipart/form-data" class="card admin-form-card">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label">Teacher Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="form-input @error('name') input-error-border @enderror" required placeholder="e.g. Pak Ahmad Hidayat, S.Kom">
                    @error('name') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="subject" class="form-label">Subject / Mata Pelajaran <span class="text-danger">*</span></label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                        class="form-input @error('subject') input-error-border @enderror" required placeholder="e.g. Pemrograman Web, Desain Grafis">
                    @error('subject') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Biography & Description (optional)</label>
                    <textarea id="description" name="description" rows="4" class="form-textarea"
                        placeholder="Brief bio, teaching philosophy, or background notes...">{{ old('description') }}</textarea>
                    @error('description') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="photo" class="form-label">Profile Photo (optional)</label>
                    <input type="file" id="photo" name="photo" class="form-input-file" accept="image/*">
                    <p class="form-hint">Accepted formats: JPG, PNG, WEBP (Max 2MB)</p>
                    @error('photo') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg">➕ Create Teacher</button>
                </div>
            </form>
        </div>
    </section>
</x-layout>
