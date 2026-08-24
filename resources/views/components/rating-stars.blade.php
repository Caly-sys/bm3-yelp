@props(['rating' => 0, 'size' => 'md'])

@php
    $rating = floatval($rating);
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.25;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
@endphp

<div class="rating-stars rating-stars-{{ $size }}" aria-label="{{ number_format($rating, 1) }} out of 5 stars" role="img">
    @for($i = 0; $i < $fullStars; $i++)
        <span class="star star-full" aria-hidden="true">★</span>
    @endfor
    @if($halfStar)
        <span class="star star-half" aria-hidden="true">★</span>
    @endif
    @for($i = 0; $i < $emptyStars; $i++)
        <span class="star star-empty" aria-hidden="true">★</span>
    @endfor
</div>
