<x-layout :title="'Review ' . $teacher->name">
    <section class="section page-header-section">
        <div class="container">
            <a href="{{ route('teachers.show', $teacher) }}" class="back-link">← Back to {{ $teacher->name }}</a>
            <h1 class="page-title">Write a Review</h1>
            <p class="page-subtitle">Share your experience with {{ $teacher->name }} ({{ $teacher->subject }})</p>
        </div>
    </section>

    <section class="section">
        <div class="container container-sm">
            <form method="POST" action="{{ route('reviews.store', $teacher) }}" class="review-form card">
                @csrf

                <div class="form-section">
                    <h3>Rate Your Experience</h3>
                    <p class="form-hint">Click on the stars to rate (1 = Poor, 5 = Excellent)</p>

                    <x-rating-input name="overall_rating" label="Overall Rating" :value="old('overall_rating', 0)" />
                    <x-rating-input name="teaching_rating" label="Teaching Quality" :value="old('teaching_rating', 0)" />
                    <x-rating-input name="explanation_rating" label="Explanation Clarity" :value="old('explanation_rating', 0)" />
                    <x-rating-input name="fairness_rating" label="Fairness" :value="old('fairness_rating', 0)" />
                    <x-rating-input name="workload_rating" label="Assignment Workload" :value="old('workload_rating', 0)" />
                </div>

                <div class="form-section">
                    <h3>Your Comment</h3>
                    <div class="form-group">
                        <textarea name="comment" rows="5" class="form-textarea @error('comment') input-error-border @enderror"
                            placeholder="Share your experience... What did you like? What could be improved? (minimum 10 characters)"
                            required minlength="10" maxlength="2000">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="input-error">{{ $message }}</p>
                        @enderror
                        <p class="form-hint char-counter">
                            <span id="charCount">0</span> / 2000 characters
                        </p>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg">Submit Review</button>
                </div>
            </form>
        </div>
    </section>
</x-layout>
