<x-layout :title="$teacher->name">
    {{-- Teacher Profile Header --}}
    <section class="teacher-profile-header">
        <div class="container">
            <div class="profile-header-content">
                @php
                    $colors = ['#6C5CE7', '#A29BFE', '#00B894', '#00CEC9', '#E17055', '#FDCB6E', '#E84393', '#0984E3'];
                    $bgColor = $colors[$teacher->id % count($colors)];
                @endphp
                <div class="profile-avatar" style="background-color: {{ $bgColor }}">
                    @if($teacher->photo)
                        <img src="{{ Storage::url($teacher->photo) }}" alt="{{ $teacher->name }}">
                    @else
                        <span class="avatar-initials-lg">{{ $teacher->initials }}</span>
                    @endif
                </div>
                <div class="profile-info">
                    <h1 class="profile-name">{{ $teacher->name }}</h1>
                    <p class="profile-subject">{{ $teacher->subject }}</p>
                    @if($teacher->description)
                        <p class="profile-description">{{ $teacher->description }}</p>
                    @endif
                    <div class="profile-rating-summary">
                        <x-rating-stars :rating="$averages['overall']" size="lg" />
                        <span class="rating-big">{{ number_format($averages['overall'], 1) }} / 5</span>
                        <span class="rating-count">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="profile-content-grid">
                {{-- Left: Rating Breakdown --}}
                <div class="profile-sidebar">
                    <div class="card">
                        <h3 class="card-title">Rating Breakdown</h3>
                        <x-rating-breakdown :averages="$averages" />
                    </div>

                    {{-- Write Review Button --}}
                    @auth
                        @if($hasReviewed)
                            <div class="card card-info">
                                <p>✏️ You've already reviewed this teacher.</p>
                                <a href="{{ route('reviews.edit', $userReview) }}" class="btn btn-primary btn-block">Edit Your Review</a>
                            </div>
                        @else
                            <a href="{{ route('reviews.create', $teacher) }}" class="btn btn-primary btn-block btn-lg">
                                ✍️ Write a Review
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-block btn-lg">
                            Login to Write a Review
                        </a>
                    @endauth
                </div>

                {{-- Right: Reviews --}}
                <div class="profile-reviews">
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
                </div>
            </div>
        </div>
    </section>
</x-layout>
