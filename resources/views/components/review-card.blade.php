@props(['review', 'showTeacher' => false])

@php
    $user = $review->user;
    $votesCount = $review->votes_count ?? $review->votes()->count();
    $hasVoted = auth()->check() ? $review->hasVoteFrom(auth()->id()) : false;
    $isOwner = auth()->check() && auth()->id() === $review->user_id;
@endphp

<div class="review-card" id="review-{{ $review->id }}">
    <div class="review-header">
        <div class="review-user">
            <div class="review-avatar">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->username }}" loading="lazy">
                @else
                    <span>{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                @endif
            </div>
            <div class="review-user-info">
                <span class="review-username">{{ '@' . $user->username }}</span>
                <span class="review-date">{{ $review->created_at->format('M d, Y') }}</span>
            </div>
        </div>
        <div class="review-rating-badge">
            <x-rating-stars :rating="$review->overall_rating" size="sm" />
            <span>{{ $review->overall_rating }}/5</span>
        </div>
    </div>

    @if($showTeacher && $review->teacher)
        <div class="review-teacher-link">
            Reviewing: <a href="{{ route('teachers.show', $review->teacher) }}">{{ $review->teacher->name }}</a>
        </div>
    @endif

    <p class="review-comment">{{ $review->comment }}</p>

    <div class="review-details-grid">
        <span class="review-detail"><strong>Teaching:</strong> {{ $review->teaching_rating }}/5</span>
        <span class="review-detail"><strong>Explanation:</strong> {{ $review->explanation_rating }}/5</span>
        <span class="review-detail"><strong>Fairness:</strong> {{ $review->fairness_rating }}/5</span>
        <span class="review-detail"><strong>Workload:</strong> {{ $review->workload_rating }}/5</span>
    </div>

    <div class="review-actions">
        @auth
            <button class="btn-helpful {{ $hasVoted ? 'voted' : '' }}"
                onclick="toggleVote({{ $review->id }}, this)"
                {{ $isOwner ? 'disabled title=Cannot vote on your own review' : '' }}>
                👍 <span class="helpful-text">Helpful</span>
                <span class="helpful-count">{{ $votesCount }}</span>
            </button>

            @if(!$isOwner)
                <button class="btn-report" onclick="openReportModal({{ $review->id }})" aria-label="Report this review">
                    🚩 Report
                </button>
            @endif

            @if($isOwner)
                <a href="{{ route('reviews.edit', $review) }}" class="btn btn-ghost btn-sm">Edit</a>
                <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="inline-form"
                    onsubmit="return confirm('Are you sure you want to delete this review?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm btn-danger-text">Delete</button>
                </form>
            @endif
        @else
            <span class="helpful-display">👍 {{ $votesCount }} found this helpful</span>
        @endauth
    </div>
</div>
