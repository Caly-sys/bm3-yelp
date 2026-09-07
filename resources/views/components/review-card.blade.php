@props(['review', 'showTeacher' => false])

@php
    $user = $review->user;
    $votesCount = $review->votes_count ?? $review->votes()->count();
    $hasVoted = auth()->check() ? $review->hasVoteFrom(auth()->id()) : false;
    $isOwner = auth()->check() && auth()->id() === $review->user_id;
    $teacher = $review->teacher;
    $isLinkedTeacher = auth()->check() && $teacher && $teacher->user_id && $teacher->user_id === auth()->id();
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
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

    <p class="review-comment">{{ \App\Helpers\ProfanityFilter::filter($review->comment) }}</p>

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
            @endif

            @if($isAdmin)
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

    {{-- Teacher Response / Comments Section --}}
    @if($review->comments && $review->comments->count() > 0)
        <div class="teacher-responses">
            @foreach($review->comments as $comment)
                <div class="teacher-response {{ $comment->user && $comment->user->isTeacher() ? 'official' : '' }}">
                    <div class="response-header">
                        <div class="response-avatar">
                            @if($comment->user->avatar)
                                <img src="{{ Storage::url($comment->user->avatar) }}" alt="{{ $comment->user->username }}" loading="lazy">
                            @else
                                <span>{{ strtoupper(substr($comment->user->username, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="response-meta">
                            <span class="response-username">{{ '@' . $comment->user->username }}</span>
                            @if($comment->user->isTeacher())
                                <span class="response-badge">🎓 Official Teacher Response</span>
                            @elseif($comment->user->isAdmin())
                                <span class="response-badge admin-badge">🛡️ Admin</span>
                            @endif
                            <span class="response-date">{{ $comment->created_at->format('M d, Y \a\t g:i A') }}</span>
                        </div>
                    </div>
                    <p class="response-content">{{ $comment->content }}</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Teacher Reply Form --}}
    @auth
        @if($isLinkedTeacher || $isAdmin)
            <div class="teacher-reply-form">
                <form method="POST" action="{{ route('reviews.comments.store', $review) }}">
                    @csrf
                    <textarea name="content" rows="2" placeholder="Write your official response..." required minlength="2" maxlength="2000" class="form-textarea"></textarea>
                    <button type="submit" class="btn btn-primary btn-sm">Submit Response</button>
                </form>
            </div>
        @endif
    @endauth
</div>
