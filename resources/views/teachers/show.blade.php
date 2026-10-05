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
                        <span class="rating-big" id="overallRatingText">{{ number_format($averages['overall'], 1) }} / 5</span>
                        <span class="rating-count" id="reviewCountText">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</span>
                        <span class="rating-points" id="pointsText" style="margin-left: 1rem; padding: 0.2rem 0.6rem; background: var(--bg-surface-alt); border-radius: 1rem; font-weight: bold; font-size: 0.9rem;">
                            🎯 Points: {{ $teacher->points }}/100
                        </span>
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
                            <button class="btn btn-secondary btn-block btn-lg" disabled title="You have already submitted a review for this teacher">
                                ✍️ Write a Review (Already Reviewed)
                            </button>
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
                <div class="profile-reviews" id="reviewsContainer">
                    @include('teachers._reviews')
                </div>
            </div>
        </div>
    </section>

    <script>
        (function() {
            let isFetching = false;

            function fetchUpdates() {
                // If there's pagination in the URL, don't poll to avoid jumping back to page 1
                // or we could poll the current URL including query string.
                if (isFetching || window.location.search.includes('page=')) return;
                isFetching = true;

                fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    // Update reviews HTML
                    const container = document.getElementById('reviewsContainer');
                    if (container && data.html) {
                        // Check if html changed before replacing (prevents flashing)
                        if (container.innerHTML.trim() !== data.html.trim()) {
                            container.innerHTML = data.html;
                        }
                    }

                    // Update stats
                    const overallRatingEl = document.getElementById('overallRatingText');
                    if (overallRatingEl && data.overall_rating !== undefined) {
                        overallRatingEl.textContent = data.overall_rating + ' / 5';
                    }

                    const reviewCountEl = document.getElementById('reviewCountText');
                    if (reviewCountEl && data.reviewCount !== undefined) {
                        reviewCountEl.textContent = data.reviewCount + ' ' + (data.reviewCount === 1 ? 'review' : 'reviews');
                    }

                    const pointsEl = document.getElementById('pointsText');
                    if (pointsEl && data.points !== undefined) {
                        pointsEl.textContent = '🎯 Points: ' + data.points + '/100';
                    }
                })
                .catch(err => console.error('Error polling teacher updates:', err))
                .finally(() => { isFetching = false; });
            }

            // Listen for ReviewUpdated event via WebSockets
            if (window.Echo) {
                window.Echo.channel('teacher.{{ $teacher->id }}')
                    .listen('.ReviewUpdated', (e) => {
                        fetchUpdates();
                    });
            } else {
                console.warn('Laravel Echo is not defined. Falling back to polling.');
                setInterval(fetchUpdates, 5000);
            }
        })();
    </script>
</x-layout>
