<x-layout :title="'Edit Review for ' . $teacher->name">
    <section class="section page-header-section">
        <div class="container">
            <a href="{{ route('teachers.show', $teacher) }}" class="back-link">← Back to {{ $teacher->name }}</a>
            <h1 class="page-title">Edit Your Review</h1>
            <p class="page-subtitle">Update your review for {{ $teacher->name }} ({{ $teacher->subject }})</p>
        </div>
    </section>

    <section class="section">
        <div class="container container-sm">
            <form method="POST" action="{{ route('reviews.update', $review) }}" class="review-form card">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <h3>Update Ratings</h3>
                    <x-rating-input name="overall_rating" label="Overall Rating" :value="old('overall_rating', $review->overall_rating)" />
                    <x-rating-input name="teaching_rating" label="Teaching Quality" :value="old('teaching_rating', $review->teaching_rating)" />
                    <x-rating-input name="explanation_rating" label="Explanation Clarity" :value="old('explanation_rating', $review->explanation_rating)" />
                    <x-rating-input name="fairness_rating" label="Fairness" :value="old('fairness_rating', $review->fairness_rating)" />
                    <x-rating-input name="workload_rating" label="Assignment Workload" :value="old('workload_rating', $review->workload_rating)" />
                </div>

                <div class="form-section">
                    <h3>Update Comment</h3>
                    <div class="form-group">
                        <textarea name="comment" rows="5" class="form-textarea @error('comment') input-error-border @enderror"
                            required minlength="10" maxlength="2000">{{ old('comment', $review->comment) }}</textarea>
                        @error('comment')
                            <p class="input-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg">Update Review</button>
                </div>
            </form>
        </div>
    </section>
</x-layout>
