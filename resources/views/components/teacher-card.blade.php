@props(['teacher', 'rating' => null, 'reviewCount' => null, 'rank' => null])

@php
    $avg = $rating ?? $teacher->reviews_avg_overall_rating ?? $teacher->averageRating();
    $count = $reviewCount ?? $teacher->reviews_count ?? $teacher->publishedReviewCount();
    $initials = $teacher->initials;
    $colors = ['#0096fa', '#00c8ff', '#ff4060', '#ffaa00', '#10b981', '#8b5cf6', '#06b6d4', '#f43f5e'];
    $colorIndex = $teacher->id % count($colors);
    $bgColor = $colors[$colorIndex];
@endphp

<a href="{{ route('teachers.show', $teacher) }}" class="teacher-card" aria-label="View {{ $teacher->name }}'s profile">
    @if($rank)
        <div class="ranking-badge rank-{{ $rank }}">
            @if($rank === 1) 🥇 #1
            @elseif($rank === 2) 🥈 #2
            @elseif($rank === 3) 🥉 #3
            @else #{{ $rank }}
            @endif
        </div>
    @endif

    <div class="teacher-card-avatar" style="background-color: {{ $bgColor }}">
        @if($teacher->photo)
            <img src="{{ Storage::url($teacher->photo) }}" alt="{{ $teacher->name }}" loading="lazy">
        @else
            <span class="avatar-initials">{{ $initials }}</span>
        @endif
    </div>
    <div class="teacher-card-body">
        <h3 class="teacher-card-name">{{ $teacher->name }}</h3>
        <p class="teacher-card-subject">#{{ $teacher->subject }}</p>
        <div class="teacher-card-rating">
            <x-rating-stars :rating="$avg" size="sm" />
            <span class="rating-number">{{ number_format($avg, 1) }} / 5</span>
        </div>
        <p class="teacher-card-reviews">{{ $count }} {{ Str::plural('review', $count) }}</p>
    </div>
    <div class="teacher-card-footer">
        <span class="view-profile">View Profile →</span>
    </div>
</a>
