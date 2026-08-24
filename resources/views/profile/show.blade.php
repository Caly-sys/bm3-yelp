<x-layout title="My Profile">
    <section class="section page-header-section">
        <div class="container">
            <h1 class="page-title">My Profile</h1>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="profile-content-grid">
                <div class="profile-sidebar">
                    <div class="card profile-card-user">
                        <div class="profile-user-avatar">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->username }}">
                            @else
                                <span class="avatar-initials-lg">{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                            @endif
                        </div>
                        <h2 class="profile-user-name">{{ $user->name }}</h2>
                        <p class="profile-user-handle">{{ '@' . $user->username }}</p>
                        <div class="profile-user-stats">
                            <div class="profile-stat">
                                <span class="profile-stat-number">{{ $user->reviews()->count() }}</span>
                                <span class="profile-stat-label">Reviews</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-number">{{ $helpfulVotes }}</span>
                                <span class="profile-stat-label">Helpful Votes</span>
                            </div>
                        </div>
                        <p class="profile-user-joined">Joined {{ $user->created_at->format('M Y') }}</p>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline btn-block">Edit Profile</a>
                    </div>
                </div>

                <div class="profile-reviews">
                    <h2 class="section-title">My Reviews ({{ $reviews->total() }})</h2>

                    @if($reviews->isEmpty())
                        <div class="empty-state">
                            <span class="empty-icon">📝</span>
                            <h3>No reviews yet</h3>
                            <p>Start by reviewing your teachers!</p>
                            <a href="{{ route('teachers.index') }}" class="btn btn-primary">Browse Teachers</a>
                        </div>
                    @else
                        @foreach($reviews as $review)
                            <x-review-card :review="$review" :showTeacher="true" />
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
