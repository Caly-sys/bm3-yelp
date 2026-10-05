<h2 class="section-title">Reviews ({{ $reviewCount }})</h2>

@if($reviews->isEmpty())
    <div class="empty-state">
        <span class="empty-icon">📝</span>
        <h3>No reviews yet</h3>
        <p>Be the first to review this teacher!</p>
    </div>
@else
    @foreach($reviews as $review)
        <x-review-card :review="$review" />
    @endforeach

    <div class="pagination-wrapper">
        {{ $reviews->links() }}
    </div>
@endif
